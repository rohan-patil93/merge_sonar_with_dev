<?php 
class crmuserClass extends userClass{
	function getCRMLeads($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$todaydate = date("Y-m-d");
		if($get['admin_type'] != 1){
			$originatercondition = " AND originator = '".$get['user_id']."'";
			$prooriginatercondition = " AND assigner_id = '".$get['user_id']."'";
			$feasibiltycondition = " AND fr.created_by = '".$get['user_id']."'";
			
		}
		if($get['dashboard'] == "Y"){
			$datecondition = " AND created_date BETWEEN '".$todaydate." 00:00:00' AND '".$todaydate." 23:59:59'";
			$othercondition  =" WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$crm_leadssql = "SELECT (SELECT COUNT(id) as cnt FROM crm_leads ".$othercondition.$datecondition.$originatercondition.") as today_crm_lead_cnt, (SELECT COUNT(id) as cnt FROM crm_contacts ".$othercondition.$datecondition.$originatercondition.") as today_crm_contacts_cnt, (SELECT COUNT(id) as cnt FROM crm_deals ".$othercondition.$datecondition.$originatercondition.") as today_crm_deals_cnt, (SELECT COUNT(id) as cnt FROM crm_leads ".$othercondition.$originatercondition.") as crm_lead_cnt, (SELECT COUNT(id) as cnt FROM crm_contacts ".$othercondition.$originatercondition.") as crm_contacts_cnt, (SELECT COUNT(id) as cnt FROM crm_companies ".$othercondition.$originatercondition.") as crm_companies_cnt, (SELECT COUNT(id) as cnt FROM crm_campaigns ".$othercondition.$originatercondition.") as crm_campaigns_cnt, (SELECT COUNT(id) as cnt FROM crm_deals ".$othercondition.$originatercondition.") as crm_deals_cnt, (SELECT COUNT(id) as cnt FROM projects ".$othercondition.$prooriginatercondition.") as projects_cnt";
			$crm_leadsres=$callConfig->getRow($crm_leadssql);
			$return['dashboard_sql'] = $crm_leadssql;
			$return['dashboard_cnt'] = $crm_leadsres;

			$crm_leadssql = "SELECT * FROM crm_leads WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0  ORDER BY created_date DESC limit 5";
			$crm_leadsres=$callConfig->getAllRows($crm_leadssql);
			$return['crm_leads'] = $crm_leadsres;

			$crm_leadsgraphsql = "SELECT count(id) as cnt, crm_status FROM crm_leads WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0 GROUP BY crm_status";
			$crm_leadsgraphres=$callConfig->getAllRows($crm_leadsgraphsql);
			$return['crm_leads_graph'] = $crm_leadsgraphres;

			$crm_leadssql = "SELECT * FROM crm_contacts WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0 ORDER BY created_date DESC limit 5";
			$crm_leadsres=$callConfig->getAllRows($crm_leadssql);
			$return['crm_contacts'] = $crm_leadsres;

			$crm_leadsgraphsql = "SELECT count(id) as cnt, crm_status FROM crm_contacts WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0 GROUP BY crm_status";
			$crm_leadsgraphres=$callConfig->getAllRows($crm_leadsgraphsql);
			$return['crm_contacts_graph'] = $crm_leadsgraphres;
			
			$crm_dealssql = "SELECT * FROM crm_deals WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0 ORDER BY created_date DESC limit 5";
			$crm_dealsres=$callConfig->getAllRows($crm_dealssql);
			$return['crm_deals'] = $crm_dealsres;
			
			$crm_leadsgraphsql = "SELECT count(id) as cnt, crm_status FROM crm_deals WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0 GROUP BY crm_status";
			$crm_leadsgraphres=$callConfig->getAllRows($crm_leadsgraphsql);
			$return['crm_deals_graph'] = $crm_leadsgraphres;

			$crm_companiessql = "SELECT id, company_name FROM crm_companies WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0";
			$crm_companiesres=$callConfig->getAllRows($crm_companiessql);
			$crm_companies = array();
			foreach($crm_companiesres as $crmlead){
				$crm_companies[$crmlead->id] = $crmlead->company_name;
			}
			$return['crm_companies_id_name'] = $crm_companies;


			$crm_lead_typessql = "SELECT id,type_name,type, icon FROM crm_activity_types WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$crm_lead_typesres=$callConfig->getAllRows($crm_lead_typessql);
			$return['crm_activity_types'] = $crm_lead_typesres;

			$crm_lead_detailssql = "SELECT id, transaction_table, transaction_id, lead_type, date, time, address, country_id, state_id, city_id, post_code, phone, sms_text, email_content_id, recipient_email_id, member, comment, created_date FROM crm_histories WHERE company_id = '".$get['company_id']."' AND lead_type IN(2,3,6,7,8) AND(originator = '".$get['user_id']."' OR JSON_SEARCH(member, 'one', '".$get['user_id']."') IS NOT NULL) AND  current_status = 0 AND is_active = 0";
			$crm_lead_detailsres=$callConfig->getAllRows($crm_lead_detailssql);
			$return['crm_pendding_tasks'] = $crm_lead_detailsres;
			$tasktabd = array();
			foreach($crm_lead_detailsres as $hist){
				$tasktabd[$hist->transaction_table][$hist->transaction_id] = $hist->transaction_id;
			}
			foreach($tasktabd as $transtable=>$ids){
				$crm_leadssql = "SELECT id,display_name,phone FROM ".$transtable." WHERE company_id = '".$get['company_id']."' AND id IN(".implode(',', (array)$ids).") ".$originatercondition." AND is_active = 0";
				$crm_leadsres=$callConfig->getAllRows($crm_leadssql);
				foreach($crm_leadsres as $crmdet){
					$return['crm_pendding_task_details'][$transtable][$crmdet->id] = $crmdet;
				}
				//$return['crm_pendding_task_details'][$transtable] = $ids;
			}
			
			$frsql =  "SELECT fr.id as feasibility_id, cc.id as crm_contact_id, cc.display_name, cc.email_id, cc.phone, cc.company_name, cc.crm_company_id FROM feasibility_requirements fr, crm_contacts cc WHERE fr.company_id = '".$get['company_id']."' AND fr.company_id = cc.company_id AND fr.lead_id = cc.id AND fr.is_active = cc.is_active  AND fr.is_active = 0 AND fr.sales_order_id IS NULL AND fr.current_status = '".ST_APPROVED."'".$feasibiltycondition;
			$frres=$callConfig->getAllRows($frsql);
			$return['feasibility_requirements'] = $frres;


			$query = "SELECT fr.id AS feasibility_id, fr.current_status, fr.lead_id AS lead_id, ft.type_name AS feasibility_type, fe.end_date AS evaluation_end_date, crmcon.display_name FROM feasibility_requirements fr LEFT JOIN feasibility_types ft ON fr.feasibility_type_id = ft.id AND ft.company_id = " . $get['company_id'] . " AND ft.is_active = 0 LEFT JOIN lead_feasibility_evaluations fe ON fr.id = fe.feasibility_id  AND fe.company_id = " . $get['company_id'] . " AND fe.is_active = 0 LEFT JOIN crm_contacts crmcon  ON fr.company_id  = crmcon.company_id AND fr.lead_id = crmcon.id WHERE fr.company_id = " . $get['company_id'] . " AND fr.current_status != " . ST_APPROVED ." AND fr.is_active = 0  " ;
        	$data11 = $callConfig->getAllRows($query);
        	$return['feasibility_requirements_expiry_days'] = $data11;


		}elseif($get['list'] == "Y"){
			$from_date = $this->dateconvertdbformat($get['from_date']);
			$to_date = $this->dateconvertdbformat($get['to_date']);
			$datecondition = " AND created_date BETWEEN '".$from_date." 00:00:00' AND '".$to_date." 23:59:59'";
			if($get['transaction_table_list']=="crm_leads"){
				$crm_leadssql = "SELECT * FROM crm_leads WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0".$datecondition;
				$crm_leadsres=$callConfig->getAllRows($crm_leadssql);
				$return['crm_leads'] = $crm_leadsres;

				$crm_leadssql1 = "SELECT count(id) as cnt, crm_status,  GROUP_CONCAT(CONCAT_WS('|', id,display_name, phone,image) SEPARATOR ',') AS group_values   FROM crm_leads WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0  GROUP BY crm_status";
				$crm_leadsres1=$callConfig->getAllRows($crm_leadssql1);
				$return['crm_leads_graph'] = $crm_leadsres1;


			}elseif($get['transaction_table_list']=="crm_contacts"){
				$crm_leadssql = "SELECT * FROM crm_contacts WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0".$datecondition;
				$crm_leadsres=$callConfig->getAllRows($crm_leadssql);
				$return['crm_contacts'] = $crm_leadsres;

				$feasibilitycrmcontactid = array();
				foreach($crm_leadsres as $crml){
					if($crml->feasibility_check == "Y"){
						$feasibilitycrmcontactid[$crml->id] = $crml->id;
					}
				}
				if(count((array)$feasibilitycrmcontactid) !="0"){
					$feasibility_requirementssql = "SELECT id, lead_id, sales_order_id, current_status FROM feasibility_requirements WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0".$datecondition;
					$feasibility_requirementsres=$callConfig->getAllRows($feasibility_requirementssql);
					foreach($feasibility_requirementsres as $feasl){
						$feasibility_requirements[$feasl->lead_id] = $feasl;
					}
					$return['feasibility_requirements'] = $feasibility_requirements;	
				}



				$crm_leadssql = "SELECT count(id) as cnt, crm_status, GROUP_CONCAT(CONCAT_WS('|', id,display_name, phone,image) SEPARATOR ',') AS group_values  FROM crm_contacts WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0  GROUP BY crm_status";
				$crm_leadsres=$callConfig->getAllRows($crm_leadssql);
				$return['crm_contacts_graph'] = $crm_leadsres;


			}elseif($get['transaction_table_list']=="crm_companies"){
				$crm_companiessql = "SELECT * FROM crm_companies WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0".$datecondition;
				$crm_companiesres=$callConfig->getAllRows($crm_companiessql);
				$return['crm_companies'] = $crm_companiesres;

				$crm_companiessql = "SELECT count(id) as cnt, crm_status, GROUP_CONCAT(CONCAT_WS('|', id,company_name, company_registration_number,image) SEPARATOR ',') AS group_values  FROM crm_companies WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0 GROUP BY crm_status";
				$crm_companiesres=$callConfig->getAllRows($crm_companiessql);
				$return['crm_companies_graph'] = $crm_companiesres;

			}elseif($get['transaction_table_list']=="crm_campaigns"){
				$crm_campaignssql = "SELECT * FROM crm_campaigns WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0".$datecondition;
				$crm_campaignsres=$callConfig->getAllRows($crm_campaignssql);
				$return['crm_campaigns'] = $crm_campaignsres;

				$crm_campaignssql = "SELECT count(id) as cnt, crm_status, GROUP_CONCAT(CONCAT_WS('|', id,campaign_name, total_budget,image) SEPARATOR ',') AS group_values  FROM crm_campaigns WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0 GROUP BY crm_status";
				$crm_campaignsres=$callConfig->getAllRows($crm_campaignssql);
				$return['crm_campaigns_graph'] = $crm_campaignsres;
			}elseif($get['transaction_table_list']=="crm_deals"){
				$crm_dealssql = "SELECT * FROM crm_deals WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0".$datecondition;
				$crm_dealsres=$callConfig->getAllRows($crm_dealssql);
				$return['crm_deals'] = $crm_dealsres;

				$crm_dealssql = "SELECT count(id) as cnt, crm_status, GROUP_CONCAT(CONCAT_WS('|', id,deal_name, total_budget,image) SEPARATOR ',') AS group_values FROM crm_deals WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0 GROUP BY crm_status";
				$crm_dealsres=$callConfig->getAllRows($crm_dealssql);
				$return['crm_deals_graph'] = $crm_dealsres;
			}elseif($get['transaction_table_list']=="crm_projects"){
				$crm_dealssql = "SELECT * FROM projects WHERE company_id = '".$get['company_id']."' AND is_active = 0".$datecondition;
				$crm_dealsres=$callConfig->getAllRows($crm_dealssql);
				$return['crm_projects'] = $crm_dealsres;
			}elseif($get['transaction_table_list']=="crm_feasibilities"){
				$query = "SELECT fr.id AS feasibility_id,fr.lead_id AS lead_id,ft.type_name AS feasibility_type,
					crm_con.display_name,fr.current_status,fr.sales_order_id,crm_con.phone,fr.updated_date
				FROM feasibility_requirements fr
				LEFT JOIN feasibility_types ft  ON fr.feasibility_type_id = ft.id AND ft.company_id = fr.company_id AND  ft.is_active = 0
				LEFT JOIN crm_contacts crm_con ON fr.lead_id = crm_con.id AND crm_con.company_id = fr.company_id AND  crm_con.is_active = 0
				WHERE fr.company_id = " .$get['company_id']." AND fr.created_date BETWEEN '".$from_date." 00:00:00' AND '".$to_date." 23:59:59' AND fr.is_active = 0";
			// $return['crm_feasibilities_query'] = $query;
			$crm_feasibilities = $callConfig->getAllRows($query);
			$return['crm_feasibilities'] = $crm_feasibilities;
			
		}
		// $crmldid = array();
			// foreach($crm_leadsres as $crmlead){
			// 	$crmldid[$crmlead->id] = $crmlead->id;
			// }

			// $lastcrmleadssql = "SELECT id, transaction_id, lead_type, date, time, address, country_id, state_id, city_id, post_code, phone, sms_text, email_content_id, recipient_email_id, member, comment, max(updated_date) as max_updated_date FROM crm_lead_details WHERE company_id = '".$get['company_id']."' AND transaction_id IN(".implode(',', $crmldid).") AND is_active = 0 GROUP BY transaction_id,updated_date ORDER BY max_updated_date ASC";
			// $lastcrmleadsres=$callConfig->getAllRows($lastcrmleadssql);
			// $return['lastactivity_crm_lead_details'] = $lastcrmleadsres;
			
			$todaycrmleadssql = "SELECT crmp.id, crmp.transaction_table, crmp.transaction_id, crmp.lead_type, crmp.date, crmp.time, crmp.address, crmp.country_id, crmp.state_id, crmp.city_id, crmp.post_code, crmp.phone, crmp.sms_text, crmp.email_content_id, crmp.recipient_email_id, crmp.member, crmp.comment, crmp.created_date FROM crm_histories crmp WHERE crmp.company_id = '".$get['company_id']."' AND crmp.is_active = 0 AND crmp.id IN(SELECT MAX(id) as id FROM crm_histories crmc WHERE crmc.company_id = crmp.company_id AND crmc.is_active = crmp.is_active AND crmc.transaction_table = crmp.transaction_table AND crmc.transaction_id = crmp.transaction_id GROUP BY crmc.transaction_table, crmc.transaction_id)";
			$todaycrmleadsres=$callConfig->getAllRows($todaycrmleadssql);
			$return['crm_histories'] = $todaycrmleadsres;


			$crm_companiessql = "SELECT id, company_name FROM crm_companies WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$crm_companiesres=$callConfig->getAllRows($crm_companiessql);
			$crm_companies = array();
			foreach($crm_companiesres as $crmlead){
				$crm_companies[$crmlead->id] = $crmlead->company_name;
			}
			$return['crm_companies_id_name'] = $crm_companies;


		

		}elseif($get['rid'] !=0 AND $get['rid'] !="" AND $get['transaction_table'] !=""){
			//* requird please don't add filed list
			$crm_leadssql = "SELECT * FROM ".$get['transaction_table']." WHERE company_id = '".$get['company_id']."' AND id = '".$get['rid']."' AND is_active = 0";
			$crm_leadsres=$callConfig->getRow($crm_leadssql);
			$return['transaction'] = $crm_leadsres;
			if($crm_leadsres->country_id !=""){
				$statessql = "SELECT id,state_name FROM states WHERE company_id = '".$get['company_id']."' AND country_id = '".$crm_leadsres->country_id."' AND is_active = 0";
				$statesres=$callConfig->getAllRows($statessql);
				$return['states'] = $statesres;
			}
			if($crm_leadsres->state_id !=""){
				$citiessql = "SELECT id,city_name FROM cities WHERE company_id = '".$get['company_id']."' AND state_id = '".$crm_leadsres->state_id."' AND is_active = 0";
				$citiesres=$callConfig->getAllRows($citiessql);
				$return['cities'] = $citiesres;
			}
			$crm_lead_detailssql = "SELECT id, transaction_id, lead_type, date, time, address, country_id, state_id, city_id, post_code, phone, sms_text, email_content_id, recipient_email_id, member, comment, created_date FROM crm_histories WHERE company_id = '".$get['company_id']."' AND transaction_id = '".$get['rid']."' AND transaction_table = '".$get['transaction_table']."' AND is_active = 0";
			$crm_lead_detailsres=$callConfig->getAllRows($crm_lead_detailssql);
			$return['crm_lead_details_sql'] = $crm_lead_detailssql;
			$return['crm_lead_details'] = $crm_lead_detailsres;

			$email_contentsql = "SELECT template_reference_id,template_name FROM email_content WHERE company_id = '".$get['company_id']."' AND application_module = '29' AND template_reference_id = 73 AND is_active = 0";
			$email_contentres=$callConfig->getAllRows($email_contentsql);
			$return['email_content'] = $email_contentres;

			if($get['transaction_table'] == "crm_companies"){ //only use for company with contacts
				$crm_leadssql1 = "SELECT * FROM crm_contacts WHERE company_id = '".$get['company_id']."' AND crm_company_id = '".$get['rid']."' AND is_active = 0";
				$crm_leadsres1=$callConfig->getAllRows($crm_leadssql1);
				$return['company_with_contacts'] = $crm_leadsres1;

				$company_with_dealssql = "SELECT * FROM crm_deals WHERE company_id = '".$get['company_id']."' AND crm_company_id = '".$get['rid']."' AND is_active = 0";
				$company_with_dealsres=$callConfig->getAllRows($company_with_dealssql);
				$return['company_with_deals'] = $company_with_dealsres;

			}elseif($get['transaction_table'] == "crm_deals"){ //only use for company with contacts
				$crm_contactssql = "SELECT id, display_name, phone, originator FROM crm_contacts WHERE company_id = '".$get['company_id']."' AND crm_company_id = '".$crm_leadsres->crm_company_id."' AND is_active = 0";
				$crm_contactsres=$callConfig->getAllRows($crm_contactssql);
				$return['deal_with_contacts'] = $crm_contactsres;
			}elseif($get['transaction_table'] == "projects"){ //only use for company with contacts
				$projctteamsql = "SELECT id, user_id FROM `project_teams` WHERE company_id = '".$get['company_id']."' AND project_id = '".$crm_leadsres->id."' AND is_active = 0";
				$projctteamres=$callConfig->getAllRows($projctteamsql);
				$return['project_teams'] = $projctteamres;
			}

			$application_userssql = "SELECT id,display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND JSON_SEARCH(role_id, 'one', 'CRM_USER') IS NOT NULL";
			$application_usersres=$callConfig->getAllRows($application_userssql);
			$return['application_users_role'] = $application_usersres;

		}
		$crm_general_codessql = "SELECT id, code, value, label FROM crm_general_codes WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$crm_general_codesres=$callConfig->getAllRows($crm_general_codessql);
		foreach($crm_general_codesres as $crmcode){
			$crm_general_codes[$crmcode->code][] =$crmcode; 
		}
		$return['crm_general_codes'] = $crm_general_codes;

		$application_userssql = "SELECT id,display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$application_usersres=$callConfig->getAllRows($application_userssql);
		$return['application_users'] = $application_usersres;

		$countriessql = "SELECT id,country_name FROM countries WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$countriesres=$callConfig->getAllRows($countriessql);
		$return['countries'] = $countriesres;

		$crm_lead_typessql = "SELECT id,type_name,type, icon FROM crm_activity_types WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$crm_lead_typesres=$callConfig->getAllRows($crm_lead_typessql);
		$return['crm_lead_types'] = $crm_lead_typesres;

		$company_typessql = "select id,company_type_name FROM company_types WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$company_typesres = $callConfig->getAllRows($company_typessql);
		$return['company_types'] = $company_typesres;	

		$project_task_statussql = "SELECT  id, table_name, name, status, action_label FROM project_task_status WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$project_task_statusres = $callConfig->getAllRows($project_task_statussql);
		$project_task_status = array();
		foreach($project_task_statusres as $aulres){
			$project_task_status[$aulres->table_name][$aulres->status]= $aulres;
		}
		$return['project_task_status'] = $project_task_status;	
		//echo "ascsdcvsvsfvsfvsfv".$get['transaction_table']; exit;
		if($get['transaction_table'] =="crm_companies"){
			$crm_lead_detailssql = "SELECT id, display_name FROM crm_contacts WHERE company_id = '".$get['company_id']."' AND (crm_company_id IN ('".$get['rid']."') OR crm_company_id IS NULL) AND is_active = 0";
			$crm_lead_detailsres=$callConfig->getAllRows($crm_lead_detailssql);
			$return['company_crm_contacts'] = $crm_lead_detailsres;

			$crm_all_pending_contactsql = "SELECT id, display_name FROM crm_contacts WHERE company_id = '".$get['company_id']."' AND (crm_company_id ='' OR crm_company_id IS NULL) AND is_active = 0";
			$crm_all_pending_contactres=$callConfig->getAllRows($crm_all_pending_contactsql);
			$return['without_company_contacts'] = $crm_all_pending_contactres;

		}elseif($get['transaction_table'] =="crm_deals" OR $get['transaction_table'] =="crm_contacts"){
			$crm_companiessql = "SELECT id, company_name FROM crm_companies WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$crm_companiesres=$callConfig->getAllRows($crm_companiessql);
			$return['crm_companies'] = $crm_companiesres;
		}elseif($get['transaction_table'] =="projects" OR $get['transaction_table'] =="crm_projects"){
			$customerssql = "SELECT id, display_name, company FROM customers WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$customersres=$callConfig->getAllRows($customerssql);
			$return['customers'] = $customersres;
		}
		if($get['transaction_table'] =="crm_leads" OR $get['transaction_table'] =="crm_contacts"){
			$crm_campaignssql = "SELECT id, campaign_name FROM crm_campaigns WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$crm_campaignsres=$callConfig->getAllRows($crm_campaignssql);
			$return['crm_campaigns'] = $crm_campaignsres;
		}
		return $return;
	}
	function insertCRMLeads($get,$post){
		global $callConfig;
		$return = array();
		$return['post'] = $post;
		$transaction_table = $post['transaction_table'];
		$display_name = $post['first_name']." ".$post['last_name'];
		$insertary=array('company_id'=>$post['company_id'],'first_name'=>$post['first_name'],'middle_name'=>$post['middle_name'],'last_name'=>$post['last_name'],'display_name'=>$display_name,'phone'=>$post['phone'],'office_phone'=>$post['office_phone'],'fax'=>$post['fax'],'website'=>$post['website'],'address'=>$post['address'],'post_code'=>$post['post_code'],'country_id'=>$post['country_id'],'state_id'=>$post['state_id'],'city_id'=>$post['city_id'],'originator'=>$post['originator'],'contact_source'=>$post['contact_source'],'remark'=>$post['remark'],'email_id'=>$post['email_id'],'image'=>$post['image']);
		if($transaction_table== "crm_leads"){
			$insertary['company_name'] = $post['company_name'];
			$insertary['crm_status'] = $post['crm_status'];
			$insertary['crm_campaign_id'] = $post['crm_campaign_id'];

		}elseif($transaction_table== "crm_contacts"){
			$insertary['crm_company_id'] = $post['crm_company_id'];
			$insertary['crm_campaign_id'] = $post['crm_campaign_id'];
			$insertary['feasibility_check'] = $post['feasibility_check'] =="Y"?"Y":"N";		
		}elseif($transaction_table =="crm_companies"){
			//$insertary=array('company_id'=>$post['company_id'],'company_name'=>$post['company_name'],'business_size'=>$post['business_size'],'business_type'=>$post['business_type'],'established_year'=>$post['established_year'],'tin_number'=>$post['tin_number'],'vat_number'=>$post['vat_number'],'website_url'=>$post['website_url'],'pan_no'=>$post['pan_no'],'company_trading_name'=>$post['company_trading_name'],'company_registration_number'=>$post['company_registration_number'],'tan_number'=>$post['tan_number'],'address'=>$post['address'],'post_code'=>$post['post_code'],'country_id'=>$post['country_id'],'state_id'=>$post['state_id'],'city_id'=>$post['city_id'],'originator'=>$post['originator'],'remark'=>$post['remark'],'image'=>$post['image']);

			$insertary=array('company_id'=>$post['company_id'],'company_name'=>$post['company_name'],'website_url'=>$post['website_url'],'pan_no'=>$post['pan_no'],'gstin'=>$post['gstin'],'fax'=>$post['fax'],'address'=>$post['address'],'post_code'=>$post['post_code'],'country_id'=>$post['country_id'],'state_id'=>$post['state_id'],'city_id'=>$post['city_id'],'originator'=>$post['originator'],'remark'=>$post['remark'],'image'=>$post['image']);

				$insertary1=array('company_id'=>$post['company_id'],'display_name'=>$post['company_name'],'company'=>$post['company_name'],'website_url'=>$post['website_url'],'pan_no'=>$post['pan_no'],'billing_address'=>$post['address'], 'shipping_address'=>$post['address'],'billing_post_code'=>$post['post_code'], 'shipping_post_code'=>$post['post_code'], 'billing_country'=>$post['country_id'], 'shipping_country'=>$post['country_id'], 'billing_state'=>$post['state_id'], 'shipping_state'=>$post['state_id'], 'billing_city'=>$post['city_id'], 'shipping_city'=>$post['city_id'],'image'=>$post['image'],'updated_by'=>$post['user_id'],'updated_date'=>date("Y-m-d H:i:s"),'gstin'=>$post['gstin'],'fax'=>$post['fax']);

				if($post['crm_customer_id'] !=""){
					$return['customer'] = $callConfig->updateRecord('customers',$insertary1,'id',$post['crm_customer_id']);
				}else{
					$customerinsert =$callConfig->insertRecord('customers',$insertary1);
					$return['customer'] = $customerinsert;
					$insertary['customer_id'] = $customerinsert['LASTID'];
				}
				

		}elseif($transaction_table =="crm_campaigns"){
			$insertary=array('company_id'=>$post['company_id'],'campaign_name'=>$post['campaign_name'],'crm_campaign_type'=>$post['crm_campaign_type'],'campaign_objective'=>$post['campaign_objective'],'campaign_start_date'=>$this->dateconvertdbformat($post['campaign_start_date']),'campaign_end_date'=>$this->dateconvertdbformat($post['campaign_end_date']),'total_budget'=>$post['total_budget'],'redirect_url'=>$post['redirect_url'],'contact_email_id'=>$post['contact_email_id'],'contact_no'=>$post['contact_no'],'originator'=>$post['originator'],'remark'=>$post['remark'],'image'=>$post['image']);
		}elseif($transaction_table =="crm_deals"){
			$insertary=array('company_id'=>$post['company_id'],'deal_name'=>$post['deal_name'],'source_type'=>$post['source_type'],'crm_company_id'=>$post['crm_company_id'],'crm_contact_id'=>$post['crm_contact_id'],'total_budget'=>$post['total_budget'],'due_date'=>$this->dateconvertdbformat($post['due_date']),'expected_close_date'=>$this->dateconvertdbformat($post['expected_close_date']),'originator'=>$post['originator'],'remark'=>$post['remark'],'image'=>$post['image']);
		}elseif($transaction_table =="projects"){
			$insertary=array('company_id'=>$post['company_id'],'project_name'=>$post['project_name'],'customer_id'=>$post['customer_id'],'project_cost'=>$post['project_cost'],'planned_start'=>$this->dateconvertdbformat($post['planned_start']),'planned_finish'=>$this->dateconvertdbformat($post['planned_finish']),'assigner_id'=>$post['originator'],'description'=>$post['description'],'assigner_id'=>$post['originator'],'project_logo'=>$post['image']);
		}
 		if($post['rid'] !="" AND $post['rid'] !="0"){
			$insertary['updated_date']=date('Y-m-d h:i:s');
			$insertary['updated_by']=$post['user_id'];
			$return['res'] = $callConfig->updateRecord($transaction_table,$insertary,'id',$post['rid']);
			$lastid = $post['rid'];	
			
			if($transaction_table == "projects"){
				$projctteamsql = "SELECT id, user_id FROM `project_teams` WHERE company_id = '".$get['company_id']."' AND project_id = '".$lastid."' AND is_active = 0";
				$projctteamres=$callConfig->getAllRows($projctteamsql);
				$project_teamsuserid = array();
				foreach($projctteamres as $prteam){
					$project_teamsuserid[$prteam->user_id] = $prteam->id; 
				}
				$callConfig->updateRecord('project_teams',array('is_active'=>null),'id',$lastid);
				if(count((array)$post['project_team_user_id']) != '0'){
					foreach($post['project_team_user_id'] as $userid){
						if($project_teamsuserid[$userid] !=""){
							$insertary1=array('company_id'=>$post['company_id'],'project_id'=>$lastid,'user_id'=>$userid,'is_active'=>'0','created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"));
							$return['project_teams_up'][] = $callConfig->updateRecord('project_teams',$insertary1,'id',$project_teamsuserid[$userid]);
						}else{
							$insertary1=array('company_id'=>$post['company_id'],'project_id'=>$lastid,'user_id'=>$userid,'is_active'=>'0','created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"));
							$return['project_teams'][] = $callConfig->insertRecord('project_teams',$insertary1);
						}
					}
				}
			}

		}else{
			//$insertary['lead_code'] = $this->toGetApplicationSerialNumber('crm_contacts');
			$return['insertary---'] = $insertary;
			$insertary['created_by']=$post['user_id'];
			$insertary['created_date']=date("Y-m-d H:i:s");
			$insertary['is_active']='0';
			$insertary['current_status']='0';
			$tapres = $callConfig->insertRecord($transaction_table,$insertary);
			$return['res'] =$tapres;
			$lastid = $tapres['LASTID'];	
			
			if($tapres['status'] == 'sucess' AND $transaction_table == "projects"){
				if(count((array)$post['project_team_user_id']) != '0'){
					foreach($post['project_team_user_id'] as $userid){
						$insertary1=array('company_id'=>$post['company_id'],'project_id'=>$lastid,'user_id'=>$userid,'is_active'=>'0','created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"));
						$return['project_teams'][] = $callConfig->insertRecord('project_teams',$insertary1);
					}
				}
			}
		}

		return $return;
	}
	function insertCRMLeadDetails($get,$post){
		global $callConfig; 
		$return = array(); 
		if($post['rid'] !=""){ // updatre status
			$insertary1=array('company_id'=>$post['company_id'],'completion_datetime'=>date("Y-m-d H:i:s"),'completion_note'=>$post['comment'],'current_status'=>ST_APPROVED,'updated_by'=>$post['user_id'],'updated_date'=>date("Y-m-d H:i:s"));
			$crmleaddetails = $callConfig->updateRecord('crm_histories',$insertary1,'id',$post['rid']);
		}else{
	 		$insertary=array('company_id'=>$post['company_id'],'transaction_id'=>$post['transaction_id'],'transaction_table'=>$post['transaction_table'],'lead_type'=>$post['lead_type'],'date'=>$this->dateconvertdbformat($post['date']),'time'=>$post['time'],'address'=>$post['address'],'country_id'=>$post['country_id'],'state_id'=>$post['state_id'],'city_id'=>$post['city_id'],'post_code'=>$post['post_code'],'email_id'=>$post['email_id'],'phone'=>$post['phone'],'sms_text'=>$post['sms_text'],'email_content_id'=>$post['email_content_id'],'recipient_email_id'=>$post['recipient_email_id'],'member'=>json_encode((array)$post['member']),'comment'=>$post['comment'],'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"),'originator'=>$post['user_id']);
			$crmleaddetails = $callConfig->insertRecord('crm_histories',$insertary);
		}
		if($crmleaddetails['status'] == "sucess"){
			if($post['lead_type'] == '6' || $post['lead_type'] == '7'){
				$aulist = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND id IN(".implode(',',(array)$post['member']).") AND is_active = 0";
				$aulres=$callConfig->getAllRows($aulist);
				$assign_user_list = "";
				foreach($aulres as $aulres){
					$assign_user_list .= $aulres->display_name.", ";
				}
			}
			$touserarray = $post['member'];
			$ccuserarray[] = $post['user_id'];
			if($post['lead_type'] == '6'){
				$senddata = array('session_company_id'=>$get['company_id'],'template_reference_id'=>'28','to_users'=>$touserarray,'cc_users'=>$ccuserarray);
				$senddata['lead_date_time'] = $this->dateconvertdbformat($post['date'])." ".$post['time'];
				$senddata['lead_Task'] = $post['comment'];
				$senddata['assign_user_list'] = $assign_user_list;
				$res = $this->emailsendmediaternew($senddata);
				
				$msg = "New Task Assign For You :".$post['comment'];
				$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$post['transaction_id'],'application_table_name'=>'crm_histories', 'type'=>'ACTION', 'notification_users'=>$touserarray,'option'=>'COM_VIEW_CRM_DASHBOARDS','assigner_id'=>$post['user_id']);
				$sendnotification = $this->tosendnotifiations($notdataa);
				$notdataa['notification_message'] = "New Task Assign Successfully";
				$notdataa['notification_users'] = $ccuserarray;
				$notdataa['type'] = "INFORMATION";
				$sendnotification = $this->tosendnotifiations($notdataa);

			}
			if($post['lead_type'] == '7'){
				$senddata = array('session_company_id'=>$get['company_id'],'template_reference_id'=>'29','to_users'=>$touserarray,'cc_users'=>$ccuserarray);
				$senddata['lead_date_time'] = $this->dateconvertdbformat($post['date'])." ".$post['time'];
				$senddata['lead_Task'] = $post['comment'];
				$senddata['assign_user_list'] = $assign_user_list;
				$res = $this->emailsendmediaternew($senddata);

				$msg = "New Meeting Assign For You :".$post['comment'];
				$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$post['transaction_id'],'application_table_name'=>'crm_histories', 'type'=>'ACTION', 'notification_users'=>$touserarray,'option'=>'COM_VIEW_CRM_DASHBOARDS','assigner_id'=>$post['user_id']);
				$sendnotification = $this->tosendnotifiations($notdataa);
				$notdataa['notification_message'] = "New Meeting Assign Successfully";
				$notdataa['notification_users'] = $ccuserarray;
				$notdataa['type'] = "INFORMATION";
				$sendnotification = $this->tosendnotifiations($notdataa);
			}
			if($post['lead_type'] == '5'){
				$senddata = array('session_company_id'=>$get['company_id'],'template_reference_id'=>$post['email_content_id'],'to_users'=>$ccuserarray,'cc_users'=>array(),'recipient_email_id'=>array($post['recipient_email_id']));
				$res = $this->emailsendmediaternew($senddata);
				//$return['senddata'] = $res;
				$msg = "Email Send Successfully";
				$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$post['transaction_id'],'application_table_name'=>'crm_histories', 'type'=>'INFORMATION', 'notification_users'=>$ccuserarray,'option'=>'COM_VIEW_CRM_DASHBOARDS','assigner_id'=>$post['user_id']);
				$sendnotification = $this->tosendnotifiations($notdataa);
			}
		}
		$return['res'] = $crmleaddetails;
		return $return;
	}
	function dynamicUpdateStatus($get,$post){
		global $callConfig;
		$return = array();
		$return['post'] = $post;
		$updateary['updated_date']=date('Y-m-d h:i:s');
		$updateary['updated_by']=$post['user_id'];
		$updateary['current_status'] = $post['current_status'];
		$return['res'] = $callConfig->updateRecord($post['transaction_table'],$updateary,'id',$post['rid']);
		return $return;
	}



//Start Email New Code 

	function emailsendmediaternew($senddata){
		global $callConfig;
		$senddata['super_admin_email_id'] = SUPERADMIN_EMAIL_ID;
		$senddata['super_admin_company_name'] = SUPERADMIN_COMPNAY_NAME;
		$senddata['super_admin_display_name'] = SUPERADMIN_DISLAY_NAME;
		$adminsersql = "SELECT au.id, au.display_name, au.user_email, au.user_mobile, comp.name FROM application_users au, companies comp WHERE au.company_id = '".$senddata['session_company_id']."' AND au.company_id = comp.id AND au.role_id LIKE '".'%"'.ADMIN_ROLES.'"%'."'";
		$adminserres = $callConfig->getRow($adminsersql);
		$senddata['admin_email_sql'] = $adminsersql;
		$senddata['admin_email_id'] = $adminserres->user_email;
		$senddata['admin_company_name'] = $adminserres->name;
		$senddata['company_name'] = $adminserres->name;
		$senddata['admin_display_name'] = $adminserres->display_name;
		$senddata['admin_phone_number'] = $adminserres->user_mobile;

		$email_send_types = array('TO'=>'to_users','CC'=>'cc_users');
		$query = "SELECT id,user_email FROM application_users";
		$emaildata = $callConfig->getAllRows($query);
		foreach($emaildata as $emaild){
			$useremail[$emaild->id] = $emaild->user_email;
		}
		foreach($email_send_types as $key=>$email_send_type){
			$revicer = array();
			$senddata['email_send_type'] =$key;
			$email_array = $this->emailtemplatenew($senddata);
			$file_name = 'email'.'_'.time().uniqid().'.'.'html';
			$email_array ['content'] = $file_name;
			if(count((array)$senddata[$email_send_type]) !='0'){
				foreach($senddata[$email_send_type] as $email_send){
					$revicer[] = $useremail[$email_send];
				}
				$email_array['revicer'] = (count((array)$senddata['recipient_email_id'])!='0')?$senddata['recipient_email_id']:$revicer;
				$email_array['email_type'] = 1;
				$email = $email_array['mesg'];
				$file_path='uploads/email/'.$file_name;
				file_put_contents($file_path, $email);
				$lenn = $this->sendEmailslistnew($email_array);
				$ress[] = $email_array;
			}
		}
		return $ress;
	} 
	function sendEmailslistnew($data){
		global $callConfig;
		$email_data = array('email_source'=>$data['sender'], 'content'=>$data['content'], 'email_type'=>$data['email_type'], 'is_active'=>0 , 'current_status'=>0,'email_content_ref_id'=>$data['email_content_ref_id'], 'created_by'=>$data['created_by'] ,'created_date'=> date('Y-m-d H:i:s'));   
		foreach ($data['revicer'] as $email) {
			$email_data['email'] = $email;
			$email_res=$callConfig->insertRecord(TBL_EMAIL_LOGS,$email_data);
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			// More headers
			$sender = $data['sender']!="" ? $data['sender']:SUPERADMIN_EMAIL_ID;
			$headers .= 'From:'.$sender . "\r\n";
			$subject = ucwords(str_replace('_', ' ', $data['subject']));
			mail($email,$subject,$data['mesg'],$headers);
		}
		//echo "email send sucess";
		return $email_data;
	} 	
	function emailtemplatenew($send_data){
		global $callConfig;
		$temp = "";
		$configsql = "SELECT name,contact_address,application_module,admin_email,display_logo FROM config WHERE id = 1";
		$return['config'] = $configsql;
		$configres=$callConfig->getRow($configsql);
		$send_data['company_name'] = $configres->name!=""?$configres->name:SUPERADMIN_COMPNAY_NAME;
		$send_data['contact_address'] = $configres->contact_address ;
		if($send_data['email_send_type'] == "TO"){
			$emailcontentsql = "SELECT email_subject_to as email_subject,email_header_to as email_header,email_content_to as email_content_1,button_label, email_footer  as email_fotter FROM email_content WHERE company_id = '".$send_data['session_company_id']."' AND  template_reference_id = '".$send_data['template_reference_id'] ."' AND is_active = 0 ";
		}else{
			$emailcontentsql = "SELECT email_subject_cc as email_subject,email_header_cc as email_header,email_content_cc as email_content_1,button_label,email_footer  as email_fotter FROM email_content WHERE company_id = '".$send_data['session_company_id']."' AND  template_reference_id = '".$send_data['template_reference_id'] ."' AND is_active = 0";
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

//End Email New Code

	function setStateCity($get){
		global $callConfig;
		$citiessql = "SELECT id,city_name FROM cities";
		$citiesres=$callConfig->getAllRows($citiessql);
		foreach($citiesres as $citt){
			$cityNames[$citt->id] = $citt->city_name;
		}

		$state_namesql = "SELECT id,state_name FROM states";
		$state_nameres=$callConfig->getAllRows($state_namesql);
		foreach($state_nameres as $citt){
			$stateNames[$citt->id] = $citt->state_name;
		}

		$customersssql = "SELECT id,billing_address FROM customers WHERE billing_address !=''";
		$customerssres=$callConfig->getAllRows($customersssql);
		foreach($customerssres as $cust){
			$customers[$cust->id]['billing_address'] = $cust->billing_address;

			//Statrt Get Post Code
			preg_match('/\b\d{6}\b/', $cust->billing_address, $postalCodeMatch);
			$postalCode = $postalCodeMatch[0] ?? null;
			$address = trim(str_replace($postalCode, '', $cust->billing_address));
			$customers[$cust->id]['post_code'] = $postalCode;			
			//End Get Post Code
			
			// $words = preg_split('/[\s,]+/', $cust->billing_address);
		 //    foreach ($words as $word) {
		 //        if (in_array(ucfirst(strtolower($word)), $stateNames, true)) {
		 //            $customers[$cust->id]['state_name'] =  ucfirst(strtolower($word));
		 //        }
		 //    }

		 //    foreach ($stateNames as $index => $city) {
			//     // Check if the city exists in the input string (case-insensitive)
			//     if (stripos($cust->billing_address, $city) !== false) {
			//         $cityPosition = $index; // Get the index of the matched city
			//         $customers[$cust->id]['cityNames2'] =  ucfirst(strtolower($city));
			//     }
			// }


			// foreach($citiesres as $citt){
		 //        if (stripos($cust->billing_address, $citt->city_name) !== false) {
		 //            $customers[$cust->id]['city_id'] = $citt->id;			
		 //        	$customers[$cust->id]['city_city_name'] = $citt->city_name;	
		 //        }
		 //    }
			$updateary = array('billing_city'=>'','billing_state'=>'','billing_post_code'=>$postalCode,'billing_country'=>'101','shipping_city'=>'','shipping_state'=>'','shipping_post_code'=>$postalCode,'shipping_country'=>'101');
			$return['res'] = $callConfig->updateRecord('customers',$updateary,'id',$cust->id);
		}
		echo "<pre>";
		print_r($return);

	}
	function leadConvertToContacts($get,$post){
		global $callConfig;
		$transaction_id = $post['transaction_id'];
		$exicutesql = "INSERT INTO `crm_contacts`(`crm_lead_id`,`company_name`,`company_id`, `first_name`, `middle_name`, `last_name`, `display_name`, `email_id`, `phone`, `office_phone`, `fax`, `website`, `address`, `post_code`, `country_id`, `state_id`, `city_id`, `originator`, `contact_source`, `is_active`, `current_status`, `created_date`, `created_by`,`image`) SELECT `id`,`company_name`,'".$post['company_id']."', `first_name`, `middle_name`, `last_name`, `display_name`, `email_id`, `phone`, `office_phone`, `fax`, `website`, `address`, `post_code`, `country_id`, `state_id`, `city_id`, `originator`, `contact_source`, 0, 0,'".date('Y-m-d H:i:s')."', '".$post['user_id']."',`image` FROM `crm_leads` WHERE id='".$transaction_id."'";
		$res = $callConfig->executeQuery($exicutesql);
		//$res['exicutesql'] = $exicutesql;
		if($res['status'] == 'sucess'){
			$insertary['updated_date']=date('Y-m-d h:i:s');
			$insertary['updated_by']=$post['user_id'];
			$insertary['lead_convert_to_contact']="Y";
			$insertary['crm_contact_id']=$transaction_id;
			$callConfig->updateRecord("crm_leads",$insertary,'id',$transaction_id);
		}
		return $res; 

	}
	function getLeadAndContactAssigned($get){
		global $callConfig;
		if($get['admin_type'] != 1){
			$originatercondition = " AND crml.originator = '".$get['user_id']."'";
			$originatercondition2 = " AND originator = '".$get['user_id']."'";
		}

		$application_userssql = "SELECT id,display_name,user_photo FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0  AND JSON_SEARCH(role_id, 'one', 'CRM_USER') IS NOT NULL";
		$application_usersres=$callConfig->getAllRows($application_userssql);
		

 		$crm_historiessql = "SELECT crml.access_user_id, COUNT(DISTINCT crml.id) AS lead_total_cnt, COUNT(DISTINCT crmh.transaction_id) AS lead_used_cnt FROM crm_leads crml LEFT JOIN crm_histories crmh ON crmh.company_id = crml.company_id AND crmh.transaction_table = 'crm_leads' AND crmh.transaction_id = crml.id AND crmh.is_active = crml.is_active WHERE crml.company_id = '".$get['company_id']."' ".$originatercondition." AND crml.is_active = 0 GROUP BY crml.access_user_id"; 
 		$return['crm_historiessql'] = $crm_historiessql;
 		$crm_historiesres=$callConfig->getAllRows($crm_historiessql);
 		foreach($crm_historiesres as $aus){
	 		$crm_histories_lead['crm_leads'][$aus->access_user_id] =$aus;
	 	}

 		$crm_contactssql = "SELECT crml.access_user_id, COUNT(DISTINCT crml.id) AS lead_total_cnt, COUNT(DISTINCT crmh.transaction_id) AS lead_used_cnt FROM crm_contacts crml LEFT JOIN crm_histories crmh ON crmh.company_id = crml.company_id AND crmh.transaction_table = 'crm_contacts' AND crmh.transaction_id = crml.id AND crmh.is_active = crml.is_active WHERE crml.company_id = '".$get['company_id']."' ".$originatercondition." AND crml.is_active = 0 GROUP BY crml.access_user_id"; 
 		$return['crm_contactssql'] = $crm_contactssql;
 		$crm_contactsres=$callConfig->getAllRows($crm_contactssql);
 		foreach($crm_contactsres as $aus){
	 		$crm_histories_lead['crm_contacts'][$aus->access_user_id] =$aus;
	 	}
 		$return['crm_histories_lead'] = $crm_histories_lead;

 		$application_users = array();
 		foreach($application_usersres as $aus){
 			$aus->leads = $crm_histories_lead['crm_leads'][$aus->id];
 			$aus->contacts = $crm_histories_lead['crm_contacts'][$aus->id];
 			$application_users[$aus->id] = $aus;
 		}
		$return['application_users'] = $application_users;

		$othercondition  =" WHERE company_id = '".$get['company_id']."' ".$originatercondition2." AND is_active = 0";
		$crm_leadssql = "SELECT (SELECT COUNT(id) as cnt FROM crm_leads ".$othercondition.") as crm_lead_total_cnt, (SELECT COUNT(id) as cnt FROM crm_leads ".$othercondition." AND access_user_id IS NOT NULL) as crm_lead_assigne_cnt, (SELECT COUNT(id) as cnt FROM crm_leads ".$othercondition." AND access_user_id IS NULL) as crm_lead_un_assigne_cnt, (SELECT COUNT(id) as cnt FROM crm_contacts ".$othercondition.") as crm_contacts_total_cnt, (SELECT COUNT(id) as cnt FROM crm_contacts ".$othercondition." AND access_user_id IS NOT NULL) as crm_contacts_assigne_cnt, (SELECT COUNT(id) as cnt FROM crm_contacts ".$othercondition." AND access_user_id IS NULL) as crm_contacts_un_assigne_cnt";
		$crm_leadsres=$callConfig->getRow($crm_leadssql);
		$return['dashboard_sql'] = $crm_leadssql;
		$return['dashboard_cnt'] = $crm_leadsres;

		$crm_dealssql = "SELECT id, project_name, (SELECT COUNT(pt.id) as cnt FROM project_teams pt WHERE pt.company_id = proj.company_id AND pt.project_id = proj.id AND pt.is_active = proj.is_active) as team_user_cnt FROM projects proj WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$crm_dealsres=$callConfig->getAllRows($crm_dealssql);
		$return['crm_projects'] = $crm_dealsres;

		return $return; 
	}
	function leadAndContactAssigning($get,$post){
		global $callConfig;
		$return['post'] = $post;
		$order_by = $post['order_by'];
		$assign_lead_contact = $post['assign_lead_contact'];
		if($get['admin_type'] != 1){
			$originatercondition = " AND originator = '".$get['user_id']."'";
		}
		if($post['assign_type'] =="2"){ //project
			$sql = "SELECT user_id FROM project_teams WHERE company_id ='".$post['company_id']."' AND project_id ='".$post['project_id']."'  AND is_active = 0";
			$resul=$callConfig->getAllRows($sql);
			$projuid = array();
			foreach($resul as $resultt){
				$projuid[] = $resultt->user_id;
			}
			$return['projuid'] = $projuid;
			$contactCount = $post['number_of_lead_contact']; 
			$employeeCount = count((array)$projuid); 
			$contactsPerEmployee = intdiv($contactCount, $employeeCount);
			$extraContacts = $contactCount % $employeeCount;
			$seq = 0;
			for ($i = 0; $i < $employeeCount; $i++){
			    $distribution = $contactsPerEmployee + ($i == $employeeCount - 1 ? $extraContacts : 0);
			    $user_id[] = array("user_id"=>$projuid[$seq],"contact"=>$distribution);
				$seq++;
			}	
		}else{ //user
			$user_id[] = array("user_id"=>$post['application_user_id'],"contact"=>$post['number_of_lead_contact']);
		}
		$insertary=array('company_id'=>$post['company_id'],'assign_type'=>$post['assign_type'],'assign_lead_contact'=>$post['assign_lead_contact'],'order_by'=>$post['order_by'],'user_id'=>json_encode((array)$user_id),'project_id'=>$post['project_id'],'number_of_lead_contact'=>$post['number_of_lead_contact'],'assigning_datetime'=>date("Y-m-d H:i:s"),'is_active'=>'0','created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"));
		$crm_lead_and_contact_assignings = $callConfig->insertRecord('crm_lead_and_contact_assignings',$insertary);
		$return['res'] = $crm_lead_and_contact_assignings;
		if($crm_lead_and_contact_assignings['status'] == 'sucess'){
			foreach($user_id as $usepor){
				$uids = $usepor['user_id'];
				$contact = $usepor['contact'];
				$query = "UPDATE ".$assign_lead_contact." SET access_user_id =".$uids.", access_datetime = '".date("Y-m-d H:i:s")."', crm_lead_and_contact_assigning_id =".$crm_lead_and_contact_assignings['LASTID']." WHERE company_id = '".$post['company_id']."' ".$originatercondition." AND `access_user_id` IS NULL AND is_active = 0 ORDER BY created_date asc limit ".$contact;
				$return['udate_crm_leads']  = $callConfig->executeQuery($query);

			    $msg = $contact. " : New Lead/Cntacts Assign For You";
				$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>"",'application_table_name'=>'crm_lead_and_contact_assignings', 'type'=>'INFORMATION', 'notification_users'=>array($uids),'option'=>'','assigner_id'=>$post['user_id']);
				$sendnotification = $this->tosendnotifiations($notdataa);

			}
		}
		return $return; 
	}
	function testAPI($get){
		global $callConfig;
		$return = array();


		// $get['transaction_date'] = date("Y-m-d");
		// $get['party_id'] = 1;
		// $get['transaction_type'] = '';
		// $return['transaction_number_details'] = $this->generatingApplicationSerialNumberMastersTst('purchase_invoices',$get);
		
/**payment tersm
		$transaction_table = 'sales_invoices';
		$sql = "SELECT * FROM `sales_invoices` WHERE  is_active = 0 AND cancel_invoice = 'N' AND current_status = 199";
		$partyinvoices=$callConfig->getAllRows($sql);
		foreach($partyinvoices as $partyinvoice){
			$transaction_no = $partyinvoice->id;
			$payment_term_history = json_decode($partyinvoice->payment_term_history,1);
			if(count((array)$payment_term_history) != "0"){
				$pth_cnt = count((array)$payment_term_history);
				$mk = 1;
				foreach($payment_term_history as $key=>$pterh){ 
					
					$insertdata = array('company_id'=>1,'transaction_table'=>$transaction_table,'transaction_id'=>$transaction_no,'transaction_number'=>$partyinvoice->transaction_number,'party_id'=>$partyinvoice->party_id,'due_date'=>$this->dateconvertdbformat($pterh['due_date']),'due_amount'=>$pterh['amount'],'advance_offer_date'=>$this->dateconvertdbformat($pterh['extra_date']),'advance_offer_amount'=>$pterh['extra_amt'],'pending_amount'=>$pterh['amount'],'is_active'=>'0','current_status'=>'0','created_date'=>date('Y-m-d H:i:s'),'created_by'=>$get['user_id'],'amount'=>$pterh['without_tax_amount'],'tax_amount'=>$pterh['tax_amount'],'percentage'=>$pterh['percentage'],'without_tax_pending_amount'=>$pterh['without_tax_amount'],'tax_pending_amount'=>$pterh['tax_amount']);
					$callConfig->insertRecord('payment_term_transactions',$insertdata);
					$mk++;
				}	
			}
		}
payment tersm**/ 


		//	$mobilsendnotification = array('company_id'=>1,'message'=>'hi test message','user_id'=>14,'title'=>'Test');
		//	$this->tocallmobilenotificationmob($mobilsendnotification);

//Start opening balance stock upload
	/**
		

		$itemsql = "SELECT * FROM `items`";
        $itemres = $callConfig->getAllRows($itemsql);
		foreach($itemres as $itm){

			
			//$day_rate = $this->togetitemwadayrate($itm->item_id, $get['company_id']);
			$day_rate = $day_rate !=""?$day_rate:0;
   			
		/**	
   			$reason_id = 1;//1:add, 2 minus
   			//$in_qty = $purchase_qty = $itm->plus_qty; 
			//$in_value = $itm->plus_qty*$day_rate;

			$in_qty = $purchase_qty = 0; 
			$in_value = 0;
			$out_qty = $sales_qty = 0;
			$out_value = 0;
			$sale_price = 0;
			$sale_value = 0;
		/**/	/*
			$reason_id = 2;//1:add, 2 minus
   			$in_qty = $purchase_qty = 0;
			$in_value = 0;
			
			$out_qty = $sales_qty = $itm->minus_qty; 
			$out_value = $itm->minus_qty*$day_rate;
			$sale_price = $day_rate;
			$sale_value = $itm->minus_qty*$day_rate;
	*//**
			$len = array('company_id' =>1 , 'location_id' =>1, 'transaction_warehouse_id' =>1, 'transaction_date' =>'2026-01-01', 'item_id' =>$itm->id,'document_no'=>$itm->id,'document_type' =>'items','document_ref_no' =>$itm->id,'in_qty' =>$in_qty,'in_value'=>$in_value,'item_in_value' =>$in_value,'reason_id'=>$reason_id,'out_qty' =>$out_qty,'out_value' =>$out_value,'sale_price'=>$sale_price,'sale_value'=>$sale_value,'item_wa_cost'=>$day_rate,'item_st_cost'=>0,'is_active'=>'0','current_status'=>'0','sales_qty'=>$sales_qty,'purchase_qty'=>$purchase_qty);
			//echo "<pre>"; print_r($len); echo "</br>";
			$ddf = $this->invoiceUpdates('items',$len);



		}
**/ 
//End opening balance stock upload


//Start SLA Traking Log
		/**
		//$transrtable = "sales_orders";
		//$transrtable = "sales_invoices";
		$transrtable = "delivery_orders";
		$sql = "SELECT * FROM ".$transrtable;
		$sires=$callConfig->getAllRows($sql);
		foreach ($sires as $sidata){
			$insertary = "INSERT INTO `sla_tracking_logs`(`company_id`, `module_setup_id`, `transaction_table`, `transaction_id`, `originator`, `request_datetime`, `response_datetime`, `remarks`, `sla_type`, `previous_status`, `response_status`, `is_active`, `current_status`) VALUES ('1',1,'".$transrtable."','".$sidata->id."','".$sidata->originator."','".$sidata->transaction_date." 14:54:58','".$sidata->transaction_date." 14:54:58','INSERT','T','2','',0,0),('1',1,'".$transrtable."','".$sidata->id."','".$sidata->originator."','".$sidata->transaction_date." 16:54:58','".$sidata->transaction_date." 16:54:58','WorkFlow','W','2','0',0,0),('1',1,'".$transrtable."','".$sidata->id."','4','".$sidata->transaction_date." 17:54:58','".$sidata->transaction_date." 17:54:58','WorkFlow','W','0','101',0,0),('1',1,'".$transrtable."','".$sidata->id."','3','".$sidata->transaction_date." 19:54:58','".$sidata->transaction_date." 19:54:58','WorkFlow','W','101','199',0,0)";
			//$res = $callConfig->executeQuery($insertary);	
			$return['maintrans_sql'][] = $insertary;
			$return['maintrans_res'][] = $res;
		}
		echo "<pre>"; print_r($return);
		**/
//End SLA Traking Log		

		

//Start Insert goods_receipt_notes 
		/**
		 //SELECT * FROM `purchase_invoices` WHERE current_status = 199  AND id >= 812
		//$sql ="SELECT * FROM `purchase_invoices` WHERE current_status = 199  AND id >= 812 AND is_active = 0";
		//$sql ="SELECT * FROM `purchase_invoices` WHERE id >=812 AND is_active = 0 AND cancel_invoice = 'N' AND current_status = 199";
		$sql ="SELECT * FROM `purchase_invoices` WHERE id >=897 AND is_active = 0 AND current_status = 199 AND cancel_invoice = 'N'";
		$sires=$callConfig->getAllRows($sql);
		foreach ($sires as $sidata){
			$return['maintrans'][] = $sidata;
			$insertary=array('company_id'=>$get['company_id'],'cost_center_id'=>$sidata->cost_center_id,'transaction_date'=>$sidata->transaction_date,'party_id'=>$sidata->party_id,'party_name'=>$sidata->party_name,'party_tax_number'=>$sidata->party_tax_number,'cost_center_id'=>$sidata->party_email,'party_email'=>$sidata->party_email,'party_phone'=>$sidata->party_phone,'party_address'=>$sidata->party_address,'party_ship_adderss'=>$sidata->party_ship_adderss,'payment_terms'=>$sidata->payment_terms,'amount'=>$sidata->amount,'tax_amount'=>$sidata->tax_amount,'type'=>$sidata->type,'tax_type'=>$sidata->tax_type,'discount_type'=>$sidata->discount_type,'discount_value'=>$sidata->discount_value,'discount_amount'=>$sidata->discount_amount,'total_amount'=>$sidata->total_amount,'tax_state'=>$sidata->tax_state,'due_date'=>$sidata->due_date,'ref_type'=>$sidata->ref_type,'ref_id'=>$sidata->ref_id,'ref_no'=>$sidata->ref_no,'ref_date'=>$sidata->ref_date,'payment_term_history'=>$sidata->payment_term_history,'pending_amount'=>$sidata->pending_amount,'total_tax_split_details'=>$sidata->total_tax_split_details,'is_active'=>0 ,'current_status'=>199); 
			$res = $callConfig->insertRecord('goods_receipt_notes',$insertary);	
			$return['maintrans_res'][] = $res;

			if($res['LASTID'] !=""){
				$siitemssql= "INSERT INTO grn_items (`company_id`, `transaction_id`, `item_id`, `account_code`, `item_name`, `description`, `purchase_uom`, `item_quantity`, `item_price`, `item_amount`, `tax_amount`, `discount_amount`, `total_amount`, `reason_id`, `doc_ref_no`, `doc_ref_date`, `req_date`, `item_st_cost`, `item_fifo_cost`, `item_lifo_cost`, `item_wa_cost`, `approved_quantity`, `transaction_type`, `transaction_qty`, `pending_qty`, `item_details`, `tax_deducted_source_id`, `tds_amount`, `ref_transaction_item_table_id`, ref_transaction_table_id, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date`) SELECT `company_id`, ".$res['LASTID'].", `item_id`, `account_code`, `item_name`, `description`, `purchase_uom`, `item_quantity`, `item_price`, `item_amount`, `tax_amount`, `discount_amount`, `total_amount`, `reason_id`, `doc_ref_no`, `doc_ref_date`, `req_date`, `item_st_cost`, `item_fifo_cost`, `item_lifo_cost`, `item_wa_cost`, `approved_quantity`, `transaction_type`, `transaction_qty`, `pending_qty`, `item_details`, `tax_deducted_source_id`, `tds_amount`, ref_transaction_item_table_id, ref_transaction_table_id, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date` FROM `pi_items` WHERE transaction_id = '".$sidata->id."' AND is_active = 0"; 
				$res1 = $callConfig->executeQuery($siitemssql);
				$return['maintrans_res1'][] = $res1;

				$siitemssql= "INSERT INTO grn_item_taxes (`company_id`, `transaction_id`, `transaction_item_id`, `tax_name`, `tax_value`, `tax_id`, `amount`, `tax_amount`, `total_amount`, `tax_type`, `tax_split_details`, `transaction_item_row_id`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date`)SELECT `company_id`, ".$res['LASTID'].", `transaction_item_id`, `tax_name`, `tax_value`, `tax_id`, `amount`, `tax_amount`, `total_amount`, `tax_type`, `tax_split_details`, `transaction_item_row_id`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date` FROM `pi_item_taxes` WHERE transaction_id = '".$sidata->id."' AND is_active = 0"; 
				$res2 = $callConfig->executeQuery($siitemssql);
				$return['maintrans_res1_text'][] = $res2;
			}
		}	

	**/
//End Insert goods_receipt_notes 



//Start Insert delivery_orders 
		/**
		$sql = "SELECT * FROM `sales_invoices` WHERE id >= 843 AND id <=995 AND is_active = 0";
		$sires=$callConfig->getAllRows($sql);
		$i=1;
		foreach ($sires as $sidata){
			$return['maintrans'][] = $sidata;
// 'transaction_number'=>'DO000'.$i
			$insertary=array('company_id'=>$get['company_id'],'cost_center_id'=>$sidata->cost_center_id,'transaction_date'=>$sidata->transaction_date,'party_id'=>$sidata->party_id,'party_name'=>$sidata->party_name,'party_tax_number'=>$sidata->party_tax_number,'cost_center_id'=>$sidata->party_email,'party_email'=>$sidata->party_email,'party_phone'=>$sidata->party_phone,'party_address'=>$sidata->party_address,'party_ship_adderss'=>$sidata->party_ship_adderss,'payment_terms'=>$sidata->payment_terms,'amount'=>$sidata->amount,'tax_amount'=>$sidata->tax_amount,'type'=>$sidata->type,'tax_type'=>$sidata->tax_type,'discount_type'=>$sidata->discount_type,'discount_value'=>$sidata->discount_value,'discount_amount'=>$sidata->discount_amount,'total_amount'=>$sidata->total_amount,'tax_state'=>$sidata->tax_state,'due_date'=>$sidata->due_date,'ref_type'=>$sidata->ref_type,'ref_id'=>$sidata->ref_id,'ref_no'=>$sidata->ref_no,'ref_date'=>$sidata->ref_date,'payment_term_history'=>$sidata->payment_term_history,'pending_amount'=>$sidata->pending_amount,'total_tax_split_details'=>$sidata->total_tax_split_details,'is_active'=>0 ,'current_status'=>199); 
			$res = $callConfig->insertRecord('delivery_orders',$insertary);	
			$return['maintrans_res'][] = $res;

			if($res['LASTID'] !=""){
			//$callConfig->updateRecord("sales_invoices",array('ref_no'=>$res['LASTID']),'id',$sidata->id);
				$siitemssql= "INSERT INTO do_items (`company_id`, `transaction_id`, `item_id`, `account_code`, `item_name`, `description`, `purchase_uom`, `item_quantity`, `item_price`, `item_amount`, `tax_amount`, `discount_amount`, `total_amount`, `reason_id`, `doc_ref_no`, `doc_ref_date`, `req_date`, `item_st_cost`, `item_fifo_cost`, `item_lifo_cost`, `item_wa_cost`, `approved_quantity`, `transaction_type`, `transaction_qty`, `pending_qty`, `item_details`, `tax_deducted_source_id`, `tds_amount`, `ref_transaction_item_table_id`, ref_transaction_table_id, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date`)SELECT `company_id`, ".$res['LASTID'].", `item_id`, `account_code`, `item_name`, `description`, `purchase_uom`, `item_quantity`, `item_price`, `item_amount`, `tax_amount`, `discount_amount`, `total_amount`, `reason_id`, `doc_ref_no`, `doc_ref_date`, `req_date`, `item_st_cost`, `item_fifo_cost`, `item_lifo_cost`, `item_wa_cost`, `approved_quantity`, `transaction_type`, `transaction_qty`, `pending_qty`, `item_details`, `tax_deducted_source_id`, `tds_amount`, `id`,  transaction_id,`is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date` FROM `si_items` WHERE transaction_id = '".$sidata->id."'  AND is_active = 0"; 
				$res1 = $callConfig->executeQuery($siitemssql);
				$return['maintrans_res1'][] = $res1;

				$siitemssql= "INSERT INTO do_item_taxes (`company_id`, `transaction_id`, `transaction_item_id`, `tax_name`, `tax_value`, `tax_id`, `amount`, `tax_amount`, `total_amount`, `tax_type`, `tax_split_details`, `transaction_item_row_id`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date`)SELECT `company_id`, ".$res['LASTID'].", `transaction_item_id`, `tax_name`, `tax_value`, `tax_id`, `amount`, `tax_amount`, `total_amount`, `tax_type`, `tax_split_details`, `transaction_item_row_id`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date` FROM `si_item_taxes` WHERE transaction_id = '".$sidata->id."'  AND is_active = 0"; 
				$res2 = $callConfig->executeQuery($siitemssql);
				$return['maintrans_res1_tax'][] = $res2;

			}

			$i++;
		
		} 
		**/
//End Insert delivery_orders 


		



//https://dev.infistaq.in/crm_api.php/?mode=testapi&company_id=1&data_name=infistaqin_live_nri&user_id=1
/**
 		//$sql1 = "SELECT id, transaction_date,transaction_location_id, transaction_warehouse_id, transaction_number FROM goods_receipt_notes WHERE MONTH(transaction_date) IN(01) AND  YEAR(transaction_date) IN(2026) AND is_active = 0  ORDER BY `transaction_date` ASC";
		//$sql = "SELECT id, transaction_date,transaction_location_id, transaction_warehouse_id, transaction_number FROM goods_receipt_notes ORDER BY `transaction_date` ASC";
		$sql1 = "SELECT id, transaction_date,transaction_location_id, transaction_warehouse_id, transaction_number FROM goods_receipt_notes WHERE MONTH(transaction_date) IN(02,03,04) AND  YEAR(transaction_date) IN(2026) AND is_active = 0  ORDER BY `transaction_date` ASC";
		$sires1=$callConfig->getAllRows($sql1);
		$datewithgrndo = array();
		foreach ($sires1 as $transaction1){
			$datewithgrndo111[$transaction1->transaction_date] =$transaction1->transaction_date; 
			$datewithgrndo[$transaction1->transaction_date]['goods_receipt_notes'][] =$transaction1; 
		}

		$sql2 = "SELECT * FROM `delivery_orders` WHERE transaction_number IS NULL ORDER BY `transaction_date` ASC";
		
		//$sql2 = "SELECT id, transaction_date,transaction_location_id, transaction_warehouse_id, transaction_number FROM delivery_orders WHERE  MONTH(transaction_date) IN(01) AND  YEAR(transaction_date) IN(2026)  AND is_active = 0 ORDER BY `transaction_date` ASC";
		//$sql = "SELECT id, transaction_date,transaction_location_id, transaction_warehouse_id, transaction_number FROM delivery_orders ORDER BY `transaction_date` ASC";
		$sires2=$callConfig->getAllRows($sql2);
		foreach ($sires2 as $transaction2){
			$datewithgrndo111[$transaction2->transaction_date] =$transaction2->transaction_date; 
			$datewithgrndo[$transaction2->transaction_date]['delivery_orders'][] =$transaction2; 
		}
		ksort($datewithgrndo);
		$i=1;
		foreach($datewithgrndo as $date=>$transtab){
			foreach($transtab as $transaction_table=>$sires1){
			
				$get['transaction_table'] = $transaction_table;
				$parentdmtsql = "SELECT id,display_table,item_table,tax_table FROM `dynamic_mapping_tables` WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$get['transaction_table']."' AND item_table !='' AND is_active = 0";
				$parentdmtres=$callConfig->getRow($parentdmtsql);

				foreach ($sires1 as $transaction){
					//stock In And Out Code Staart
					$out_array = array('grn_returns','stock_transfers','delivery_orders','stock_transfer_receipts','stock_issues');
					$in_array = array('goods_receipt_notes','stock_transfer_returns','sales_returns');
					$reason_array = array('stock_adjustments','stock_take');
					$purchaseuomtables = array('goods_receipt_notes','grn_returns');
					$salesuomtables = array('delivery_orders','sales_returns');

				 	$itemressql = "SELECT id,company_id,item_id,item_quantity,total_amount,item_amount,reason_id,item_price FROM ".$parentdmtres->item_table." WHERE company_id = '".$get['company_id']."' AND transaction_id = '".$transaction->id."' AND is_active = 0";
					$itemresult=$callConfig->getAllRows($itemressql);

					foreach($itemresult as $itemres){
						$mainitem = $this->togetitemvalues($itemres->item_id);
						$day_rate = $this->togetitemwadayrate($itemres->item_id, $get['company_id']);	
						$len = array('company_id' =>$itemres->company_id , 'location_id' =>$transaction->transaction_location_id, 'transaction_warehouse_id' => $transaction->transaction_warehouse_id, 'transaction_date' => $transaction->transaction_date , 'item_id' =>$itemres->item_id,'document_no'=>$transaction->id,'document_type' =>$get['transaction_table'],'document_ref_no' =>$transaction->transaction_number,'in_qty' =>$itemres->item_quantity,'in_value' =>$itemres->total_amount,'item_in_value' =>$itemres->item_amount,'reason_id'=>$itemres->reason_id,'out_qty' =>$itemres->item_quantity,'out_value' =>$itemres->total_amount,'sale_price'=>$itemres->item_price,'sale_value'=>$itemres->total_amount,'item_wa_cost'=>$day_rate,'item_st_cost'=>$mainitem->purchase_price,'is_active'=>'0','current_status'=>'0','sales_qty'=>0,'purchase_qty'=>0);
					

						if(in_array($get['transaction_table'], (array)$purchaseuomtables)){
							$len['purchase_uom'] =$itemres->purchase_uom;
							$len['purchase_qty'] = $itemres->item_quantity;
							$len['purchase_base_uom'] = $itemres->purchase_base_uom;
							// $parent_uom_id= $mainitem->purchase_base_uom;
							// $child_uom_id= $mainitem->sale_base_uom;
							$parent_uom_id= $itemres->purchase_uom;
							$child_uom_id= $mainitem->purchase_base_uom;

						}elseif(in_array($get['transaction_table'], (array)$salesuomtables)){
							$len['sales_uom'] = $itemres->purchase_uom;
							$len['sales_qty'] = $itemres->item_quantity;
							$len['sale_base_uom'] = $itemres->sale_base_uom;
							// $parent_uom_id= $mainitem->sale_base_uom;
							// $child_uom_id= $mainitem->purchase_base_uom;
							$parent_uom_id= $itemres->purchase_uom;
							$child_uom_id= $mainitem->sale_base_uom;
						}


						// $convsql = "SELECT id,conversion_ratio FROM uom_conversions WHERE company_id = '".$get['company_id']."' AND ((parent_uom_id = '".$parent_uom_id."' AND child_uom_id = '".$child_uom_id."') OR (parent_uom_id = '".$child_uom_id."' AND child_uom_id = '".$parent_uom_id."')) AND is_active = 0";
						// $convresd = $callConfig->getRow($convsql);
						// if($convresd->id !=""){
						// 	//ok coneversion
						// 	if($convresd->conversion_ratio !="0" AND $convresd->conversion_ratio !=""){
						// 		$len['in_qty'] = $itemres->item_quantity*$convresd->conversion_ratio;
						// 		$len['in_value'] = $itemres->item_amount*$convresd->conversion_ratio;
						// 	}
						// }else{
						// 	//reverse coneversion
						// 	$convsql = "SELECT id,conversion_ratio FROM uom_conversions WHERE company_id = '".$get['company_id']."' AND  ((parent_uom_id = '".$parent_uom_id."' AND child_uom_id = '".$child_uom_id."') OR (parent_uom_id = '".$child_uom_id."' AND child_uom_id = '".$parent_uom_id."'))  AND is_active = 0";
						// 	$convresd = $callConfig->getRow($convsql);
						// 	if($convresd->id !=""){
						// 		if($convresd->conversion_ratio !="0" AND $convresd->conversion_ratio !=""){
						// 			$len['in_qty'] = $post['in_qty']/$convresd->conversion_ratio;
						// 			$len['in_value'] = $itemres->item_amount/$convresd->conversion_ratio;
						// 		}
						// 	}
						// }

						if(in_array($get['transaction_table'], (array)$in_array)){
							$len['out_qty'] = '0';
							$len['out_value'] = '0'; 
						}else if(in_array($get['transaction_table'], (array)$out_array)){
							$len['in_qty'] = '0';
							$len['in_value'] = '0'; 
						}else if(in_array($get['transaction_table'], (array)$reason_array)){
							if($itemres->reason_id == 0){
								$len['in_qty'] = '0';
								$len['in_value'] = '0'; 
							}else{
								$len['out_qty'] = '0';
								$len['out_value'] = '0'; 
							}
						}else{
							$len = array();
						}
						//echo "<pre>"; print_r($len); echo "</pre>";
						//echo $transaction->transaction_date." -------- ".$get['transaction_table']." --- transaction_number - ".$transaction->transaction_date."  - ".$transaction->id."</br></br></br>";
						if(count((array)$len) !=0){
							
							$ddf = $this->invoiceUpdates($get['transaction_table'],$len);
								//echo "<pre>"; print_r($len); echo "</pre>";
						} 
					}
					//stock In And Out Code End
					
				}	
			}
		}
**/
//stock
		/**/





	/**	

		foreach ($sires as $transaction){
			//echo "<pre>"; print_r($transaction->id); echo "</pre>";
			//stock In And Out Code Staart
			$out_array = array('grn_returns','stock_transfers','delivery_orders','stock_transfer_receipts','stock_issues');
			$in_array = array('goods_receipt_notes','stock_transfer_returns','sales_returns');
			$reason_array = array('stock_adjustments','stock_take');

			$purchaseuomtables = array('goods_receipt_notes','grn_returns');
			$salesuomtables = array('delivery_orders','sales_returns');

			$parentdmtsql = "SELECT id,display_table,item_table,tax_table FROM `dynamic_mapping_tables` WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$get['transaction_table']."' AND item_table !='' AND is_active = 0";
			$parentdmtres=$callConfig->getRow($parentdmtsql);

			$itemressql = "SELECT * FROM ".$parentdmtres->item_table." WHERE company_id = '".$get['company_id']."' AND transaction_id = '".$transaction->id."' AND is_active = 0";
			$itemresult=$callConfig->getAllRows($itemressql);

			foreach($itemresult as $itemres){
				$mainitem = $this->togetitemvalues($itemres->item_id);
				$day_rate = $this->togetitemwadayrate($itemres->item_id, $get['company_id']);	
				$len = array('company_id' =>$itemres->company_id , 'location_id' =>$transaction->transaction_location_id, 'transaction_warehouse_id' => $transaction->transaction_warehouse_id, 'transaction_date' => $transaction->transaction_date , 'item_id' =>$itemres->item_id,'document_no'=>$transaction->id,'document_type' =>$get['transaction_table'],'document_ref_no' =>$transaction->transaction_number,'in_qty' =>$itemres->item_quantity,'in_value' =>$itemres->total_amount,'item_in_value' =>$itemres->item_amount,'reason_id'=>$itemres->reason_id,'out_qty' =>$itemres->item_quantity,'out_value' =>$itemres->total_amount,'sale_price'=>$itemres->item_price,'sale_value'=>$itemres->total_amount,'item_wa_cost'=>$day_rate,'item_st_cost'=>$mainitem->purchase_price,'is_active'=>'0','current_status'=>'0','sales_qty'=>0,'purchase_qty'=>0);
				//echo "<pre>"; print_r($len);echo "<pre>";

				if(in_array($get['transaction_table'], (array)$purchaseuomtables)){
					$len['purchase_uom'] =$itemres->purchase_uom;
					$len['purchase_qty'] = $itemres->item_quantity;
					$len['purchase_base_uom'] = $itemres->purchase_base_uom;
					$parent_uom_id= $mainitem->purchase_base_uom;
					$child_uom_id= $mainitem->sale_base_uom;

				}elseif(in_array($get['transaction_table'], (array)$salesuomtables)){
					$len['sales_uom'] = $itemres->purchase_uom;
					$len['sales_qty'] = $itemres->item_quantity;
					$len['sale_base_uom'] = $itemres->sale_base_uom;
					$parent_uom_id= $mainitem->sale_base_uom;
					$child_uom_id= $mainitem->purchase_base_uom;
				}
				$convsql = "SELECT id,conversion_ratio FROM uom_conversions WHERE company_id = '".$get['company_id']."' AND  parent_uom_id = '".$parent_uom_id."' AND child_uom_id = '".$child_uom_id."' AND is_active = 0";
				$convresd = $callConfig->getRow($convsql);
				if($convresd->id !=""){
					//ok coneversion
					if($convresd->conversion_ratio !="0" AND $convresd->conversion_ratio !=""){
						$len['in_qty'] = $itemres->item_quantity*$convresd->conversion_ratio;
						$len['in_value'] = $itemres->item_amount*$convresd->conversion_ratio;
					}
				}else{
					//reverse coneversion
					$convsql = "SELECT id,conversion_ratio FROM uom_conversions WHERE company_id = '".$get['company_id']."' AND  parent_uom_id = '".$child_uom_id."' AND child_uom_id = '".$parent_uom_id."' AND is_active = 0";
					$convresd = $callConfig->getRow($convsql);
					if($convresd->id !=""){
						if($convresd->conversion_ratio !="0" AND $convresd->conversion_ratio !=""){
							$len['in_qty'] = $post['in_qty']/$convresd->conversion_ratio;
							$len['in_value'] = $itemres->item_amount/$convresd->conversion_ratio;
						}
					}
				}
				if(in_array($get['transaction_table'], (array)$in_array)){
					$len['out_qty'] = '0';
					$len['out_value'] = '0'; 
				}else if(in_array($get['transaction_table'], (array)$out_array)){
					$len['in_qty'] = '0';
					$len['in_value'] = '0'; 
				}else if(in_array($get['transaction_table'], (array)$reason_array)){
					if($itemres->reason_id == 0){
						$len['in_qty'] = '0';
						$len['in_value'] = '0'; 
					}else{
						$len['out_qty'] = '0';
						$len['out_value'] = '0'; 
					}
				}else{
					$len = array();
				}
				if(count((array)$len) !=0){
					$ddf = $this->invoiceUpdates($get['transaction_table'],$len);
					//echo "<pre>"; print_r('woerwfwfcwefc'); echo "</pre>";
				} 
			}
			//stock In And Out Code End
	}

**/


/** //manual stock adjustments Start
	$itemsql = "SELECT * FROM excel_march_item_stocks where item_id = 8077";
        $itemres11 = $callConfig->getAllRows($itemsql);
        echo "<pre>"; print_r($itemres11);
        
        foreach($itemres11 as $itemres){ 
				$mainitem = $this->togetitemvalues($itemres->item_id);
				$day_rate = $this->togetitemwadayrate($itemres->item_id, 1);	
				$len = array('company_id' =>1 , 'location_id' =>1, 'transaction_warehouse_id' => 1, 'transaction_date' =>'2025-04-01', 'item_id' =>$itemres->item_id,'document_no'=>0,'document_type' =>'stock_adjustments','document_ref_no' =>0,'in_qty' =>0,'in_value' =>0,'item_in_value' =>0,'reason_id'=>0,'out_qty' =>0,'out_value' =>0,'sale_price'=>0,'sale_value'=>0,'item_wa_cost'=>$day_rate,'item_st_cost'=>$mainitem->purchase_price,'is_active'=>'0','current_status'=>'0','sales_qty'=>0,'purchase_qty'=>0);
				$doandgrn = $itemres->actual_qty-$itemres->closing_qty;
				//$oldrate = $itemres->closing_val/($itemres->closing_qty==0?1:$itemres->closing_qty);
				$oldrate = abs(($itemres->actual_qty*$itemres->actual_rate)-$itemres->closing_val)/(abs($doandgrn)!=0?abs($doandgrn):1);
				echo "rate --  ".$oldrate;
				if($doandgrn > 0){
					$len['in_qty'] = $doandgrn;
					$len['in_value'] = $doandgrn*$oldrate; 
					$len['item_in_value'] = $doandgrn*$oldrate; 
					$len['item_wa_cost'] = $oldrate; 
					$len['item_st_cost'] = $oldrate; 
				}else{
					$len['out_qty'] = abs($doandgrn);
					$len['out_value'] = abs($doandgrn)*$oldrate; 
					$len['sale_price'] = $oldrate;
					$len['sale_value'] = abs($doandgrn)*$oldrate; 
					$len['item_wa_cost'] = $oldrate; 
					$len['item_st_cost'] = $oldrate;  
				}
				$bdate = '2025-04-01';
				$insert = array('company_id' =>$len['company_id'] , 'item_id' =>$len['item_id'] ,'balance_date' =>$bdate,'in_qty' =>$len['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$len['out_qty'] ,'out_value' =>$len['out_value'],'day_value' => $len['day_value'],'day_rate' => $len['day_rate']);
				$day_rate = ($itemres->closing_val/($itemres->closing_qty!=0?$itemres->closing_qty:1));
				$insert['opening_qty'] = $itemres->closing_qty;
				$insert['closing_qty'] = $itemres->closing_qty+$len['in_qty']-$len['out_qty'];
				$insert['opening_value'] = $itemres->closing_val;
				$insert['day_value'] = $itemres->closing_val+$len['in_value']-$len['out_value'];
				$insert['opening_rate'] = $day_rate;
				if($insert['closing_qty'] !="" AND $insert['closing_qty']!="0"){
					$insert['day_rate'] = $insert['day_value']/($insert['closing_qty']!=0?$insert['closing_qty']:1);
				}else{
					$insert['day_rate'] = 0;
				}
				$res=$callConfig->insertRecord('item_wa_balances',$insert);

			$insert = array('company_id' =>1 , 'location_id' =>1, 'ware_house_id'=>1,'item_id' =>$len['item_id'] ,'balance_date' =>$bdate ,'in_qty' =>$len['in_qty'] ,'in_value' =>$len['in_value'],'out_qty' =>$len['out_qty'] ,'out_value' =>$len['out_value'],'day_value' => $len['day_value'],'day_rate' => $len['day_rate']);
			$day_rate = ($itemres->closing_val/($itemres->closing_qty!=0?$itemres->closing_qty:1));
			$insert['opening_qty'] = $itemres->closing_qty;
			$insert['closing_qty'] = $itemres->closing_qty+$len['in_qty']-$len['out_qty'];
			$insert['opening_value'] = $itemres->closing_val;
			$insert['day_value'] = $itemres->closing_val+$len['in_value']-$len['out_value'];
			$insert['opening_rate'] = $day_rate;
			if($insert['closing_qty'] !="" AND $insert['closing_qty']!="0"){
				$insert['day_rate'] = $insert['day_value']/($insert['closing_qty']!=0?$insert['closing_qty']:1);
			}else{
				$insert['day_rate'] = 0;
			}
			$insert['ware_house_id'] = $post['transaction_warehouse_id'];
			//print_r($insert); //exit;
			$res=$callConfig->insertRecord('loc_item_wa_balances',$insert);



			$insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'balance_date' =>$bdate ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['day_value'],'day_rate' => $post['day_rate']);
			$day_rate = ($itemres->closing_val/($itemres->closing_qty!=0?$itemres->closing_qty:1));
						$insert['opening_qty'] = $itemres->closing_qty;
						$insert['closing_qty'] = $itemres->closing_qty+$len['in_qty']-$len['out_qty'];
						$insert['opening_value'] = $itemres->closing_val;
						$insert['day_value'] = $itemres->closing_val+$len['in_value']-$len['out_value'];
						$insert['opening_rate'] = $day_rate;
						if($insert['closing_qty'] !="" AND $insert['closing_qty']!="0"){
							$insert['day_rate'] = $insert['day_value']/($insert['closing_qty']!=0?$insert['closing_qty']:1);
						}else{
							$insert['day_rate'] = 0;
						}
			$insert['ware_house_id'] = $post['transaction_warehouse_id'];
			//print_r($insert); //exit;
			$res=$callConfig->insertRecord('loc_item_st_balances',$insert);


			$insert = array('company_id' =>$post['company_id'] , 'item_id' =>$post['item_id'] ,'balance_date' =>$bdate ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['day_value'],'day_rate' => $post['day_rate']);
			$day_rate = ($itemres->closing_val/($itemres->closing_qty!=0?$itemres->closing_qty:1));
						$insert['opening_qty'] = $itemres->closing_qty;
						$insert['closing_qty'] = $itemres->closing_qty+$len['in_qty']-$len['out_qty'];
						$insert['opening_value'] = $itemres->closing_val;
						$insert['day_value'] = $itemres->closing_val+$len['in_value']-$len['out_value'];
						$insert['opening_rate'] = $day_rate;
						if($insert['closing_qty'] !="" AND $insert['closing_qty']!="0"){
							$insert['day_rate'] = $insert['day_value']/($insert['closing_qty']!=0?$insert['closing_qty']:1);
						}else{
							$insert['day_rate'] = 0;
						}
			$res=$callConfig->insertRecord('item_st_balances',$insert);



			$insert = array('company_id' =>1 , 'location_id' =>1 ,'item_id' =>$len['item_id'] ,'balance_date' =>$bdate ,'in_qty' =>$len['in_qty'] ,'in_value' =>$len['in_value'],'day_value' => $post['day_value'],'day_rate' => $len['day_rate']);
			$res=$callConfig->insertRecord('loc_item_fifo_lifo_balances',$insert);


				$insert = array('company_id' =>1 , 'location_id' =>1,'item_id' =>$len['item_id'] ,'document_no' =>0 ,'document_type' =>'','document_ref_no' =>'','document_date' =>$len['transaction_date'],'in_qty' =>$len['in_qty'] ,'in_value' =>$len['in_value'],'item_cost' => $len['item_st_cost'],'total_cost'=> $len['item_st_cost']*($len['in_qty']+$len['out_qty']),'out_qty' => $len['out_qty'], 'out_value' => $len['out_value'],'is_active'=>0);
				$res=$callConfig->insertRecord('item_ledger_st',$insert);

				$insert = array('company_id' =>1 , 'location_id' =>1 ,'item_id' =>$len['item_id'] ,'document_no' =>$len['document_no'] ,'document_type' =>$len['document_type'] ,'document_ref_no' =>$len['document_ref_no'] ,'document_date' =>$len['transaction_date'],'in_qty' =>$len['in_qty'] ,'in_value' =>$len['in_value'], 'item_cost' => $len['item_wa_cost'],'total_cost'=> $len['item_wa_cost']*($len['in_qty']+$len['out_qty']),'out_qty' => $len['out_qty'], 'out_value' => $len['out_value'],'is_active'=>'0','purchase_uom' => $len['purchase_uom'],'purchase_qty' => $len['purchase_qty'],'purchase_base_uom' => $len['purchase_base_uom'],'sales_uom' => $len['sales_uom'],'sales_qty' => $len['sales_qty'],'sale_base_uom' => $len['sale_base_uom'],'is_active'=>0);
				$res=$callConfig->insertRecord('item_ledger_wa',$insert);

		}
**/
/** manual stock adjustments Start **/




		echo "Working<pre>"; print_r($return);

		//return $return; 
	}

	//Koteswar start
	function importCSV($get, $post){
    	global $callConfig;
    	$return = array();
    	// $return['get'] = $get;
    	// $return['post'] = $post;
    	$parent_id =  '';
    	
    	$data = array(
    		'file_name' => $post['file_name'],
    		'import_name' => $post['import_name'],
    		'company_id' => $get['company_id'],
	        'current_status' => '0',
	        'is_active' => '0',
	        'created_date' => date('Y-m-d H:i:s'),
	        'created_by' => $get['user_id'],
	        'import_date' =>$this->dateconvertdbformat($post['import_date']),
	        'invoice_from_date' =>$this->dateconvertdbformat($post['invoice_from_date']),
	        'invoice_to_date' =>$this->dateconvertdbformat($post['invoice_to_date'])
    	);
    	// print_r($data);

    	$res = $callConfig->insertRecord('sales_invoice_data_parent', $data);
    	$parent_id = $res['LASTID'];
    	$return['parent'] = $res;

	    $csvData = base64_decode($post['filedata']);

	    $rows = explode("\n", $csvData);
	    $headerSkipped = false;
	    // print_r($rows);  exit;
	    if($parent_id != '' && $parent_id > 0){
			foreach ($rows as $line) {
			    if (!$headerSkipped) {
			        $headerSkipped = true;
			        continue;
			    }

			    $columns = str_getcsv($line, ",", '"');
			    // print_r($columns);  exit;
			    if (count($columns) < 32 && count(array_filter($columns)) === 0) {
				    continue; // skip only completely empty lines
				}

		        $insert_array = array();

		        $insert_array['user_name'] = trim($columns[0]);
		        $insert_array['party_name'] = trim($columns[1]);
		        $insert_array['user_code'] = trim($columns[2]);
		        $insert_array['pin_code'] = trim($columns[3]);
		        $insert_array['sales_person'] = trim($columns[4]);
		        $insert_array['address'] = trim($columns[5]);
		        $insert_array['mobile_no'] = trim($columns[6]);
		        $insert_array['expiry_date'] = $this->toMySQLDate($columns[7]);
		        $insert_array['mode_of_renew'] = trim($columns[8]);
		        $insert_array['bill_no'] = trim($columns[9]);
		        $insert_array['activation_type'] = trim($columns[10]);
		        $insert_array['plan_activation_date'] = $this->toMySQLDate($columns[11]);;
		        $insert_array['bill_date'] = $this->toMySQLDateTime($columns[12]);
		        $insert_array['plan_amount'] = trim($columns[13]);
		        $insert_array['client_gst'] = trim($columns[14]);
		        $insert_array['sgst'] = trim($columns[15]);
		        $insert_array['cgst'] = trim($columns[16]);
		        $insert_array['bill_amount'] = trim($columns[17]);
		        $insert_array['operator_amount'] = trim($columns[18]);
		        $insert_array['partner_share'] = trim($columns[19]);
		        $insert_array['partner_commission'] = trim($columns[20]);
		        $insert_array['adjusted_amount'] = trim($columns[21]);
		        $insert_array['actual_bill_amount'] = trim($columns[22]);
		        $insert_array['discount_amount'] = trim($columns[23]);
		        $insert_array['user_pending_amount'] = trim($columns[24]);
		        $insert_array['plan_name'] = trim($columns[25]);
		        $insert_array['site'] = trim($columns[26]);
		        $insert_array['building_name'] = trim($columns[27]);
		        $insert_array['zone_name'] = trim($columns[28]);
		        $insert_array['operator_name'] = trim($columns[29]);
		        $insert_array['franchisee_name'] = trim($columns[30]);
		        $insert_array['user_current_status'] = trim($columns[31]);
		        $insert_array['parent_id'] = $parent_id;
		        $insert_array['company_id'] = $get['company_id'];
		        $insert_array['current_status'] = '0';
		        $insert_array['is_active'] = '0';
		        $insert_array['created_date'] = date('Y-m-d H:i:s');
		        $insert_array['created_by'] = $get['user_id'];

		        // print_r($insert_array); exit;
		        $res1 = $callConfig->insertRecord('sales_invoice_data_excel', $insert_array);
		        $return['res'][] = $res1;
		       	if (!$res1) {
				    error_log("Insert failed: " . json_encode($insert_array));
				}
			    
			}
		}
	    // echo json_encode(['status' => 'success', 'message' => 'Data imported']);

	   	return $return;

	}
	
	function toMySQLDate($dateString) {
	    $date = DateTime::createFromFormat('d-m-Y', trim($dateString));
	    return $date ? $date->format('Y-m-d') : null;
	}

	function toMySQLDateTime($dateTimeString) {
	    $date = DateTime::createFromFormat('d-m-Y H:i:s', trim($dateTimeString));
	    return $date ? $date->format('Y-m-d H:i:s') : null;
	}

	function getSalesInvoiceDynamicData($get){
		global $callConfig;
        $accountlist = array();
        $return['get'] = $get;
        // $sql = "SELECT id,user_name,party_name,mobile_no,expiry_date,bill_no, count(*) as line_of_item_count FROM sales_invoice_data_excel WHERE company_id = '".$get['company_id']."'  AND is_active = 0 GROUP BY bill_no";
        $sql = "SELECT s.id,s.user_name,s.party_name,s.mobile_no,s.expiry_date,s.bill_no, s.plan_name,
			    CASE 
			        WHEN (c.company IS NOT NULL  OR c.display_name IS NOT NULL) THEN 'Yes' 
			        ELSE 'No' 
			    END AS party_exists,
			    CASE 
			        WHEN (i.item_name IS NOT NULL OR i.sku IS NOT NULL)  THEN 'Yes' 
			        ELSE 'No'
			     END AS plan_exists,     
			    CASE 
			        WHEN si.transaction_number IS NOT NULL  THEN 'Yes' 
			        ELSE 'No'    
			    END AS transaction_exists,
			    CASE 
			        WHEN (sm.salesman_name IS NOT NULL) THEN 'Yes' 
			        ELSE 'No'
			     END AS salesman_exists,
			    si.id as sales_invoice_id,
			    si.transaction_number as sales_invoice_transaction_number
			FROM 
			    sales_invoice_data_excel s
			LEFT JOIN 
			    customers c ON (c.company = s.party_name OR c.display_name = s.party_name) AND c.company_id= s.company_id AND c.is_active=0
			LEFT JOIN 
			    items i ON (i.item_name = s.plan_name OR i.sku = s.plan_name) AND i.company_id= s.company_id AND i.is_active=0
			LEFT JOIN 
			    salesmen sm ON (sm.salesman_name = s.user_name) AND sm.company_id= s.company_id AND sm.is_active=0    
			LEFT JOIN 
			    sales_invoices si ON si.doc_ref_no = s.bill_no AND si.company_id= s.company_id AND si.is_active=0     
			WHERE s.parent_id = ".$get['rid']." AND  
			    s.company_id = ".$get['company_id']." AND s.is_active = 0";
		$res = $callConfig->getAllRows($sql);
		$sales = array();
		foreach($res as $row) {
			$sales[] = $row;
		}
		$return['sales_sql'] = $sql;
		$return['sales'] = $sales;

		return $return;
	}

	function getSalesInvoiceDynamicExcelList($get){
		global $callConfig;
        $accountlist = array();
        // $return['get'] = $get;
        if($get['LIMIT']!=""){
        	$condlimi = " LIMIT ".$get['LIMIT'];
        }
        $sql = "SELECT * FROM sales_invoice_data_parent WHERE company_id=".$get['company_id']." AND is_active=0 ORDER BY `import_date` ASC".$condlimi;  
		$res = $callConfig->getAllRows($sql);
		$excel_data = array();
		foreach($res as $row) {
			$excel_data[] = $row;
		}
		$return['excel_data'] = $excel_data;

		return $return;

	}

	function createSalesInvoice($get, $post) {
	    global $callConfig;
	    $return =  array();
	    $return['post'] = $post;
    	$selected_ids = $post['selected_ids'];
    	$sales_tables = array('enquiries','estimates','delivery_orders','sales_orders','sales_returns');
    	$hsnary = array('5'=>array('hsn_id'=>141,'hsn_name'=>'0303.00.00','percentage'=>5),'12'=>array('hsn_id'=>5,'hsn_name'=>'0101.21.00','percentage'=>12),'18'=>array('hsn_id'=>245,'hsn_name'=>'0402.91.10','percentage'=>18),'28'=>array('hsn_id'=>1086,'hsn_name'=>'1703.00.00','percentage'=>28));
    	if(count((array)$selected_ids)!= 0){
    		$bill_no_array = $application_usersuser_name =$application_usersdiplay_name = $customerscompany = $customersdisplay_name = array();
    		$implodeid = implode("','",$selected_ids);
	    	$query = "SELECT * FROM sales_invoice_data_excel WHERE bill_no IN ('".$implodeid."') AND company_id = ".$get['company_id']." AND parent_id = '".$post['sales_invoice_data_parent_id']."' AND is_active = 0";
	    	$query_result = $callConfig->getAllRows($query);
	        //$return['sales_invoice_data_excel_res'] = $query_result;
	        foreach($query_result as $side){
	        	$bill_no_array[$side->bill_no][] = $side;
	        }
	        $return['bill_no_array'] = $bill_no_array;
	        // $auquery = "SELECT id, user_name, display_name FROM application_users WHERE company_id = ".$get['company_id']." AND is_active = 0";
            // $aures = $callConfig->getAllRows($auquery);
            // foreach($aures as $auser){
	        // 	$application_usersuser_name[$auser->user_name] = $auser;
	        // 	$application_usersdiplay_name[$auser->user_name] = $auser;
	        // }
	        //$return['application_usersuser_name'] = $application_usersuser_name;
	        //$return['application_usersdiplay_name'] = $application_usersdiplay_name;
            $custquery = "SELECT id, company, display_name,email, phone, mobile, billing_address, shipping_address, payment_terms, gstin, pan_no FROM customers WHERE company_id = ".$get['company_id']." AND is_active = 0";
            $custres = $callConfig->getAllRows($custquery);
            foreach($custres as $custrs){
	        	$customerscompany[$custrs->company] = $custrs;
	        	$customersdisplay_name[$custrs->display_name] = $custrs;
	        }
	       //$return['customerscompany'] = $customerscompany;
	       // $return['customersdisplay_name'] = $customersdisplay_name;
           	// $smquery = "SELECT id, salesman_name FROM salesmen WHERE company_id = ".$get['company_id']." AND is_active = 0";
            // $smres = $callConfig->getAllRows($smquery);
            // foreach($smres as $smres1){
	        // 	$salesmendetails[$smres1->salesman_name] = $smres1->id;
	        // }

	        $itemquery = "SELECT id, sku, item_name, sale_uom FROM items WHERE company_id = ".$get['company_id']." AND is_active = 0";
            $itemres = $callConfig->getAllRows($itemquery);
            foreach($itemres as $itemre){
	        	$itemssku[$itemre->sku] = $itemre;
	        	$itemsitemname[$itemre->item_name] = $itemre;
	        }

	        $tax_authorities_rates_splitsql = "SELECT id, split_name, (tax_rate/100) as tax_rate, account_code, split_type, sales_account_code FROM `tax_authorities_rates_split` WHERE company_id = '".$post['company_id']."' AND is_active =0";
	        $tax_authorities_rates_splitres1 = $callConfig->getAllRows($tax_authorities_rates_splitsql);
	        //$return['tax_authorities_rates_splitres1'] = $tax_authorities_rates_splitsql;
	        foreach($bill_no_array as $billno=>$billitems){
	        	$first = $billitems[0];
	        	

	        	//$userdeatil = (count((array)$application_usersuser_name[$first->user_name])!='0')?$application_usersuser_name[$first->user_name]:$application_usersdiplay_name[$first->user_name]; 
		       	$salemannameid = NULL;
	        	// if(isset($salesmendetails[$first->user_name])){
	        	// 	$salemannameid = $salesmendetails[$first->user_name]; 	
	        	// }else{
	        	// 	$insertsalesmenary = array('company_id'=>$post['company_id'],'salesman_name'=>$first->user_name,'is_active'=>0,'current_status'=>0,'created_date'=>date('Y-m-d H:i:s'),'created_by'=>$get['user_id']);
				// 	$salesmenres = $callConfig->insertRecord('salesmen', $insertsalesmenary);
				// 	$salemannameid = $salesmenres['LASTID']; 
				// 	$salesmendetails[$first->user_name] = $salemannameid;
	        	// }
	        	
		       	
	        	if(isset($customerscompany[$first->party_name])){
	        		$custdeatil = $customerscompany[$first->party_name];
	        	}elseif(isset($customersdisplay_name[$first->party_name])){
	        		$custdeatil = $customersdisplay_name[$first->party_name]; 		
	        	}else{
	        		$insertcustomersary = array('company_id'=>$post['company_id'],'company'=>$first->party_name,'display_name'=>$first->party_name,'email'=>'','billing_address'=>$first->address,'shipping_address'=>$first->address,'billing_post_code'=>$first->pin_code,'shipping_post_code'=>$first->pin_code,'phone'=>$first->mobile_no,'contact_person'=>$first->user_name,'is_active'=>0,'current_status'=>0,'created_date'=>date('Y-m-d H:i:s'),'created_by'=>$post['user_id']);
					$customerres = $callConfig->insertRecord('customers', $insertcustomersary);
					
					$custdeatilary = array('id'=>$customerres['LASTID'],'company'=>$first->party_name,'display_name'=>$first->party_name,'email'=>'','billing_address'=>$first->address,'shipping_address'=>$first->address,'billing_post_code'=>$first->pin_code,'shipping_post_code'=>$first->pin_code,'phone'=>$first->mobile_no); 
					$custdeatil = (object)$custdeatilary;
					$customerscompany[$first->party_name] = $custdeatil;
	        	}

		       	
		       	$saleinvitem = array('amount'=>0,'total_amount'=>0,'total_tax_amount'=>0,'discount_amount'=>0);
		       	foreach($billitems as $itmlist){
		       		$saleinvitem['amount'] += $itmlist->plan_amount;
		       		$saleinvitem['total_amount'] += $itmlist->bill_amount;
		       		$cgstamt = ($itmlist->cgst !="")?$itmlist->cgst:0;
		       		$sgstamt = ($itmlist->sgst !="")?$itmlist->sgst:0;
		       		$totaltax = $cgstamt+$sgstamt;
		       		$saleinvitem['total_tax_amount'] += $totaltax;
		       		$saleinvitem['discount_amount'] += $itmlist->discount_amount;

		       		if(count((array)$tax_authorities_rates_splitres1) !=0){
				       	$tax_authorities_rates_splitres = json_decode(json_encode($tax_authorities_rates_splitres1), true);
						$return['tax_authorities_rates_splitres'] = $tax_authorities_rates_splitres;
				       
						//if($post['originator_state_id'] == $post['supplier_state_id']){ 
							$tax_flag = "S";
							$split_1_name = $tax_authorities_rates_splitres[0]['split_name'];
						    $split_2_name = $tax_authorities_rates_splitres[1]['split_name'];
						    $split_1 = $tax_authorities_rates_splitres[0]['tax_rate'];
						    $split_2 = $tax_authorities_rates_splitres[1]['tax_rate'];
						    if(in_array($table, (array)$sales_tables)){
							    $split_1_gl_account_code =  $tax_authorities_rates_splitres[0]['sales_account_code'];
							    $split_2_gl_account_code =  $tax_authorities_rates_splitres[1]['sales_account_code'];
							}else{
							    $split_1_gl_account_code =  $tax_authorities_rates_splitres[0]['account_code'];
							    $split_2_gl_account_code =  $tax_authorities_rates_splitres[1]['account_code'];
							}
						/**}else{
							$tax_flag = "I";
							$split_1_name = $tax_authorities_rates_splitres[2]['split_name'];
						    $split_1 = $tax_authorities_rates_splitres[2]['tax_rate'];
						    if(in_array($table, (array)$sales_tables)){
						    	$split_1_gl_account_code =  $tax_authorities_rates_splitres[2]['sales_account_code'];
							}else{
								$split_1_gl_account_code =  $tax_authorities_rates_splitres[2]['account_code'];
							}
						} **/
						$return['split_1_name'] = $split_1_name;
						$return['split_1'] = $split_1;
					}

		       		if($itmlist->sgst !='0' AND $itmlist->sgst!=''){ 
						if($tax_flag == "S"){
							$perc = ($totaltax/$itmlist->plan_amount)*100;
							$txperc1 = $perc*$split_1;
							$txvalc1 += $totaltax*$split_1;
							$txperc2 = $perc*$split_2;
							$txvalc2 += $totaltax*$split_2;
						}else{
							$txperc1 = $post['tax'][$key]*$split_1;
							$txvalc1 += $post['ptax_amount'][$key]*$split_1;
						}
					}
		       	}

				
				$total_tax_split_details = array();
				//if($txperc1 == '0'){
					if($tax_flag == "S"){
						$total_tax_split_details = array(array('tax_name'=>$split_1_name,'tax_percentage'=>$txperc1,'tax_amount'=>$txvalc1,'account_code'=>$split_1_gl_account_code),array('tax_name'=>$split_2_name,'tax_percentage'=>$txperc2,'tax_amount'=>$txvalc2,'account_code'=>$split_2_gl_account_code));  
					}else{
						$total_tax_split_details = array(array('tax_name'=>$split_1_name,'tax_percentage'=>$txperc1,'tax_amount'=>$txvalc1,'account_code'=>$split_1_gl_account_code)); 
					}
				//}
				$return['total_tax_split_details'] = $total_tax_split_details;

				$total_tax_split_details_json = json_encode($total_tax_split_details);

				$payment_term_historyary[] = array('amount'=>$saleinvitem['total_amount'],'due_date'=>$this->dateconvertdbformat($first->bill_date),'extra_amt'=>'0','extra_date'=>'','percentage'=>'100','tax_amount'=>$totaltax,'without_tax_amount'=>$saleinvitem['amount']);
				
				$payment_term_history = json_encode($payment_term_historyary);

				$party_additional_detail = array('gstin'=>$first->client_gst,'pan_no'=>$custdeatil->pan_no);
				$party_additional_details = json_encode((array)$party_additional_detail);

		       	$sales_invoice_data = array();
	            $sales_invoice_data['transaction_number'] = $this->toGetApplicationSerialNumber('sales_invoices');
	            $sales_invoice_data['transaction_date'] = $this->dateconvertdbformat($first->bill_date);
	            $sales_invoice_data['currency_id'] = 93;
	            $sales_invoice_data['currency_rate'] = 1;
	            $sales_invoice_data['originator'] = $post['user_id'];
	            $sales_invoice_data['party_id'] = $custdeatil->id;
	            $sales_invoice_data['salesmen_id'] =$salemannameid;
	            $sales_invoice_data['party_name'] = $custdeatil->company;
	            $sales_invoice_data['party_email'] = $custdeatil->email;
	            $sales_invoice_data['party_phone'] = $first->mobile_no;
	            $sales_invoice_data['party_address'] = $custdeatil->shipping_address;
	            $sales_invoice_data['party_ship_adderss'] = $custdeatil->shipping_address;
	            $sales_invoice_data['originator_state_id'] = 4008;
	            $sales_invoice_data['supplier_state_id'] = 4008;
	            $sales_invoice_data['amount'] = $saleinvitem['amount'];
	            $sales_invoice_data['tax_amount'] = $saleinvitem['total_tax_amount'];
	            $sales_invoice_data['discount_amount'] = $saleinvitem['discount_amount'];
	            $sales_invoice_data['total_amount'] = $saleinvitem['total_amount'];
	            $sales_invoice_data['due_date'] = $this->dateconvertdbformat($first->bill_date);
	            $sales_invoice_data['pending_amount'] = $saleinvitem['total_amount'];
	            $sales_invoice_data['company_id'] = $get['company_id'];
	            $sales_invoice_data['current_status'] = ST_APPROVED;
	            $sales_invoice_data['is_active'] = '0';
	            $sales_invoice_data['created_date'] = date('Y-m-d H:i:s');
	            $sales_invoice_data['created_by'] = $get['user_id'];
	            $sales_invoice_data['total_tax_split_details'] = $total_tax_split_details_json;
				$sales_invoice_data['payment_term_history'] = $payment_term_history;
				$sales_invoice_data['party_additional_details'] = $party_additional_details;
				$sales_invoice_data['doc_ref_no'] = $first->bill_no;
				$sales_invoice_data['doc_ref_date'] = $this->dateconvertdbformat($first->bill_date);
				$sales_invoice_data['ref_type'] = 'sales_invoice_data_parent';
				$sales_invoice_data['ref_id'] = $first->id;
				$sales_invoice_data['ref_no'] = $first->bill_no;
				$sales_invoice_data['ref_date'] = $this->dateconvertdbformat($first->bill_date);

				$sales_invoice_data['payment_terms'] = 1;
	            $return['sales_invoice_data'][] = $sales_invoice_data;
	            // Insert sales invoice
	           	$res1 = $callConfig->insertRecord('sales_invoices', $sales_invoice_data);
	           	$return['res'] = $res1;

	        

	           	if($res1['status'] == 'sucess'){
	           	   foreach($billitems as $itmlist){
	             		
	           	   		if($itemssku[$itmlist->plan_name]!=""){
			        		$itemdetail = $itemssku[$itmlist->plan_name];
			        	}elseif($itemsitemname[$itmlist->plan_name]!=""){
			        		$itemdetail = $itemsitemname[$itmlist->plan_name]; 		
			        	}else{
			        		$insertitemary = array('company_id'=>$post['company_id'],'sku'=>$itmlist->plan_name,'item_name'=>$itmlist->plan_name,'is_active'=>0,'current_status'=>0,'created_date'=>date('Y-m-d H:i:s'),'created_by'=>$post['user_id']);
							$itemsres = $callConfig->insertRecord('items', $insertitemary);
							
							$itmary = array('id'=>$itemsres['LASTID'],'item_name'=>$itmlist->plan_name); 
							$itemdetail = (object)$itmary;

							$itemssku[$itmlist->plan_name] = $itemdetail;
			        	}
					    $item_data = [];

	                   	// $req_date = $$this->dateconvertdbformat($itmlist->bill_date);
						// $mainitem = $this->togetitemvalues($itemdetail->id);
						// $day_rate = $this->togetitemwadayrate($itemdetail->id, $post['company_id']);
						// $fifo_day_rate = $this->togetitemfifodayrate($itemdetail->id, $get['company_id']);
						// $lifo_day_rate = $this->togetitemlifodayrate($itemdetail->id, $get['company_id']);
						// $wa_day_rate = $day_rate;
						// $wa_day_rate = ($wa_day_rate != '0' AND $wa_day_rate !="")?$wa_day_rate:$post['price'][$key];
						// $fifo_day_rate = ($fifo_day_rate != '0' AND $fifo_day_rate !="")?$fifo_day_rate:$post['price'][$key];
						// $lifo_day_rate = ($lifo_day_rate != '0' AND $lifo_day_rate !="")?$lifo_day_rate:$post['price'][$key];

						//$descriptions = $itmlist->site." ".$itmlist->building_name." ".$itmlist->zone_name." ".$itmlist->operator_name." ".$itmlist->franchisee_name;

						$descriptions = $itmlist->site." ".$itmlist->building_name." ".$itmlist->zone_name." ".$itmlist->operator_name." ".$itmlist->franchisee_name." Plan Activation Date - ".$itmlist->plan_activation_date." To ".$itmlist->expiry_date;
						$itemfieldnames=array('company_id'=>$post['company_id'],'transaction_id'=>$res1['LASTID'],'item_id'=>$itemdetail->id,'item_name'=>$itemdetail->item_name,'description'=>$descriptions,'purchase_uom'=>31,'item_quantity'=>1,'item_price'=>$itmlist->plan_amount,'item_amount'=>$itmlist->plan_amount,'tax_amount'=>($itmlist->sgst + $itmlist->cgst),'total_amount'=>$itmlist->bill_amount,'discount_amount'=>$itmlist->discount_amount,'req_date'=>$req_date,'account_code'=>'','item_wa_cost'=>$wa_day_rate,'item_fifo_cost'=>$fifo_day_rate,'item_lifo_cost'=>$lifo_day_rate,'pending_qty'=>1,'is_active'=>'0','current_status'=>'0','ref_transaction_item_table_id'=>'');
	                    $res2 = $callConfig->insertRecord('si_items', $itemfieldnames);
	                    $return['res_item'][] = $res2;


						if($itmlist->sgst !='0' AND $itmlist->sgst!=''){ 
							$perc = (($itmlist->sgst + $itmlist->cgst)/$itmlist->plan_amount)*100;


							$minDiff = PHP_INT_MAX;
							$array = [5, 8, 12, 18, 28];
						    foreach ($array as $value) {
						        $diff = abs($value - $perc);
						        if ($diff < $minDiff) {
						            $minDiff = $diff;
						            $nearest = $value;
						        }
						    }
						    $hsnnearest = $hsnary[$nearest];

						    $taxfield=array('company_id'=>$post['company_id'],'transaction_id'=>$res1['LASTID'],'transaction_item_id'=>$itemdetail->id,'tax_id'=>$hsnnearest['hsn_id'],'tax_name'=>$hsnnearest['hsn_name'],'amount'=>$itmlist->plan_amount,'tax_amount'=>($itmlist->sgst + $itmlist->cgst),'total_amount'=>$itmlist->bill_amount,'tax_type'=>$tax_flag,'tax_value'=>$hsnnearest['percentage'],'is_active'=>'0','current_status'=>'0','transaction_item_row_id'=>$res2['LASTID']);

							//$hsnary
							if($tax_flag == "S"){
								$txper1 = $perc*$split_1;
								$txval1 = ($itmlist->sgst + $itmlist->cgst)*$split_1;
								$txper2 = $perc*$split_2;
								$txval2 = ($itmlist->sgst + $itmlist->cgst)*$split_2;
								$tax_split_details = array(array('tax_name'=>$split_1_name,'tax_percentage'=>$txper1,'tax_amount'=>$txval1,'account_code'=>$split_1_gl_account_code),array('tax_name'=>$split_2_name,'tax_percentage'=>$txper2,'tax_amount'=>$txval2,'account_code'=>$split_2_gl_account_code));  
								$taxfield['tax_split_details'] = json_encode($tax_split_details);
							}else{
								$txper1 = $post['tax'][$key]*$split_1;
								$txval1 = ($itmlist->sgst + $itmlist->cgst)*$split_1;
								$tax_split_details = array(array('tax_name'=>$split_1_name,'tax_percentage'=>$txper1,'tax_amount'=>$txval1,'account_code'=>$split_1_gl_account_code)); 
								$taxfield['tax_split_details'] = json_encode($tax_split_details);
							}
							$itax_id2=$callConfig->insertRecord('si_item_taxes',$taxfield);
							$return['res_itm_tax'][] = $taxfield;
						}

	                }
	         	}

	         	$query4 = "UPDATE sales_invoice_data_excel SET table_name ='sales_invoices', table_id = '".$res1['LASTID']."',comments = '',current_status=".ST_APPROVED." WHERE bill_no = '".$first->bill_no."'";
				$res323 = $callConfig->executeQuery($query4);

	        } 
	    }
	    return $return;
	}
	//Koteswar End
	//OOmkar Data uploaded - 23-06-2025
	function importleadcontactCSV($get, $post)
	{
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$return['post'] = $post;
		$data = array('file_name' => $post['file_name'],'import_type' => $post['import_type'],'original_file_name' => $post['original_file_name'],'company_id' => $get['company_id'],'source_type' => $post['source_type'],'current_status' => '0','is_active' => '0','created_date' => date('Y-m-d H:i:s'),'created_by' => $get['user_id']);
		$res = $callConfig->insertRecord('crm_lead_and_contact_imports', $data);
		$parent_id = $res['LASTID'];
		if (!$parent_id) {
			return ['status' => 'error','message' => 'Failed to create import file record.'];
		}
		// Read and process CSV
		$csvData = base64_decode($post['filedata']);
		$rows = explode("\n", $csvData);
		$headerSkipped = false;
		$seenPhones = [];
		// Counters
		$inserted = 0;
		$duplicates = 0;
		$failed = 0;
		foreach ($rows as $line) {
			if (!$headerSkipped) {
				$headerSkipped = true;
				continue;
			}
			$columns = str_getcsv($line, ",", '"');
			if (count($columns) < 8 || count(array_filter($columns)) === 0) {
				continue;
			}
			$phone = trim($columns[3]);
			$officePhone = trim($columns[4]);

			// Check if it's a duplicate (within this file)
			$is_duplicate = (in_array($phone, $seenPhones) || in_array($officePhone, $seenPhones)) ? 1 : 0;

			if (!empty($phone))
				$seenPhones[] = $phone;
			if (!empty($officePhone))
				$seenPhones[] = $officePhone;

			if ($is_duplicate)
				$duplicates++;

			$insert_array = array(
				'first_name' => trim($columns[0]), 'last_name' => trim($columns[1]), 'display_name' => trim($columns[0] . ' ' . $columns[1]), 'email' => trim($columns[2]), 'phone' => $phone, 'office_phone' => $officePhone, 'address' => trim($columns[5]), 'company_name' => trim($columns[6]), 'campaign_name' => trim($columns[7]), 'crm_import_file_id' => $parent_id, 'company_id' => $get['company_id'], 'current_status' => '0', 'is_active' => '0', 'is_duplicate' => $is_duplicate, 'created_date' => date('Y-m-d H:i:s'), 'created_by' => $get['user_id']
			);

			$res1 = $callConfig->insertRecord('crm_lead_and_contact_import_items', $insert_array);

			if (isset($res1['LASTID']) && $res1['LASTID'] > 0) {
				$inserted++;
			} else {
				$failed++;
			}
		}
		$return['status'] = 'success';
		$return['message'] = "Data import complete.";
		$return['rid'] = $parent_id;
		$return['summary'] = ['total_rows' => count($rows) - 2, 'inserted' => $inserted, 'duplicates' => $duplicates, 'failed' => $failed];
		return $return;
	}
	function getlistcontactleaddata($get)
	{
		global $callConfig;
		$return['get'] = $get;

		$query = "SELECT id, file_name, import_type, original_file_name, source_type FROM crm_lead_and_contact_imports WHERE company_id = '" . $get['company_id'] . "' AND id = '" . $get['rid'] . "' AND is_active = 0";
		$data = $callConfig->getAllRows($query);
		$return['crm_import_files'] = $data;
		$query = "SELECT id, crm_import_file_id, display_name, first_name, last_name, email, phone, office_phone, address, company_name, campaign_name, is_duplicate FROM crm_lead_and_contact_import_items WHERE company_id = '" . $get['company_id'] . "' AND crm_import_file_id = '" . $get['rid'] . "' AND is_active = 0";
		// $return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['crm_import_file_rows'] = $data;
		return $return;
	}

	function insertcontactdata($get, $post){
		global $callConfig;
		$return = [];
		$rows = $post['rows'] ?? [];
		$files = $post['files'][0] ?? [];
		$inserted = 0;
		$duplicates = 0;
		$failed = 0;
		$seenPhones = [];
		foreach ($rows as $row) {
			$phone = trim($row['phone'] ?? '');
			$officePhone = trim($row['office_phone'] ?? '');
			$is_duplicate = (in_array($phone, $seenPhones) || in_array($officePhone, $seenPhones)) ? 1 : 0;
			if (!empty($phone)) $seenPhones[] = $phone;
			if (!empty($officePhone)) $seenPhones[] = $officePhone;
			if ($is_duplicate) $duplicates++;
			$contact = ['company_id' => $get['company_id'], 'first_name' => $row['first_name'] ?? '', 'middle_name' => '', 'last_name' => $row['last_name'] ?? '','display_name' => $row['display_name'] ?? '', 'email_id' => $row['email'] ?? '', 'phone' => $phone, 'office_phone' => $officePhone, 'address' => $row['address'] ?? '', 'company_name' => $row['company_name'] ?? '', 'originator' => $get['user_id'], 'contact_source' => $files['source_type'] ?? '', 'image' => null, 'is_active' => '0', 'current_status' => '0', 'created_date' => date('Y-m-d H:i:s'), 'created_by' => $get['user_id']];
			$result = $callConfig->insertRecord('crm_contacts', $contact);
			if (isset($result['LASTID']) && $result['LASTID'] > 0) {
				$inserted++;
			} else {
				$failed++;
			}
		}
		return ['status' => 'success','message' => 'Contacts imported successfully.','summary' => ['total_rows' => count($rows), 'inserted' => $inserted, 'duplicates' => $duplicates, 'failed' => $failed]];
	}
	

	function insertleaddata($get, $post){
		global $callConfig;
		$return = [];
		$rows = $post['rows'] ?? [];
		$files = $post['files'][0] ?? [];
		$inserted = 0;
		$duplicates = 0;
		$failed = 0;
		$seenPhones = [];
		foreach ($rows as $row) {
			$phone = trim($row['phone']);
			$officePhone = trim($row['office_phone']);
			$is_duplicate = (in_array($phone, $seenPhones) || in_array($officePhone, $seenPhones)) ? 1 : 0;
			if (!empty($phone))
				$seenPhones[] = $phone;
			if (!empty($officePhone))
				$seenPhones[] = $officePhone;
			if ($is_duplicate)
				$duplicates++;
			$lead = ['company_id' => $get['company_id'], 'first_name' => $row['first_name'] ?? '', 'middle_name' => '', 'last_name' => $row['last_name'] ?? '', 'display_name' => $row['display_name'] ?? '', 'email_id' => $row['email'] ?? '', 'phone' => $phone, 'office_phone' => $officePhone, 'address' => $row['address'] ?? '', 'company_name' => $row['company_name'] ?? '', 'originator' => $get['user_id'], 'contact_source' => $files['source_type'] ?? '', 'image' => null, 'is_active' => '0', 'current_status' => '0', 'created_date' => date('Y-m-d H:i:s'), 'created_by' => $get['user_id']];
			$result = $callConfig->insertRecord('crm_leads', $lead);
			if (isset($result['LASTID']) && $result['LASTID'] > 0) {
				$inserted++;
			} else {
				$failed++;
			}
		}
		return ['status' => 'success','summary' => ['total_rows' => count($rows), 'inserted' => $inserted, 'duplicates' => $duplicates, 'failed' => $failed]];
	}
	function getleadcontactsourcetype($get){
		global $callConfig;
		$return['get'] = $get;
		$query = "SELECT id, code, value, label FROM crm_general_codes WHERE company_id = '" . $get['company_id'] . "' AND code = 'SOURCETYPE' AND is_active = 0";
		$return['crm_general_codes'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['crm_general_codes'] = $data;
		return $return;
	} 

    // End feasibilty part By Kote - 25-06-2025


    /**
	function getFeasiblityData($get){
        global $callConfig;
        $return = array();
        $id = $get['rid'] ?? '';
        $lead_id = $get['lead_id'] ?? '';
        $feasibility_id = $get['feasibility_id'] ?? '';
        if (empty($id) && !empty($lead_id)) {
            // Lookup feasibility ID using lead_id
            $query = "SELECT id FROM feasibility_requirements 
                    WHERE lead_id = {$lead_id} AND company_id = {$get['company_id']} AND is_active = 0 
                    ORDER BY id DESC LIMIT 1";
                    $return['feasibility_types_query'] = $query;
            $row = $callConfig->getRow($query);
            if (!empty($row)) {
                $id = $row->id;  
            }
        }
        if (isset($id) && $id != '') {
            $query = "SELECT * FROM feasibility_requirements WHERE id = ".$id." AND company_id=".$get['company_id']." AND is_active=0";
            $feasibility_requirement_res = $callConfig->getRow($query);  
            $return['feasibility_requirement_res'] = $feasibility_requirement_res;
        }
        if( (isset($feasibility_id) && $feasibility_id != '')){
            $whr = '';
            if($feasibility_id != ''){
                $whr = "  feasibility_id = ".$feasibility_id." AND ";
            }
            $query = "SELECT * FROM lead_feasibility_evaluations WHERE ".$whr." company_id=".$get['company_id']." AND is_active=0";
            $data = $callConfig->getRow($query);
            $return['feasibility_evaluation_res'] = $data;
            $query = "SELECT * FROM lead_feasibility_boms WHERE ".$whr." company_id=".$get['company_id']." AND is_active=0";
            $data = $callConfig->getAllRows($query);
            $feasibility_bom_res = array();
            foreach ($data as $row) {
                $feasibility_bom_res[] = $row;
            }
            $return['feasibility_bom_res'] = $feasibility_bom_res;
            $query = "SELECT * FROM lead_feasibility_checklists WHERE ".$whr." company_id=".$get['company_id']." AND is_active=0";
            $data = $callConfig->getAllRows($query);
            $feasibility_checklist_res = array();
            foreach ($data as $row) {
                $feasibility_checklist_res[] = $row;
            }
            $return['feasibility_checklist_res'] = $feasibility_checklist_res;
        }
        $query = "SELECT id,display_name FROM application_users WHERE  company_id=".$get['company_id']." AND is_active=0";
        $data = $callConfig->getAllRows($query);
        $users_res = array();
        foreach ($data as $row) {
            $users_res[$row->id] = $row->display_name;
        }
        $return['users_res'] = $users_res;
        $query = "SELECT id,type_name FROM feasibility_types WHERE company_id=".$get['company_id']." AND is_active=0";
        $data = $callConfig->getAllRows($query);
        $feasibility_types_res = array();
        foreach ($data as $row) {
            $feasibility_types_res[$row->id] = $row->type_name;
        }
        $return['feasibility_types_res'] = $feasibility_types_res;
        return $return;
    }
    function getFeasibilityFields($get){
        global $callConfig;
        $return = array();
        if(isset($get['rid']) && $get['rid'] != ''){
            $query = "SELECT * FROM feasibility_fields WHERE feasibility_type_id = ".$get['rid']." AND company_id=".$get['company_id']." AND is_active=0";
            $data = $callConfig->getAllRows($query);
            $feasibility_fields_res = array();
            $html = "";
            foreach ($data as $row) {
                $row = (array) $row; // Convert stdClass to array
                // Prepare HTML for each field
                $label = $row['field_label'];
                $code = $row['code'];
                $type = $row['field_type'];
                $length = $row['field_length'];
                $values = json_decode($row['field_value'], true);
                $class = '';
                if($type === 'number'){
                    $class = 'text-right';
                }
                $html .= "<div class='$length mb-3'><label class='form-label'>$label ";
                if ($type === 'text' || $type === 'number' || $type === 'date') {
                    $html .= "<span class='text-danger'>*</span>";
                } 
                $html .="</label>";

                if ($type === 'text' || $type === 'number' || $type === 'date') {

                    $html .= "<input type='$type' name='$code' class='form-control $class' required>";
                } elseif ($type === 'select') {
                    $html .= "<select name='$code' class='form-select'>";
                    foreach ($values as $v) {
                    $html .= "<option value='$v'>$v</option>";
                    }
                    $html .= "</select>";
                } elseif ($type === 'radio') {
                    foreach ($values as $v) {
                    $html .= "<div class='form-check '>
                                <input class='form-check-input' type='radio' name='$code' value='$v' id='{$code}_$v'>
                                <label class='form-check-label' for='{$code}_$v'>$v</label>
                                </div>";
                    }
                }
                $html .= "</div>";
                // $feasibility_fields_res[] = $row;
            }
            // $return['feasibility_fields_res'] = $feasibility_fields_res;
        }
        return $html;
    }
    function insertFeasibilityBomAndChecklist($get, $post){
        // print_r($get);
        // print_r($post); exit;
        global $callConfig;
        $return = array();
        $feasibility_id = $post['feasibility_id'];
        $updated_date = date("Y-m-d H:i:s");
        $updated_by = $get['user_id'];
        if($post['table_name'] == 'lead_feasibility_boms'){
            $query = "UPDATE lead_feasibility_boms  SET is_active = NULL, updated_date = '{$updated_date}', updated_by={$updated_by}  WHERE feasibility_id = {$feasibility_id}";
            $res = $callConfig->executeQuery($query);
            $return['update_res'] = $res;
            $item_name_arr = $post['item_name'];
            foreach ($item_name_arr as $i => $item_name) {
                $insert_array = array('company_id' => $get['company_id'],'feasibility_id' => $feasibility_id,'item_name' => $item_name,'quantity' => $_POST['quantity'][$i],'unit' => $_POST['unit'][$i],'estimated_cost' => $_POST['cost'][$i],'planned_request_by' => $this->dateconvertdbformat($_POST['planned_request_by'][$i]),'planned_return_by' => $this->dateconvertdbformat($_POST['planned_return_by'][$i]),'actual_request_by' => $this->dateconvertdbformat($_POST['actual_request_by'][$i]),'actual_return_by' => $this->dateconvertdbformat($_POST['actual_return_by'][$i]), 'is_active' => 0,'current_status' => 0,'status_date' => date("Y-m-d H:i:s"),'created_date' => date("Y-m-d H:i:s"),'created_by' => $get['user_id']
                );
                $res = $callConfig->insertRecord('lead_feasibility_boms', $insert_array);
            }
        }elseif($post['table_name'] == 'lead_feasibility_checklists'){
            $query = "UPDATE lead_feasibility_checklists  SET is_active = NULL , updated_date = '{$updated_date}', updated_by={$updated_by}  WHERE feasibility_id = {$feasibility_id}";
            $res = $callConfig->executeQuery($query);
            $return['update_res'] = $res;
            $item_name_arr = $post['checklist_item_name'];
            foreach ($item_name_arr as $i => $item_name) {
                $insert_array = array('company_id' => $get['company_id'],'feasibility_id' => $feasibility_id,'item_name' => $item_name,'item_type' => $_POST['checklist_type'][$i],'is_done' => $_POST['checklist_is_done'][$i],'comment' => $_POST['checklist_comment'][$i],'is_active' => 0,'current_status' => 0,'status_date' => date("Y-m-d H:i:s"),'created_date' => date("Y-m-d H:i:s"), 'created_by' => $get['user_id']);
                $res = $callConfig->insertRecord('lead_feasibility_checklists', $insert_array);
            }
        }
        return $return;
    }
    function getFeasibilityReport($get) {
        global $callConfig;
        $feasibility_id = $get['feasibility_id'];
        $company_id = $get['company_id'];
        $report = [];
        // === 1. Feasibility Requirement Section ===
        $reqQuery = "SELECT * FROM feasibility_requirements  WHERE id = $feasibility_id AND company_id = $company_id AND is_active = 0";
        $requirement = $callConfig->getRow($reqQuery);
        $typeId = $requirement->feasibility_type_id ?? 0;
        $typeName = '';
        if ($typeId) {
            $typeQuery = "SELECT type_name FROM feasibility_types  WHERE id = $typeId AND company_id = $company_id AND is_active = 0";
            $res = $callConfig->getRow($typeQuery);
            $typeName = $res->type_name ??'';
        }
        $reqJson = json_decode($requirement->requirement_json ?? '{}', true);
        $fields = [];
        if (!empty($reqJson)) {
            $fieldQuery = "SELECT code, field_label FROM feasibility_fields WHERE feasibility_type_id = $typeId AND company_id = $company_id AND is_active = 0";
            $fieldMap = [];
            foreach ($callConfig->getAllRows($fieldQuery) as $row) {
                $fieldMap[$row->code] = $row->field_label;
            }
            foreach ($reqJson as $code => $value) {
                $label = $fieldMap[$code] ?? $code;
                $fields[$label] = $value;
            }
        }
        $report['requirement'] = ['feasibility_type' => $typeName,'fields' => $fields];
        // === 2. Evaluation Section ===
        $evalQuery = "SELECT * FROM lead_feasibility_evaluations WHERE feasibility_id = $feasibility_id AND company_id = $company_id AND is_active = 0";
        $report['evaluation'] = $callConfig->getRow($evalQuery);

        // === 3. BOM Section ===
        $bomQuery = "SELECT * FROM lead_feasibility_boms  WHERE feasibility_id = $feasibility_id AND company_id = $company_id AND is_active = 0";
        $report['bom'] = $callConfig->getAllRows($bomQuery);

        // === 4. Checklist Section ===
        $chkQuery = "SELECT * FROM lead_feasibility_checklists WHERE feasibility_id = $feasibility_id AND company_id = $company_id AND is_active = 0";
        $report['checklist'] = $callConfig->getAllRows($chkQuery);

        // === 5. Files Section ===
        $fileQuery = "SELECT actual_file_name, file_name FROM application_attachments  WHERE transaction_id = $feasibility_id AND company_id = $company_id  AND application_table_name = 'feasibility_requirements' AND is_active = 0";
        $report['files'] = $callConfig->getAllRows($fileQuery);
        return $report;
    }
    function checkBOMReturns($get){
        global $callConfig;
        $return = array();
        $fid = $get['feasibility_id'];
        $query = "SELECT item_name FROM lead_feasibility_boms WHERE feasibility_id = $fid AND (actual_return_by IS NULL OR actual_return_by = '')  AND company_id=".$get['company_id']." AND  is_active = 0";
        $rows = $callConfig->getAllRows($query);
        $items = array_map(fn($r) => $r->item_name, $rows);
        $return['unreturned_items'] = $items;
        return $return;
    }
    function closeFeasibility($get){
        global $callConfig;
        $return = array();
        $updated_date = date("Y-m-d H:i:s");
        $updated_by = $get['user_id'];
        $fid = $get['feasibility_id'];
        $query = "UPDATE feasibility_requirements SET current_status = ".ST_APPROVED.", updated_date = '{$updated_date}', updated_by={$updated_by}  WHERE id = $fid";
        $rows = $callConfig->executeQuery($query);
        $return['feasibility_requirements'] = $rows;
        return $rows;

    }
    
    function getFeasibilityDashboard($get){
        global $callConfig;
        $query = "SELECT 
            fr.id AS feasibility_id,
            fr.lead_id AS lead_id,
            ft.type_name AS feasibility_type,
            b.id AS bom_id,
            b.item_name,
            b.actual_return_by
        FROM feasibility_requirements fr
        LEFT JOIN feasibility_types ft  ON fr.feasibility_type_id = ft.id AND ft.company_id = " .$get['company_id']." AND  ft.is_active = 0
        LEFT JOIN lead_feasibility_boms b ON fr.id = b.feasibility_id AND b.company_id = " .$get['company_id']." AND  b.is_active = 0
        WHERE fr.current_status = ".ST_APPROVED."
        AND (b.actual_return_by IS NULL OR b.actual_return_by = '')
        AND fr.company_id = " .$get['company_id']." AND  fr.is_active = 0";
        $data = $callConfig->getAllRows($query);
        return $data;
    }
    **/

    // Feasibilty
    function getFeasiblityData($get){
        global $callConfig;
        $return = array();
        $id = $get['rid'] ?? '';
        $lead_id = $get['lead_id'] ?? '';
        $feasibility_id = $get['feasibility_id'] ?? '';
        if (empty($id) && !empty($lead_id)) {
            // Lookup feasibility ID using lead_id
            $query = "SELECT id FROM feasibility_requirements 
                    WHERE lead_id = {$lead_id} AND company_id = {$get['company_id']} AND is_active = 0 
                    ORDER BY id DESC LIMIT 1";
                    $return['feasibility_types_query'] = $query;
            $row = $callConfig->getRow($query);
            if (!empty($row)) {
                $id = $row->id;  
            }
        }
        if (isset($id) && $id != '') {
            $query = "SELECT * FROM feasibility_requirements WHERE id = ".$id." AND company_id=".$get['company_id']." AND is_active=0";
            $feasibility_requirement_res = $callConfig->getRow($query);  
            $return['feasibility_requirement_res'] = $feasibility_requirement_res;
        }
        if( (isset($feasibility_id) && $feasibility_id != '')){
        	$query = "SELECT * FROM feasibility_requirements WHERE id = ".$feasibility_id." AND company_id=".$get['company_id']." AND is_active=0";
            $feasibility_requirement_res = $callConfig->getRow($query);  
            $return['feasibility_requirement_res'] = $feasibility_requirement_res;
            
            $whr = '';
            if($feasibility_id != ''){
                $whr = "  feasibility_id = ".$feasibility_id." AND ";
            }
            $query = "SELECT * FROM lead_feasibility_evaluations WHERE ".$whr." company_id=".$get['company_id']." AND is_active=0";
            $data = $callConfig->getRow($query);
            $return['feasibility_evaluation_res'] = $data;
            $query = "SELECT * FROM lead_feasibility_boms WHERE ".$whr." company_id=".$get['company_id']." AND is_active=0";
            $data = $callConfig->getAllRows($query);
            $feasibility_bom_res = array();
            foreach ($data as $row) {
                $feasibility_bom_res[] = $row;
            }
            $return['feasibility_bom_res'] = $feasibility_bom_res;
            $query = "SELECT * FROM lead_feasibility_checklists WHERE ".$whr." company_id=".$get['company_id']." AND is_active=0";
            $data = $callConfig->getAllRows($query);
            $feasibility_checklist_res = array();
            foreach ($data as $row) {
                $feasibility_checklist_res[] = $row;
            }
            $return['feasibility_checklist_res'] = $feasibility_checklist_res;
        }
        $query = "SELECT id,display_name FROM application_users WHERE  company_id=".$get['company_id']." AND is_active=0";
        $data = $callConfig->getAllRows($query);
        $users_res = array();
        foreach ($data as $row) {
            $users_res[$row->id] = $row->display_name;
        }
        $return['users_res'] = $users_res;

        $query = "SELECT id,type_name FROM feasibility_types WHERE company_id=".$get['company_id']." AND is_active=0";
        $data = $callConfig->getAllRows($query);
        $feasibility_types_res = array();
        foreach ($data as $row) {
            $feasibility_types_res[$row->id] = $row->type_name;
        }
        $return['feasibility_types_res'] = $feasibility_types_res;

		$query = "SELECT id,uom_name, uom_short_code FROM unit_of_measurements WHERE  company_id=".$get['company_id']." AND is_active=0";
        $data = $callConfig->getAllRows($query);
        $uom_res = array();
        foreach ($data as $row) {
            $uom_res[$row->id] = $row;
        }
        $return['uom_res'] = $uom_res;

		$query = "SELECT id,item_name, purchase_uom, purchase_price FROM items WHERE  company_id=".$get['company_id']." AND is_active=0";
        $data = $callConfig->getAllRows($query);
        $items_res = array();
        foreach ($data as $row) {
            $items_res[$row->id] = $row;
        }
        $return['items_res'] = $items_res;

        return $return;
    }
    function getFeasibilityFields($get){
        global $callConfig;
        $return = array();
        if(isset($get['rid']) && $get['rid'] != ''){
            $query = "SELECT * FROM feasibility_fields WHERE feasibility_type_id = ".$get['rid']." AND company_id=".$get['company_id']." AND is_active=0";
            $data = $callConfig->getAllRows($query);
            $feasibility_fields_res = array();
            $html = "";

            foreach ($data as $row) {
                $row = (array) $row; // Convert stdClass to array
                // Prepare HTML for each field
                $label = $row['field_label'];
                $code = $row['code'];
                $type = $row['field_type'];
                $length = $row['field_length'];
                $values = json_decode($row['field_value'], true);
                $class = '';
                if($type === 'number'){
                    $class = 'text-right';
                }
				$mandatory = isset($row['mandatory']) ? (int)$row['mandatory'] : 0;
				$star = $mandatory === 0 ? " <span class='text-danger'>*</span>" : '';
				$requiredAttr = $mandatory === 0 ? 'required' : '';
                $html .= "<div class='$length mb-3'><label class='form-label'>$label $star </label>";
                

                if ($type === 'text' || $type === 'number' || $type === 'date') {

                    $html .= "<input type='$type' name='$code' class='form-control $class' $requiredAttr>";
                } elseif ($type === 'select') {
                    $html .= "<select name='$code' class='form-select' $requiredAttr>";
                    foreach ($values as $v) {
                    $html .= "<option value='$v'>$v</option>";
                    }
                    $html .= "</select>";
                } elseif ($type === 'radio') {
					$isFirst = true;
                    foreach ($values as $v) {
					$required = ($mandatory === 0 && $isFirst) ? 'checked' : '';
                    $html .= "<div class='form-check '>
                                <input class='form-check-input' type='radio' name='$code' value='$v' id='{$code}_$v' $required>
                                <label class='form-check-label' for='{$code}_$v'>$v</label>
                                </div>";
					$isFirst = false;
                    }
                }
                $html .= "</div>";
                // $feasibility_fields_res[] = $row;
            }
            // $return['feasibility_fields_res'] = $feasibility_fields_res;
        }
        return $html;
    }
    function insertFeasibilityBomAndChecklist($get, $post){
        // print_r($get);
        // print_r($post); exit;
        global $callConfig;
        $return = array();
        $feasibility_id = $post['feasibility_id'];
        $updated_date = date("Y-m-d H:i:s");
        $updated_by = $get['user_id'];
		if ($post['table_name'] == 'lead_feasibility_boms') {
			// Loop through the items
			$item_name_arr = $post['item_name'];
			$bom_row_id_arr = $post['bom_row_id'];  // Get the bom_row_id array

			foreach ($item_name_arr as $i => $item_name) {
				// Check if item_name is "others"
				if ($item_name == 'others') {
					// Insert the new item into the items table
					$newItemName = $post['new_item_name'][$i];  // Get the custom item name entered by the user
					if ($newItemName) {
						$insert_item_array = array(
							'company_id' => $get['company_id'],
							'item_name' => $newItemName,
							'purchase_uom' => $post['unit'][$i],
							'sale_uom' => $post['unit'][$i],
							'purchase_base_uom' => $post['unit'][$i],
							'sale_base_uom' => $post['unit'][$i],
							'purchase_price' => $post['cost'][$i],
							'sale_price' => $post['cost'][$i],
							'item_type' => 1,
							'is_active' => 0,
							'current_status' => 0,
							'status_date' => date("Y-m-d H:i:s"),
							'created_by' => $get['user_id'],
							'created_date' => date("Y-m-d H:i:s")
						);
						// Insert the new item and get the LASTID
						$insertItemResult = $callConfig->insertRecord('items', $insert_item_array);
						$newItemId = $insertItemResult['LASTID'];  // Get the last inserted item ID
					}
				} else {
					// Use the existing item ID for non-"others" items
					$newItemId = $item_name;
				}

				// Check if bom_row_id exists for update, otherwise insert new record
				$bom_row_id = $bom_row_id_arr[$i];
				if ($bom_row_id) {
					// Prepare the data for updating the record
					$update_array = array(
						'item_id' => $newItemId,
						'item_quantity' => $post['quantity'][$i],
						'unit' => $post['unit'][$i],
						'item_price' => $post['cost'][$i],
						'total_amount' => $post['total_cost'][$i],
						'planned_request_by' => $this->dateconvertdbformat($post['planned_request_by'][$i]),
						'planned_return_by' => $this->dateconvertdbformat($post['planned_return_by'][$i]),
						'actual_request_by' => $this->dateconvertdbformat($post['actual_request_by'][$i]),
						'actual_return_by' => $this->dateconvertdbformat($post['actual_return_by'][$i]),
						'updated_date' => $updated_date,
						'updated_by' => $updated_by
					);

					// Call the default function to update the record, passing the table name, the data array, and the condition (WHERE id = bom_row_id)
					$res = $callConfig->updateRecord('lead_feasibility_boms', $update_array, "id", $bom_row_id);
					$return['update_res'] = $res;

				} else {
					// Insert a new record if no bom_row_id exists
					$insert_array = array(
						'company_id' => $get['company_id'],
						'feasibility_id' => $feasibility_id,
						'item_id' => $newItemId,
						'item_quantity' => $post['quantity'][$i],
						'unit' => $post['unit'][$i],
						'item_price' => $post['cost'][$i],
						'total_amount' => $post['total_cost'][$i],
						'planned_request_by' => $this->dateconvertdbformat($post['planned_request_by'][$i]),
						'planned_return_by' => $this->dateconvertdbformat($post['planned_return_by'][$i]),
						'actual_request_by' => $this->dateconvertdbformat($post['actual_request_by'][$i]),
						'actual_return_by' => $this->dateconvertdbformat($post['actual_return_by'][$i]),
						'is_active' => 0,
						'current_status' => 0,
						'status_date' => date("Y-m-d H:i:s"),
						'created_date' => date("Y-m-d H:i:s"),
						'created_by' => $get['user_id']
					);
					$res = $callConfig->insertRecord('lead_feasibility_boms', $insert_array);
					$return['insert_res'] = $res;
				}
			}
		}elseif ($post['table_name'] == 'lead_feasibility_checklists') {
			$return = array();
			$updated_date = date("Y-m-d H:i:s");
			$updated_by = $get['user_id'];
			$feasibility_id = $post['feasibility_id'];

			// Loop through the checklist items
			$checklist_item_name_arr = $post['checklist_item_name'];
			$check_row_id_arr = $post['check_row_id'];  // Get the check_row_id array for checking the record ID

			foreach ($checklist_item_name_arr as $i => $item_name) {
				// Check if a check_row_id is available, if yes, perform an update
				$check_row_id = $check_row_id_arr[$i];
				
				// Prepare the data for the checklist update
				$update_array = array(
					'item_name' => $item_name,
					'item_type' => $post['checklist_type'][$i],
					'is_done' => $post['checklist_is_done'][$i],
					'comment' => $post['checklist_comment'][$i],
					'is_active' => 0,
					'current_status' => 0,
					'status_date' => date("Y-m-d H:i:s"),
					'updated_date' => $updated_date,
					'updated_by' => $updated_by
				);
				if ($check_row_id) {
					// If check_row_id exists, update the record
					$res = $callConfig->updateRecord('lead_feasibility_checklists', $update_array, "id", $check_row_id);
					$return['update_res'] = $res;
				} else {
					// If check_row_id does not exist, insert a new record
					$insert_array = array(
						'company_id' => $get['company_id'],
						'feasibility_id' => $feasibility_id,
						'item_name' => $item_name,
						'item_type' => $post['checklist_type'][$i],
						'is_done' => $post['checklist_is_done'][$i],
						'comment' => $post['checklist_comment'][$i],
						'is_active' => 0,
						'current_status' => 0,
						'status_date' => date("Y-m-d H:i:s"),
						'created_date' => date("Y-m-d H:i:s"),
						'created_by' => $updated_by
					);
					$res = $callConfig->insertRecord('lead_feasibility_checklists', $insert_array);
					$return['insert_res'] = $res;
				}
			}
		}
        return $return;
    }
    function getFeasibilityReport($get) {
        global $callConfig;
        $feasibility_id = $get['feasibility_id'];
        $company_id = $get['company_id'];
        $report = [];
        // === 1. Feasibility Requirement Section ===
        $reqQuery = "SELECT * FROM feasibility_requirements  WHERE id = $feasibility_id AND company_id = $company_id AND is_active = 0";
        $requirement = $callConfig->getRow($reqQuery);
        $report['feasibility_requirements'] = $requirement;

        $typeId = $requirement->feasibility_type_id ?? 0;
        $typeName = '';
        if ($typeId) {
            $typeQuery = "SELECT type_name FROM feasibility_types  WHERE id = $typeId AND company_id = $company_id AND is_active = 0";
            $res = $callConfig->getRow($typeQuery);
            $typeName = $res->type_name ??'';
        }
        $reqJson = json_decode($requirement->requirement_json ?? '{}', true);
        $fields = [];
        if (!empty($reqJson)) {
            $fieldQuery = "SELECT code, field_label FROM feasibility_fields WHERE feasibility_type_id = $typeId AND company_id = $company_id AND is_active = 0";
            $fieldMap = [];
            foreach ($callConfig->getAllRows($fieldQuery) as $row) {
                $fieldMap[$row->code] = $row->field_label;
            }
            foreach ($reqJson as $code => $value) {
                $label = $fieldMap[$code] ?? $code;
                $fields[$label] = $value;
            }
        }
        $report['requirement'] = ['feasibility_type' => $typeName,'fields' => $fields];
        // === 2. Evaluation Section ===
        $evalQuery = "SELECT * FROM lead_feasibility_evaluations WHERE feasibility_id = $feasibility_id AND company_id = $company_id AND is_active = 0";
        $report['evaluation'] = $callConfig->getRow($evalQuery);

        // === 3. BOM Section ===
        $bomQuery = "SELECT * FROM lead_feasibility_boms  WHERE feasibility_id = $feasibility_id AND company_id = $company_id AND is_active = 0";
        $report['bom'] = $callConfig->getAllRows($bomQuery);

        // === 4. Checklist Section ===
        $chkQuery = "SELECT * FROM lead_feasibility_checklists WHERE feasibility_id = $feasibility_id AND company_id = $company_id AND is_active = 0";
        $report['checklist'] = $callConfig->getAllRows($chkQuery);

        // === 5. Files Section ===
        $fileQuery = "SELECT actual_file_name, file_name FROM application_attachments  WHERE transaction_id = $feasibility_id AND company_id = $company_id  AND application_table_name = 'feasibility_requirements' AND is_active = 0";
        $report['files'] = $callConfig->getAllRows($fileQuery);

        // === 6. Unit Of measurement Section ===
        $chkQuery = "SELECT id, uom_name, uom_short_code FROM unit_of_measurements WHERE  company_id = $company_id AND is_active = 0";
        $query_res = $callConfig->getAllRows($chkQuery);
		$uom_res = array();
		foreach ($query_res as $row) {
			$uom_res[$row->id] = $row;
		}
		$report['uom_res'] = $uom_res;

		$chkQuery = "SELECT id, item_name FROM items WHERE  company_id = $company_id AND is_active = 0";
        $query_res = $callConfig->getAllRows($chkQuery);
		$items_res = array();
		foreach ($query_res as $row) {
			$items_res[$row->id] = $row->item_name;
		}
		$report['items_res'] = $items_res;
        return $report;
    }
    function checkBOMReturns($get){
        global $callConfig;
        $return = array();
        $fid = $get['feasibility_id'];
        $query = "SELECT i.item_name FROM lead_feasibility_boms bom, items i WHERE bom.item_id = i.id AND feasibility_id = $fid AND (actual_return_by IS NULL OR actual_return_by = '')  AND bom.company_id=".$get['company_id']." AND  bom.is_active = 0 AND i.is_active =0";
        $rows = $callConfig->getAllRows($query);
        $items = array_map(fn($r) => $r->item_name, $rows);
        $return['unreturned_items'] = $items;
        return $return;
    }
    function closeFeasibility($get){
        global $callConfig;
        $updated_date = date("Y-m-d H:i:s");
        $updated_by = $get['user_id'];
        $fid = $get['feasibility_id'];
        $query = "UPDATE feasibility_requirements SET current_status = ".ST_APPROVED.", updated_date = '{$updated_date}', updated_by={$updated_by}  WHERE id = $fid";
        $rows = $callConfig->executeQuery($query);
        return $rows;

    }
    function getFeasibilityDashboard($get){
        global $callConfig;
        $query = "SELECT 
            fr.id AS feasibility_id,
            fr.lead_id AS lead_id,
            ft.type_name AS feasibility_type,
            b.id AS bom_id,
            b.item_name,b.planned_return_by,
            b.actual_return_by,
            crmcon.display_name,
            itm.item_name
        FROM feasibility_requirements fr
        LEFT JOIN feasibility_types ft  ON fr.feasibility_type_id = ft.id AND ft.company_id = " .$get['company_id']." AND  ft.is_active = 0
        LEFT JOIN lead_feasibility_boms b ON fr.id = b.feasibility_id AND b.company_id = " .$get['company_id']." AND  b.is_active = 0
        LEFT JOIN crm_contacts crmcon  ON fr.company_id  = crmcon.company_id AND fr.lead_id = crmcon.id AND  b.is_active = 0
        LEFT JOIN items itm  ON b.company_id  = itm.company_id AND b.item_id = itm.id AND  b.is_active = 0
        WHERE fr.current_status = ".ST_APPROVED."
        AND (b.actual_return_by IS NULL OR b.actual_return_by = '')
        AND fr.company_id = " .$get['company_id']." AND  fr.is_active = 0";
        $data = $callConfig->getAllRows($query);
        return $data;
    }
    function updateReturnDate($get){
        global $callConfig;
        $updated_date = date("Y-m-d H:i:s");
        $updated_by = $get['user_id'];
        $bom_id = $get['bom_id'] ?? '';
        $actual_return_by = $get['actual_return_by'] ?? '';
        $bom_id = intval($bom_id);
        $actual_return_by = $this->dateconvertdbformat($actual_return_by); // sanitize
        $update_sql = "UPDATE lead_feasibility_boms SET actual_return_by = '{$actual_return_by}', updated_date = '{$updated_date}', updated_by={$updated_by} WHERE id = $bom_id";
        $result = $callConfig->executeQuery($update_sql);
        return $result;
    }
	function getFeasibilityTableAndFields($get) {
		global $callConfig;

		$html = "";
		if (isset($get['rid']) && $get['rid'] != '') {
			$query = "SELECT * FROM feasibility_fields WHERE feasibility_type_id = " . $get['rid'] . " AND company_id = " . $get['company_id'] . " AND is_active = 0";
			$data = $callConfig->getAllRows($query);

			if (!empty($data)) {
				$html .= "<div class='table-responsive mb-3'>
        <table class='table table-striped' id='feasibilityFieldsTable'>
        <thead><tr>
            <th width='20%'>Label</th>
            <th width='20%'>Type</th>
            <th width='20%'>Values (JSON)</th>
            <th width='20%'>Length (col)</th>
            <th width='10%'>Mandatory</th>
            <thwidth='0%'>Action</th>
        </tr></thead><tbody>";

				foreach ($data as $row) {
					$row = (array) $row;
					$isSelectable = in_array($row['field_type'], ['select', 'radio']);
					$mandatory = isset($row['mandatory']) ? (int) $row['mandatory'] : 0;
					$mandatoryBtnClass = $mandatory ? 'btn-success' : 'btn-warning';
					$mandatoryBtnText = $mandatory ? 'Unmandatory' : 'Mandatory';
					$mandatoryBtnData = $mandatory ? 1 : 0;

					$html .= "<tr>
        <td width='20%'>
            <input type='text' class='form-control' name='field_label[]' value='" . htmlspecialchars($row['field_label']) . "'>
            <input type='hidden' name='code[]' value='" . htmlspecialchars($row['code']) . "'>
            <input type='hidden' name='is_active[]' value='" . htmlspecialchars($row['is_active']) . "'>
        </td>
        <td width='20%'>
            <select class='form-select' name='field_type[]' onchange='handleFieldTypeChange(this);'>
                <option value='text' " . ($row['field_type'] == 'text' ? 'selected' : '') . ">Text</option>
                <option value='number' " . ($row['field_type'] == 'number' ? 'selected' : '') . ">Number</option>
                <option value='date' " . ($row['field_type'] == 'date' ? 'selected' : '') . ">Date</option>
                <option value='select' " . ($row['field_type'] == 'select' ? 'selected' : '') . ">Select</option>
                <option value='radio' " . ($row['field_type'] == 'radio' ? 'selected' : '') . ">Radio</option>
            </select>
        </td>
        <td width='20%'>
            <input type='text' class='form-control field-value-input' name='field_value[]' value='" . htmlspecialchars($row['field_value']) . "' " . ($isSelectable ? 'readonly' : '') . "> 
            <button type='button' class='btn btn-sm btn-success mt-3 add-options-btn' style='" . ($isSelectable ? '' : 'display:none;') . " z-index:2;' onclick='openOptionsModal(this)'>Edit Options</button>
        </td>
        <td width='20%'>
            <select class='form-select js-example-basic-single' name='field_length[]'>
                <option value='col-md-1' " . ($row['field_length'] == 'col-md-1' ? 'selected' : '') . ">col-md-1</option>
                <option value='col-md-2' " . ($row['field_length'] == 'col-md-2' ? 'selected' : '') . ">col-md-2</option>
                <option value='col-md-3' " . ($row['field_length'] == 'col-md-3' ? 'selected' : '') . ">col-md-3</option>
                <option value='col-md-4' " . ($row['field_length'] == 'col-md-4' ? 'selected' : '') . ">col-md-4</option>
                <option value='col-md-5' " . ($row['field_length'] == 'col-md-5' ? 'selected' : '') . ">col-md-5</option>
                <option value='col-md-6' " . ($row['field_length'] == 'col-md-6' ? 'selected' : '') . ">col-md-6</option>
                <option value='col-md-7' " . ($row['field_length'] == 'col-md-7' ? 'selected' : '') . ">col-md-7</option>
                <option value='col-md-8' " . ($row['field_length'] == 'col-md-8' ? 'selected' : '') . ">col-md-8</option>
                <option value='col-md-9' " . ($row['field_length'] == 'col-md-9' ? 'selected' : '') . ">col-md-9</option>
                <option value='col-md-10' " . ($row['field_length'] == 'col-md-10' ? 'selected' : '') . ">col-md-10</option>
                <option value='col-md-11' " . ($row['field_length'] == 'col-md-11' ? 'selected' : '') . ">col-md-11</option>
                <option value='col-md-12' " . ($row['field_length'] == 'col-md-12' ? 'selected' : '') . ">col-md-12</option>
            </select>
        </td>
        <td width='10%'>
            <button type='button' class='btn $mandatoryBtnClass btn-sm toggle-mandatory-btn' onclick='toggleMandatory(this)' data-mandatory='$mandatoryBtnData'>$mandatoryBtnText</button>
            <input type='hidden' name='mandatory[]' value='$mandatory'>
        </td>
        <td width='10%'><button type='button' class='btn btn-danger btn-sm' onclick='removeFieldRow(this)'><i class='fa fa-trash'></i></button></td>
    </tr>";
				}

				$html .= "</tbody></table>
						<div class='d-flex justify-content-between mt-3'>
							<button type='button' class='btn btn-success mt-2' onclick='addNewFieldRow()'>+ Add New Field</button>
        					<button type='button' class='btn btn-success mt-2' onclick='saveCustomFields()'>Save Fields</button>
						</div>
					</div>";

				// Field preview section (unchanged)
				$html .= "<div class='row' id='dynamic_preview'>";
			

	foreach ($data as $row) {
		$row = (array) $row;
		$label = $row['field_label'];
		$code = $row['code'];
		$type = $row['field_type'];
		$length = $row['field_length'];
		$values = json_decode($row['field_value'], true);
		$class = ($type === 'number') ? 'text-right' : '';
		$mandatory = isset($row['mandatory']) ? (int)$row['mandatory'] : 0;
		$isRequired = ($mandatory === 0);
		$star = $isRequired ? " <span class='text-danger'>*</span>" : '';
		$requiredAttr = $isRequired ? 'required' : '';

		$html .= "<div class='$length mb-3'><label class='form-label'>$label$star</label>";

		if (in_array($type, ['text', 'number', 'date'])) {
			$html .= "<input type='$type' name='$code' class='form-control $class' $requiredAttr>";
		} elseif ($type === 'select') {
			$html .= "<select name='$code' class='form-select' $requiredAttr>";
			foreach ($values as $v) {
				$html .= "<option value='$v'>$v</option>";
			}
			$html .= "</select>";
		} elseif ($type === 'radio') {
			$isFirst = true;
			foreach ($values as $v) {
			$required = ($mandatory === 0 && $isFirst) ? 'checked' : '';

				$html .= "<div class='form-check'>
					<input class='form-check-input' type='radio' name='$code' value='$v' id='{$code}_$v' $required>
					<label class='form-check-label' for='{$code}_$v'>$v</label>
				</div>";
				$isFirst = false;
			}
		}
		$html .= "</div>";
	}
$html .= "</div>";
			} else {
				$html .= "<div class='alert alert-warning'>No fields found for this feasibility type.</div>";
			}
		}

		return $html;
	}	
	function insertFeasibilityfeildslist($get, $post) {
		global $callConfig;
		$return = array();

		$return['post'] = $post;

		$feasibility_id = $post['feasibility_id'];
		$company_id = $get['company_id'];
		$user_id = $get['user_id'];
		$updated_date = date("Y-m-d H:i:s");

		$fields = json_decode($post['fields_data'], true);

		$lastCodeRow = $callConfig->getRow("SELECT code FROM feasibility_fields ORDER BY id DESC LIMIT 1");
		$lastCodeNumber = 0;
		if ($lastCodeRow && preg_match('/RQ(\d+)/', $lastCodeRow->code, $matches)) {
			$lastCodeNumber = (int) $matches[1];
		}

		$query = "UPDATE feasibility_fields SET is_active = null WHERE feasibility_type_id = {$feasibility_id}";
		$res = $callConfig->executeQuery($query);
		$return['update_res'] = $res;

		if (!empty($fields)) {
			foreach ($fields as $field) {
				$code = $field['code'];

				if (!empty($code)) {
					$existingQuery = "SELECT id FROM feasibility_fields WHERE code = '{$code}' AND feasibility_type_id = {$feasibility_id}";
					$existing = $callConfig->getRow($existingQuery);
				} else {
					$existing = false;
				}

				$data = array(
					'company_id' => $company_id,
					'feasibility_type_id' => $feasibility_id,
					'field_label' => $field['field_label'],
					'field_type' => $field['field_type'],
					'field_value' => $field['field_value'],
					'mandatory' => $field['mandatory'],
					'field_length' => $field['field_length'],
					'is_active' => 0,
				);

				if ($existing) {
					$data['code'] = $code;
					$data['updated_by'] = $user_id;
					$data['updated_date'] = $updated_date;
					$response = $callConfig->updateRecord('feasibility_fields', $data, 'id', $existing->id);
					$return['updaterecord'][] = $response;
				} else {
					$lastCodeNumber++;
					$data['code'] = 'RQ' . $lastCodeNumber;
					$data['created_by'] = $user_id;
					$data['created_date'] = $updated_date;
					$response = $callConfig->insertRecord('feasibility_fields', $data);
					$return['insertrecord'][] = $data;
				}
			}
			$return['status'] = 'success';
		} else {
			$return['status'] = 'error';
			$return['message'] = 'No fields data found.';
		}

		return $return;
	}

	function reOpenFeasibility($get){
        global $callConfig;
        $updated_date = date("Y-m-d H:i:s");
        $updated_by = $get['user_id'];
        $feasibility_id = $get['feasibility_id'] ?? '';
        $update_sql = "UPDATE feasibility_requirements SET current_status = " . ST_ACTIVE .", updated_date = '{$updated_date}', updated_by={$updated_by} WHERE id = $feasibility_id";
        $result = $callConfig->executeQuery($update_sql);
        return $result;
    }

    // End feasibilty part By Kote - 25-06-2025



    function createSalesOrdersWithFeasibilty($get){
    	global $callConfig;
    	$return = array();
    	$feasibility_id = $get['feasibility_id'];
    	$crm_contact_id = $get['crm_contact_id'];
    	$frsql = "SELECT * FROM feasibility_requirements WHERE company_id = '".$get['company_id']."' AND id = ".$feasibility_id." AND sales_order_id IS NULL AND is_active = 0";
    	$frres = $callConfig->getRow($frsql);
        $return['feasibility_requirements'] = $frres;

        $crm_contactssql = "SELECT * FROM crm_contacts WHERE company_id = '".$get['company_id']."' AND id = ".$crm_contact_id." AND is_active = 0";
    	$crm_contactsres = $callConfig->getRow($crm_contactssql);
        $return['crm_contacts'] = $crm_contactsres;

        if($crm_contactsres->crm_company_id !=""){
        	$crm_companiessql = "SELECT * FROM crm_companies WHERE company_id = '".$get['company_id']."' AND id = ".$crm_contactsres->crm_company_id." AND is_active = 0";
    		$crm_companiesres = $callConfig->getRow($crm_companiessql);
        	$return['crm_companies'] = $crm_companiesres;
        	if($crm_companiesres->customer_id !=""){
				$customer_id = $crm_companiesres->customer_id;
        	}else{
        		//to create customer
        		$insertcust = $this->createCustomerWithContactLeadDetails($crm_contactsres, $crm_contact_id, $crm_contactsres->crm_company_id, $get);
        		$return['insert_customers'] = $insertcust;
        		$customer_id = $insertcust['customers']['LASTID'];
        		//to create customer
        	}	
    	}else{
    		$insertcust = $this->createCustomerWithContactLeadDetails($crm_contactsres, $crm_contact_id,'',$get);
    		$return['insert_customers'] = $insertcust;
    		$customer_id = $insertcust['customers']['LASTID'];
        }
        $customersql = "SELECT * FROM customers WHERE company_id = '".$get['company_id']."' AND id = '".$customer_id."' AND is_active = 0";
    	$customerres = $callConfig->getRow($customersql);
    	$return['customers'] = $customerres;


    	$lfbsql = "SELECT * FROM lead_feasibility_boms WHERE company_id = '".$get['company_id']."' AND feasibility_id = '".$feasibility_id."' AND is_active = 0";
        $lfbres = $callConfig->getAllRows($lfbsql);
        $return['lead_feasibility_boms'] = $lfbres;
        $total_amount = 0;
        $itemsid = $itemsdetail = array();
        foreach($lfbres as $item_data){
        	$itemsid[$item_data->item_id] = $item_data->item_id;
        	$total_amount += $item_data->total_amount;
        }

        if(count((array)$itemsid) !="0"){
        	$itemssql = "SELECT id, item_name FROM items WHERE company_id = '".$get['company_id']."' AND id IN(".implode(',',$itemsid).") AND is_active = 0";
	        $itemsres = $callConfig->getAllRows($itemssql);
	        foreach($itemsres as $ires){
	        	$itemsdetail[$ires->id] = $ires->item_name;
	        }
        }

		$transaction_number = $this->toGetApplicationSerialNumber('sales_orders');
        $currency_id = 93;
        $cost_center_id = 1;
        $transaction_date = $due_date = date("Y-m-d");
        $fieldnames=array('company_id'=>$get['company_id'],'transaction_number'=>$transaction_number,'transaction_date'=>$transaction_date,'currency_id'=>$currency_id,'currency_rate'=>1,'originator'=>$get['user_id'],'party_id'=>$customerres->id,'party_name'=>$customerres->company,'party_email'=>$customerres->email,'party_phone'=>$customerres->phone,'party_address'=>$customerres->billing_address,'party_ship_adderss'=>$customerres->shipping_address,'place_of_supply'=>'','payment_terms'=>$customerres->payment_terms,'amount'=>$total_amount,'tax_amount'=>0,'total_amount'=>$total_amount,'ref_type'=>'crm_contacts','ref_id'=>$crm_contact_id,'cost_center_id'=>$cost_center_id,'current_status'=>0,'created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'),'originator_currency_id'=>$currency_id,'originator_state_id'=>$customerres->shipping_state,'supplier_state_id'=>$customerres->shipping_state,'due_date'=>$due_date, 'pending_amount'=>$total_amount,'is_active'=>'0');
        $insert_data=$callConfig->insertRecord('sales_orders',$fieldnames);
		$return['main'] = $insert_data;
		if($insert_data['status'] == 'sucess'){
			$insert_id = $insert_data['LASTID'];
			foreach($lfbres as $item_data){
				$itemfieldnames=array('company_id'=>$item_data->company_id,'transaction_id'=>$insert_id,'item_id'=>$item_data->item_id,'item_name'=>$itemsdetail[$item_data->item_id],'description'=>'','purchase_uom'=>$item_data->unit,'item_quantity'=>$item_data->item_quantity,'item_price'=>$item_data->item_price,'item_amount'=>$item_data->total_amount,'tax_amount'=>0,'discount_amount'=>0,'total_amount'=>$item_data->total_amount,'pending_qty'=>$item_data->item_quantity,'transaction_qty'=>$item_data->item_quantity,'created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'),'is_active'=>'0');
				$item_id=$callConfig->insertRecord('so_items',$itemfieldnames);
				$return['child'][] = $item_id;
			}

			$insertary1['updated_date']=date('Y-m-d H:i:s');
			$insertary1['updated_by']=$get['user_id'];
			$insertary1['sales_order_id']=$insert_id;
			$callConfig->updateRecord("feasibility_requirements",$insertary1,'id',$feasibility_id);
		}
        return $return;
    }
    function createCustomerWithContactLeadDetails($crm_contactsres, $crm_lead_contact_id, $crm_company_id="", $get){
    	global $callConfig;
    	$return = array();
    	$crecustom = "INSERT INTO customers(company_id, first_name, middle_name, last_name, display_name, email, phone, mobile, fax, website_url, billing_address, shipping_address,billing_post_code, shipping_post_code, billing_country, shipping_country, billing_state, shipping_state, billing_city, shipping_city,  company, is_active, current_Status, Created_By, Created_Date, image, pan_no)";
    	if($crm_company_id !=""){
    		$crecustom .="SELECT ".$get['company_id'].",cc.first_name, cc.middle_name, cc.last_name, cc.display_name, cc.email_id, cc.phone, cc.office_phone, cc.fax, ccomp.website_url, ccomp.address, ccomp.address, ccomp.post_code, ccomp.post_code, ccomp.country_id, ccomp.country_id, ccomp.state_id, ccomp.state_id, ccomp.city_id, ccomp.city_id, ccomp.company_name ,0,0,'".$get['user_id']."','".date("Y-m-d H:i:s")."', ccomp.image, ccomp.pan_no FROM crm_contacts cc, crm_companies ccomp WHERE cc.id = '".$crm_lead_contact_id."' AND ccomp.id = '".$crm_company_id."' AND cc.is_active = ccomp.is_active";
		}else{ // crm contact
			$crecustom .="SELECT ".$get['company_id'].", first_name, middle_name, last_name, display_name, email_id, phone, office_phone, fax, website, address, address, post_code, post_code, country_id, country_id, state_id, state_id, city_id, city_id, company_name , 0, 0, ".$get['user_id'].", '".date('Y-m-d H:i:s')."','','' FROM crm_contacts WHERE id = ".$crm_lead_contact_id;

		}
		$res = $callConfig->executeQuery($crecustom);
		if($res['status'] == 'sucess'){
			if($crm_company_id !=""){
				$insertary['updated_date']=date('Y-m-d H:i:s');
				$insertary['updated_by']=$get['user_id'];
				$insertary['customer_id']=$res['LASTID'];
				$callConfig->updateRecord("crm_companies",$insertary,'id',$crm_company_id);
			}else{
				$insertary=array('company_id'=>$get['company_id'], 'default_crm_contact_id'=>$crm_lead_contact_id, 'company_name'=>$crm_contactsres->company_name, 'customer_id'=> $res['LASTID'], 'website_url'=>$crm_contactsres->website, 'address'=>$crm_contactsres->address, 'post_code'=>$crm_contactsres->post_code, 'country_id'=>$crm_contactsres->country_id, 'state_id'=>$crm_contactsres->state_id, 'city_id'=>$crm_contactsres->city_id, 'is_active'=>'0', 'current_status'=>'0','created_by'=>$post['user_id'], 'created_date'=>date("Y-m-d H:i:s"));
				$crmleaddetails = $callConfig->insertRecord('crm_companies',$insertary);

				$insertary1['updated_date']=date('Y-m-d H:i:s');
				$insertary1['updated_by']=$get['user_id'];
				$insertary1['crm_company_id']=$crmleaddetails['LASTID'];
				$callConfig->updateRecord("crm_contacts",$insertary1,'id',$crm_lead_contact_id);
			}
		}
		$return['crecustom'] = $crecustom;
		$return['customers'] = $res;
		return $return;
     }

    function checkCompanyNameAvilableOrNotInCustomer($get, $post){
    	global $callConfig;
		$company_name = $post['company_name'];
		$customerssql = "SELECT id FROM customers WHERE company_id = ".$get['company_id']." AND company LIKE '%".$company_name."%' AND is_active = 0";
		$customersres=$callConfig->getRow($customerssql);
		$return['customers'] = $customersres;
		return $return;
    }

	// start Data comparision by koti
    function compareDatabaseSchemas($get) {
        $dbName1 = $get['db1']; // e.g., 'db_reference'
        $dbName2 = $get['db2']; // e.g., 'db_target'
        $db1 = $this->connectToDatabaseByName($dbName1);
        $db2 = $this->connectToDatabaseByName($dbName2);
        function getTablesDB($db) {
            $tables = [];
            $result = $db->query("SHOW TABLES");
            while ($row = $result->fetch_array()) {
                $tables[] = $row[0];
            }
            return $tables;
        }
        function getFieldsDB($db, $table) {
            $fields = [];
            $result = $db->query("SHOW COLUMNS FROM `$table`");
            while ($row = $result->fetch_assoc()) {
                $fields[$row['Field']] = [
                    'Type' => $row['Type'],
                    'Null' => $row['Null'],
                    'Default' => $row['Default'],
                    'Extra' => $row['Extra']
                ];
            }
            return $fields;
        }
        function getCreateTableSQL($db, $table) {
            $result = $db->query("SHOW CREATE TABLE `$table`");
            $row = $result->fetch_assoc();
            return $row['Create Table'] . ";";
        }
        $tables1 = getTablesDB($db1);
        $tables2 = getTablesDB($db2);
        $allTables = array_unique(array_merge($tables1, $tables2));
        $output = [];
        foreach ($allTables as $table) {
            $report = [
                'table' => $table,
                'status' => [],
                'actions' => []
            ];
            $existsInDb1 = in_array($table, $tables1);
            $existsInDb2 = in_array($table, $tables2);

            // Table missing in DB2
            if (!$existsInDb2 && $existsInDb1) {
                $report['status'][] = 'missing_in_db2:'.$dbName2;
                $sql = getCreateTableSQL($db1, $table);
                $result = $db2->query($sql);
                $report['actions'][] = ['direction' => 'to_db2', 'type' => 'create_table', 'query' => $sql, 'result' => $result];
            }
            // Compare fields if table exists in both
            if ($existsInDb1 && $existsInDb2) {
                $report['status'][] = 'exists_in_both';
                $fields1 = getFieldsDB($db1, $table);
                $fields2 = getFieldsDB($db2, $table);
                $allFields = array_unique(array_merge(array_keys($fields1), array_keys($fields2)));
                foreach ($allFields as $field) {
                    $in1 = array_key_exists($field, $fields1);
                    $in2 = array_key_exists($field, $fields2);
                    // Field missing in DB2
                    if (!$in2 && $in1) {
                        $f = $fields1[$field];
                        $null = ($f['Null'] === 'NO') ? "NOT NULL" : "NULL";
                        $default = ($f['Default'] !== null) ? "DEFAULT '" . $f['Default'] . "'" : "";
                        $extra = $f['Extra'];
                        $columnBefore = null;
                        $columnOrder = array_keys($fields2); // this preserves order from SHOW COLUMNS
                        $index = array_search('is_active', $columnOrder);
                        if ($index !== false && $index > 0) {
                            $columnBefore = $columnOrder[$index - 1];
                        }
                        if ($columnBefore) {
                            $sql = "ALTER TABLE `$table` ADD COLUMN `$field` {$f['Type']} $null $default $extra AFTER `$columnBefore`;";
                        } else {
                            // fallback: just add to end
                            $sql = "ALTER TABLE `$table` ADD COLUMN `$field` {$f['Type']} $null $default $extra;";
                        }
                        $result = $db2->query($sql);
                        $report['actions'][] = ['direction' => 'to_db2:'.$dbName2, 'type' => 'add_column', 'query' => $sql, 'result' => $result];
                    }

                    // Field type mismatch
                    if ($in1 && $in2 && $fields1[$field]['Type'] !== $fields2[$field]['Type']) {
                        // Fix DB2 to match DB1
                        $f1 = $fields1[$field];
                        $null1 = ($f1['Null'] === 'NO') ? "NOT NULL" : "NULL";
                        $default1 = ($f1['Default'] !== null) ? "DEFAULT '" . $f1['Default'] . "'" : "";
                        $extra1 = $f1['Extra'];
                        $sql1 = "ALTER TABLE `$table` MODIFY COLUMN `$field` {$f1['Type']} $null1 $default1 $extra1;";
                        $result = $db2->query($sql1);
                        $report['actions'][] = ['direction' => 'to_db2:'.$dbName2, 'type' => 'modify_column', 'query' => $sql1, 'result' => $result];
                    }
                }
            }

            // Add to output only if there's something meaningful
            if (!empty($report['status']) || !empty($report['actions'])) {
                $output[] = $report;
            }
        // return $output;
        }
        echo "<pre>";
        print_r($output);
        echo "</pre>";

    }

    function connectToDatabaseByName($dbName) {
        $host = HOSTNAME;
        $user = USERNAME;
        $pass = PASSWORD;
        $mysqli = new mysqli($host, $user, $pass, $dbName);
        if ($mysqli->connect_error) {
            die("Connection failed to DB [$dbName]: " . $mysqli->connect_error);
        }
        return $mysqli;
    }
	// end Data comparision by koti



	function generatingApplicationSerialNumberMastersTst($transaction_table,$get){
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
			$parts[$financial_year_position] = date("Y",strtotime($from_date))."-".date("y",strtotime($to_date));
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

			if($asnres->financial_year_check == "Y"){
				$parts[$financial_year_position] = date("Y",strtotime($transaction_date));
			}else{
				unset($parts[$financial_year_position]);
			}
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
	function getCRMAIDashboards($get){
		global $callConfig;
		$todaydate = date("Y-m-d");
		// if($get['admin_type'] != 1){
		// 	$originatercondition = " AND originator = '".$get['user_id']."'";
		// 	$prooriginatercondition = " AND assigner_id = '".$get['user_id']."'";
		// 	$feasibiltycondition = " AND fr.created_by = '".$get['user_id']."'";
		// }
		$crm_leadssql = "SELECT id, type_name, type, created_date, created_by FROM crm_activity_types WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0";
		$crm_leadsres=$callConfig->getAllRows($crm_leadssql);
		$return['crm_activity_types'] = $crm_leadsres;

		$crm_leadssql = "SELECT id, originator, campaign_name, crm_campaign_type, campaign_start_date, campaign_end_date, total_budget, created_date FROM crm_campaigns WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0";
		$crm_leadsres=$callConfig->getAllRows($crm_leadssql);
		$return['crm_campaigns'] = $crm_leadsres;

		$crm_leadssql = "SELECT id, originator, company_name, default_crm_contact_id, created_date FROM crm_companies WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0";
		$crm_leadsres=$callConfig->getAllRows($crm_leadssql);
		$return['crm_companies'] = $crm_leadsres;

		$crm_leadssql = "SELECT id, originator, display_name, post_code, country_id, state_id,city_id, created_date FROM crm_contacts WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0";
		$crm_leadsres=$callConfig->getAllRows($crm_leadssql);
		$return['crm_contacts'] = $crm_leadsres;

		$crm_leadssql = "SELECT id, originator, deal_name, crm_company_id, crm_contact_id, due_date, expected_close_date, originator, crm_status, created_date FROM crm_deals WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0";
		$crm_leadsres=$callConfig->getAllRows($crm_leadssql);
		$return['crm_deals'] = $crm_leadsres;

		$crm_leadssql = "SELECT id, transaction_table, `transaction_id`, `lead_type`, `date`, `time`, `address`, `country_id`, `state_id`, `city_id`, `post_code`, `phone`, `sms_text`, `email_content_id`, `recipient_email_id`, `member`, `comment`, `originator`, `section_id`, `division_id`, `job_grade_id`, `completion_datetime`, `completion_note`, created_date, created_by FROM crm_histories WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0";
		$crm_leadsres=$callConfig->getAllRows($crm_leadssql);
		$return['crm_histories'] = $crm_leadsres;

		$crm_leadssql = "SELECT id, display_name, post_code, country_id, state_id,city_id, created_date, created_by FROM crm_leads WHERE company_id = '".$get['company_id']."' ".$originatercondition." AND is_active = 0";
		$crm_leadsres=$callConfig->getAllRows($crm_leadssql);
		$return['crm_leads'] = $crm_leadsres;

		$project_task_statussql = "SELECT id, table_name, name, status, action_label, created_date, created_by FROM project_task_status WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$project_task_statusres = $callConfig->getAllRows($project_task_statussql);
		$return['project_task_status'] = $project_task_statusres;	

		$crm_general_codessql = "SELECT id, code, value, label, created_date, created_by FROM crm_general_codes WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$crm_general_codesres=$callConfig->getAllRows($crm_general_codessql);
		foreach($crm_general_codesres as $crmcode){
			$crm_general_codes[$crmcode->code][] =$crmcode; 
		}
		$return['crm_general_codes'] = $crm_general_codes;

		$query = "SELECT id, display_name, created_date, created_by FROM application_users WHERE  company_id=".$get['company_id']." AND is_active=0";
        $data = $callConfig->getAllRows($query);
 		$return['users_res'] = $data;

		return $return;

	}

}
?>