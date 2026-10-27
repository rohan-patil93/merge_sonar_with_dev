<?php 
class assetUserClass extends userClass{
	function getAssetAllocation($get){
		global $callConfig;
		$return = array();
		$return['get']= $get;
		$todaytedate = date('Y-m-d');
		if($get['resultgettype'] =="ADMINDASH"){
			// echo "<pre>";
			// $display_attributessql = "SELECT * FROM display_attributes WHERE  company_id = '".$get['company_id']."' AND is_active =0 GROUP BY application_table_name";
			// $display_attributessres=$callConfig->getAllRows($display_attributessql);
			// foreach($display_attributessres as $sar){
			// 	$list_attributes = json_decode($sar->list_attributes,1);
			// 	$application_table_name = $sar->application_table_name;
			// 	$sqnc = 10;
			// 	foreach($list_attributes as $fieldname => $fieldval){
			// 		$insert_display_list = $fieldval->insert_display_list =="1"?0:1;
			// 		$type = $fieldval->field_type !=""?$fieldval->field_type:'text';
			// 		$insert_array = array('application_module'=>'','company_id'=>$get['company_id'],'application_table_name'=>$application_table_name,'help_text'=>'','menu_path'=>'','field_name'=>$fieldname,'reference_field_name'=>$fieldname,'field_type'=>$type,'sequence'=>$sqnc,'display'=>$insert_display_list,'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id'],'created_date'=>date("Y-m-d H:i:s"));	
			// 		$approval_id=$callConfig->insertRecord('application_tours',$insert_array);
			// 		$sqnc =$sqnc +10;
			// 	}
			// 	//exit;
			// }
			// //display_attributessres
			// exit;

			if($get['admin_type'] !='1'){
	    		$codition = " AND asset_custodian = '".$get['user_id']."'";
	    	}
	    	$asset_categorie = "SELECT id,category_description,depreciation_account,disposal_account,Journal_id,duration,duration_type FROM  asset_categories WHERE  company_id = '".$get['company_id']."' AND is_active =0";
			$asset_categorieres=$callConfig->getAllRows($asset_categorie);
			$asset_categories = array();
			foreach($asset_categorieres as $ascat){
				$asset_categories[$ascat->id] = $ascat;
			}
			$return['asset_categories'] = $asset_categories;

			$locationsql = "SELECT id,location_name FROM locations WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$locationsres=$callConfig->getAllRows($locationsql);
			$locations = array();
			foreach($locationsres as $loc){
				$locations[$loc->id] = $loc;
			}
			$return['locations'] = $locations;
			
			$application_userssql = "SELECT id,display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$application_usersres=$callConfig->getAllRows($application_userssql);
			$application_users = array();
			foreach($application_usersres as $aus){
				$application_users[$aus->id] = $aus;
			}
			$return['application_users'] = $application_users;

			$assetstatussql = "SELECT id,name FROM asset_status WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$assetstatusres=$callConfig->getAllRows($assetstatussql);
			$asset_status = array();
			foreach($assetstatusres as $aas){
				$asset_status[$aas->id] = $aas;
			}
			$return['asset_status'] = $asset_status; 
			
			$departmentsql = "SELECT id,department_name FROM departments WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$departmentres=$callConfig->getAllRows($departmentsql);
			$return['departments'] = $departmentres; 
			
			$asset_condition_typesql = "SELECT id,condition_name FROM asset_condition_type WHERE is_active =0 AND company_id = '".$get['company_id']."'";
			$asset_condition_typeres=$callConfig->getAllRows($asset_condition_typesql);
			foreach($asset_condition_typeres as $assetct){
				$assetcondition[$assetct->id] = $assetct;
			}
			$return['asset_condition_type'] = $assetcondition;
			
			$assetsql = "SELECT * FROM assets WHERE company_id = '".$get['company_id']."' AND  is_active =0";
			$assets=$callConfig->getAllRows($assetsql);
			$return['assets_graph'] = $assets;

			$sql1 ="SELECT sum(if(end_date <= '".$todaytedate."',1,0) AND asset_return_status =='N') as overduecnt, sum(if(end_date >= '".$todaytedate."',1,0) AND asset_return_status =='N') as duesooncnt FROM `user_assets` WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$res1=$callConfig->getRow($sql1);
			$return['user_asset_cnt_sql'] = $sql1;
			$return['user_asset_cnt'] = $res1;
			
			$sql2 ="SELECT sum(if(warranty_expiry_date <= '".$todaytedate."' AND  warranty_expiry_date !='0000-00-00',1,0)) as warranty_expiry_cnt, sum(if(end_of_life >= '".$todaytedate."'  AND end_of_life !='0000-00-00',1,0)) as end_of_life, sum(if(allocation_status ='N',1,0)) as unallocatedcnt FROM assets WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$res2=$callConfig->getRow($sql2);
			$return['asset_cnt'] = $res2;
			$return['asset_cnt_sql'] = $sql2;

			if(isset($get['ownership']) AND $get['ownership'] !=""){ //self/partner
				$conditionassetlist .= " AND ownership  = '".$get['ownership']."'";
			}
			if(isset($get['allocation_status']) AND $get['allocation_status'] !=""){
				$conditionassetlist .= " AND allocation_status = '".$get['allocation_status']."'";
			}
			if(isset($get['parent_asset']) AND $get['parent_asset'] !=""){ //parent vendor
				if($get['parent_asset'] == "Y"){
					$conditionassetlist  .= " AND parent_asset !=''";
				}else{
					$conditionassetlist .= " AND parent_asset IS NULL";	
				}
			}
			if(isset($get['category']) AND $get['category'] !=""){
				$conditionassetlist .= " AND category_id  = '".$get['category']."'";
			}
			if(isset($get['location']) AND $get['location'] !=""){
				$conditionassetlist .= " AND asset_location  = '".$get['location']."'";
			}
			if(isset($get['status']) AND $get['status'] !=""){
				$conditionassetlist .= " AND asset_status ='".$get['status']."'";
			}
			if(isset($get['candition']) AND $get['candition'] !=""){
				$conditionassetlist .= " AND conditions  = '".$get['candition']."'";
			}
			$assetsql = "SELECT * FROM assets WHERE company_id = '".$get['company_id']."' AND is_active =0 ".$conditionassetlist." ".$codition;
			$assets=$callConfig->getAllRows($assetsql);
			$return['assets'] = $assets;


			$uaquery = "SELECT id, user_id, asset_id FROM user_assets WHERE company_id =".$get['company_id']." AND asset_return_status =='N' AND is_active =0";
			$uaqueryres = $callConfig->getAllRows($uaquery);
			foreach($uaqueryres as $assetct){
				$user_assets[$assetct->asset_id] = $assetct->user_id;
			}	
			$return['user_assets'] = $user_assets;
		}elseif($get['resultgettype'] =='USERDASH'){ // user data
			$dashcntsql = "SELECT (SELECT count(id) as cnt FROM user_assets WHERE company_id = '".$get['company_id']."' AND user_id = '".$get['user_id']."' AND is_active =0) as self_asset_cnt, (SELECT count(id) as cnt FROM asset_requests WHERE company_id = '".$get['company_id']."' AND originator = '".$get['user_id']."' AND is_active =0) as asset_requests_cnt, (SELECT count(art.id) as cnt FROM asset_request_transfers art, asset_requests ar WHERE art.company_id = '".$get['company_id']."' AND art.company_id = ar.company_id AND art.asset_request_id = ar.id AND ar.originator = '".$get['user_id']."' AND art.is_active = ar.is_active AND art.is_active = 0) as asset_request_transfers_cnt";
			$dashcntres=$callConfig->getRow($dashcntsql);
			$return['dashboard_cnt'] = $dashcntres;
		
			
			if($get['pagetype'] == 'ASSETS_REQUEST'){
				
				$asset_requestssql = "SELECT * FROM asset_requests WHERE company_id = '".$get['company_id']."' AND originator = '".$get['user_id']."' AND is_active =0";
				$asset_requestsres=$callConfig->getAllRows($asset_requestssql);
				$return['asset_requests'] = $asset_requestsres;
			
			}elseif($get['pagetype'] == 'ASSET_RETURNS'){
				
				//$asset_requestssql = "SELECT * FROM asset_returns WHERE company_id = '".$get['company_id']."' AND originator = '".$get['user_id']."' AND is_active =0";
			//	$asset_requestsres=$callConfig->getAllRows($asset_requestssql);
				//$return['asset_returns'] = $asset_requestsres;

				$asset_requestssql = "SELECT ass.asset_code, ass.asset_description, ass.category_id, assretur.id, assretur.return_date, assretur.description, assretur.approve_datetime, assretur.current_status FROM asset_returns assretur , assets ass WHERE assretur.company_id = '".$get['company_id']."' AND assretur.originator = '".$get['user_id']."' AND assretur.company_id = ass.company_id AND assretur.asset_id = ass.id  AND assretur.is_active = ass.is_active AND ass.is_active =0";
				$asset_requestsres=$callConfig->getAllRows($asset_requestssql);
				$return['asset_returns'] = $asset_requestsres;
				$return['asset_requestssql'] = $asset_requestssql;

			}elseif($get['pagetype'] == 'REQUEST_INPROCESS'){

				$assetsql = "SELECT id, asset_code, asset_description FROM assets WHERE company_id = '".$get['company_id']."' AND is_active =0 ";
				$assetsres=$callConfig->getAllRows($assetsql);
				foreach($assetsres as $assetct){
					$assets[$assetct->id] = $assetct;
				}
				$return['assets'] = $assets;


				$asset_requestssql = "SELECT art.id, art.asset_id, art.asset_request_id, art.asset_transfer_date, art.start_date, art.end_date, art.current_status, art.delivery_type, art.asset_accept_or_reject_date, ar.request_datetime FROM asset_request_transfers art, asset_requests ar WHERE art.company_id = '".$get['company_id']."' AND art.company_id = ar.company_id AND art.asset_request_id = ar.id AND ar.originator = '".$get['user_id']."' AND art.is_active = ar.is_active AND art.is_active = 0";
				$asset_requestsres=$callConfig->getAllRows($asset_requestssql);
				$return['asset_request_transfers'] = $asset_requestsres;

			}else{
				
				$asset_requestssql = "SELECT ass.asset_code, ass.asset_description, ass.category_id, aua.id, aua.asset_id, aua.start_date, aua.end_date, aua.current_status FROM user_assets aua , assets ass WHERE aua.company_id = '".$get['company_id']."' AND aua.user_id = '".$get['user_id']."' AND aua.company_id = ass.company_id AND aua.asset_id = ass.id  AND aua.is_active = ass.is_active AND ass.is_active =0";
				$asset_requestsres=$callConfig->getAllRows($asset_requestssql);
				$return['user_assets'] = $asset_requestsres;
				$return['asset_requestssql'] = $asset_requestssql;


				$assetsql = "SELECT id, asset_id, user_asset_id, return_date FROM asset_returns WHERE company_id = '".$get['company_id']."' AND originator = '".$get['user_id']."' AND is_active =0 ";
				$assetsres=$callConfig->getAllRows($assetsql);
				foreach($assetsres as $assetct){
					$asset_returns[$assetct->asset_id] = $assetct;
				}
				$return['asset_returns'] = $asset_returns;

			}




			$asset_categorie = "SELECT id,category_description,depreciation_account,disposal_account,Journal_id,duration,duration_type FROM  asset_categories WHERE  company_id = '".$get['company_id']."' AND is_active =0";
			$asset_categorieres=$callConfig->getAllRows($asset_categorie);
			$asset_categories = array();
			foreach($asset_categorieres as $ascat){
				$asset_categories[$ascat->id] = $ascat;
			}
			$return['asset_categories'] = $asset_categories;

			$locationsql = "SELECT id,location_name FROM locations WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$locationsres=$callConfig->getAllRows($locationsql);
			$locations = array();
			foreach($locationsres as $loc){
				$locations[$loc->id] = $loc;
			}
			$return['locations'] = $locations;
			$return['locations_sql'] = $locationsql;
		}elseif($get['resultgettype'] =='ASSET_RETURNS'){ // user data

			$asset_requestssql = "SELECT ass.asset_code, ass.asset_description, ass.category_id, assretur.id, assretur.originator, assretur.request_datetime,  assretur.return_date, assretur.description, assretur.approve_datetime, assretur.current_status FROM asset_returns assretur , assets ass WHERE assretur.company_id = '".$get['company_id']."' AND assretur.company_id = ass.company_id AND assretur.asset_id = ass.id  AND assretur.is_active = ass.is_active AND ass.is_active =0";
				$asset_requestsres=$callConfig->getAllRows($asset_requestssql);
				$return['asset_returns'] = $asset_requestsres;
				$return['asset_requestssql'] = $asset_requestssql;

		}elseif($get['resultgettype'] =='VERIFYASSETS'){ //Insert Page data	
			$assetsql = "SELECT id, asset_code, asset_description, asset_custodian, asset_location FROM assets WHERE is_active =0 AND company_id = '".$get['company_id']."' AND id IN(".$get['assetid'].")";
			$assets=$callConfig->getAllRows($assetsql);
			$return['list_assets'] = $assets;

			$asset_verificationssql = "SELECT id, asset_id, start_date_time, end_date_time FROM asset_verifications WHERE is_active =0 AND company_id = '".$get['company_id']."' AND asset_id IN(".$get['assetid'].") AND current_status !='".ST_APPROVED."'";
			$asset_verificationsres=$callConfig->getAllRows($asset_verificationssql);
			foreach($asset_verificationsres as $asset_verifications){
				$asset_verificationsarray[$asset_verifications->asset_id] = $asset_verifications;
			}
			$return['asset_verifications'] = $asset_verificationsarray;	



		// 	$asset_categorie = "SELECT id,category_description,depreciation_account,disposal_account,Journal_id,duration,duration_type FROM  asset_categories WHERE  company_id = '".$get['company_id']."' AND is_active =0";
		// 	$asset_categorieres=$callConfig->getAllRows($asset_categorie);
		// 	$return['asset_categories'] = $asset_categorieres; 
			
			$locationsql = "SELECT id,location_name FROM locations WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$locationsres=$callConfig->getAllRows($locationsql);
			foreach($locationsres as $assetct){
				$locations[$assetct->id] = $assetct->location_name;
			}
			$return['locations'] = $locations; 
			
		// 	$assetstatussql = "SELECT id,name FROM asset_status WHERE company_id = '".$get['company_id']."' AND is_active =0";
		// 	$assetstatusres=$callConfig->getAllRows($assetstatussql);
		// 	$return['asset_status'] = $assetstatusres; 


		// $asset_condition_typesql = "SELECT id,condition_name FROM asset_condition_type WHERE is_active =0 AND company_id = '".$get['company_id']."'";
		// 	$asset_condition_typeres=$callConfig->getAllRows($asset_condition_typesql);
		// 	foreach($asset_condition_typeres as $assetct){
		// 		$assetcondition[$assetct->id] = $assetct->condition_name;
		// 	}
		// 	$return['asset_condition_type'] = $assetcondition;

			$user = "SELECT id,display_name FROM application_users WHERE is_active =0 AND company_id = '".$get['company_id']."' AND JSON_SEARCH(role_id, 'one', '".ASSET_EXECUTIVE_ROLE."') IS NOT NULL AND is_active = 0";
			$users=$callConfig->getAllRows($user);
			$return['users'] = $users;

			// $assetlistsql = "SELECT id,asset_description,asset_code  FROM assets WHERE is_active =0 AND company_id = '".$get['company_id']."'";
			// $assetslist=$callConfig->getAllRows($assetlistsql);
			// $return['list_assets'] = $assetslist;

			
		}elseif($get['resultgettype'] =='INSERTASSETRETURN'){ //Insert Page data
		
			$user_assetssql = "SELECT * FROM user_assets WHERE company_id =".$get['company_id']." AND id = '".$get['user_asset_id']."' AND is_active =0";
			$user_assetsres = $callConfig->getRow($user_assetssql);
			$return['user_assets'] = $user_assetsres;
			$assetssql = "SELECT * FROM assets WHERE company_id =".$get['company_id']." AND id = '".$user_assetsres->asset_id."' AND is_active =0";
			$assetsres = $callConfig->getRow($assetssql);
			$return['assets'] = $assetsres;
		}elseif($get['resultgettype'] =='DISCARDASSET'){ //Insert Page data
		
			$assetsql = "SELECT id, asset_code, allocation_status,brand, model, serial_no, asset_location, asset_img, asset_description, category_id, asset_status FROM assets WHERE company_id = '".$get['company_id']."' AND id IN(".$get['assetid'].") AND is_active =0".$conditionrequest;
			$assets=$callConfig->getAllRows($assetsql);
			$return['assetsql'] = $assetsql;
			$return['list_assets'] = $assets;



		}elseif($get['resultgettype'] =='DIRECTASSETALLOCATE'){ //Insert Page data



			$departmentsql = "SELECT id,department_name FROM departments WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$departmentres=$callConfig->getAllRows($departmentsql);
			$return['departments'] = $departmentres; 

			$costcentersql = "SELECT id,cost_center_name FROM cost_centers WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$costcenter=$callConfig->getAllRows($costcentersql);
			$return['costcenter'] = $costcenter; 

			$assetstatussql = "SELECT id,name FROM asset_status WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$assetstatusres=$callConfig->getAllRows($assetstatussql);
			$return['asset_status'] = $assetstatusres; 
			
			$locationsql = "SELECT id,location_name FROM locations WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$locationsres=$callConfig->getAllRows($locationsql);
			$return['locations'] = $locationsres; 

			$application_userssql = "SELECT id,display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$application_usersres=$callConfig->getAllRows($application_userssql);
			$return['application_users'] = $application_usersres; 


			$alreadyusedasset = $this->getactiveassetanystage($get);
			$assetsql = "SELECT id, asset_code, allocation_status,brand, model, serial_no, asset_location, asset_img, asset_description, category_id, asset_status FROM assets WHERE company_id = '".$get['company_id']."' AND id IN(".$get['assetid'].") AND is_active =0".$conditionrequest;
			$assets=$callConfig->getAllRows($assetsql);
			$return['assetsql'] = $assetsql;
			$return['list_assets'] = $assets;

			if($get['momenthistory']=='Y'){
				$assetsql = "SELECT * FROM asset_transactions WHERE is_active =0 AND company_id = '".$get['company_id']."' AND asset_code  IN(".$get['assetid'].")";
				$assets=$callConfig->getAllRows($assetsql);
				$return['momenthistory'] = $assets;
			}

			// $assetsql = "SELECT ass.id,ass.asset_code,ass.asset_description,ass.category_id,ass.serial_no,ass.asset_location,ass.asset_img,ass.brand,ass.model,ass.conditions,ass.asset_status,ass.asset_owner,ass.allocation_status,ass.attribute, ass.ownership, ass.allocation_status, ass.Parent_asset, ass.current_status, emp_ass.end_date,emp_ass.remarks,emp_ass.allocation_type,emp_ass.id as emp_asset_id,emp_ass.attribute as emp_attribute, emp_ass.user_id as user_assets_user_id FROM assets ass LEFT JOIN user_assets emp_ass on ass.is_active =emp_ass.is_active AND ass.company_id = emp_ass.company_id AND ass.id = emp_ass.asset_id WHERE ass.is_active =0 AND ass.company_id = '".$get['company_id']."' AND ass.id IN(".$get['assetid'].")";
			// $return['list_assets_sql'] = $assetsql;
			// $assets=$callConfig->getAllRows($assetsql);
			// $return['list_assets'] = $assets;

		}elseif($get['resultgettype'] == "ASSETREQUESTTRANSFERSACCEPTORREJECT"){	
			
			$asset_request_transferssql = "SELECT * FROM asset_request_transfers WHERE company_id = '".$get['company_id']."' AND  is_active =0 AND id = '".$get['asset_request_transfer_id']."'";
			$asset_request_transfersres=$callConfig->getRow($asset_request_transferssql);
			$return['asset_request_transferssql'] = $asset_request_transferssql;
			$return['asset_request_transfers'] = $asset_request_transfersres;

			if($asset_request_transfersres->asset_id !=""){
				$assetsql = "SELECT id, asset_code, allocation_status,brand, model, serial_no, asset_location, asset_img, asset_description, category_id, asset_status FROM assets WHERE company_id = '".$get['company_id']."' AND id ='".$asset_request_transfersres->asset_id."' AND is_active =0";
				$assets=$callConfig->getRow($assetsql);
				$return['assetsql'] = $assetsql;
				$return['assets'] = $assets;
			}


		}elseif($get['resultgettype'] == "ASSETRETURNRECEIVEACCEPTSORREJECT"){	
			
			$asset_returnssql = "SELECT * FROM asset_returns WHERE company_id = '".$get['company_id']."' AND  is_active =0 AND id = '".$get['asset_return_id']."'";
			$asset_returnsres=$callConfig->getRow($asset_returnssql);
			$return['asset_returnssql'] = $asset_returnssql;
			$return['asset_returns'] = $asset_returnsres;

			if($asset_returnsres->asset_id !=""){
				$assetsql = "SELECT id, asset_code, allocation_status,brand, model, serial_no, asset_location, asset_img, asset_description, category_id, asset_status FROM assets WHERE company_id = '".$get['company_id']."' AND id ='".$asset_returnsres->asset_id."' AND is_active =0";
				$assets=$callConfig->getRow($assetsql);
				$return['assetsql'] = $assetsql;
				$return['assets'] = $assets;
			}

			
		}elseif($get['resultgettype'] =='MULTIPLEASSETUPDATE'){ //Insert Page data
			
			$assetsql = "SELECT id, asset_code, allocation_status,brand, model, serial_no, asset_location, asset_img, asset_description, category_id, asset_status FROM assets WHERE company_id = '".$get['company_id']."' AND id IN(".$get['assetid'].") AND is_active =0".$conditionrequest;
			$assets=$callConfig->getAllRows($assetsql);
			$return['assetsql'] = $assetsql;
			$return['list_assets'] = $assets;

			$asset_condition_typesql = "SELECT id,condition_name FROM asset_condition_type WHERE is_active =0 AND company_id = '".$get['company_id']."'";
			$asset_condition_typeres=$callConfig->getAllRows($asset_condition_typesql);
			foreach($asset_condition_typeres as $assetct){
				$assetcondition[$assetct->id] = $assetct->condition_name;
			}
			$return['asset_condition_type'] = $assetcondition;

			$asset_categorie = "SELECT id,category_description FROM  asset_categories WHERE  company_id = '".$get['company_id']."' AND is_active =0";
			$asset_categorieres=$callConfig->getAllRows($asset_categorie);
			$return['asset_categories'] = $asset_categorieres; 
			
			$locationsql = "SELECT id,location_name FROM locations WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$locationsres=$callConfig->getAllRows($locationsql);
			$return['locations'] = $locationsres; 
			
			$assetstatussql = "SELECT id,name FROM asset_status WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$assetstatusres=$callConfig->getAllRows($assetstatussql);
			$return['asset_status'] = $assetstatusres; 


		}elseif($get['resultgettype'] =='INSERTPAGE'){ //Insert Page data
			//used allocation popup
			$costcentersql = "SELECT id,cost_center_name FROM cost_centers WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$costcenter=$callConfig->getAllRows($costcentersql);
			$return['costcenter'] = $costcenter; 
			//$employeessql = "SELECT id,display_name FROM employees WHERE date_of_leaving IS NULL AND company_id = '".$get['company_id']."' AND is_active =0";

			$employeessql = "SELECT id,display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$employees=$callConfig->getAllRows($employeessql);
			$return['employees'] = $employees; 
			//end allocation popup
			$asset_categorie = "SELECT id,category_description,depreciation_account,disposal_account,Journal_id,duration,duration_type FROM  asset_categories WHERE  company_id = '".$get['company_id']."' AND is_active =0";
			$asset_categorieres=$callConfig->getAllRows($asset_categorie);
			$return['asset_categories'] = $asset_categorieres; 
			$locationsql = "SELECT id,location_name FROM locations WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$locationsres=$callConfig->getAllRows($locationsql);
			$return['locations'] = $locationsres; 
			$assetstatussql = "SELECT id,name FROM asset_status WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$assetstatusres=$callConfig->getAllRows($assetstatussql);
			$return['asset_status'] = $assetstatusres; 
			$departmentsql = "SELECT id,department_name FROM departments WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$departmentres=$callConfig->getAllRows($departmentsql);
			$return['departments'] = $departmentres; 

			$asset_condition_typesql = "SELECT id,condition_name FROM asset_condition_type WHERE is_active =0 AND company_id = '".$get['company_id']."'";
			$asset_condition_typeres=$callConfig->getAllRows($asset_condition_typesql);
			foreach($asset_condition_typeres as $assetct){
				$assetcondition[$assetct->id] = $assetct->condition_name;
			}
			$return['asset_condition_type'] = $assetcondition;

			$user = "SELECT id,display_name FROM application_users WHERE is_active =0 AND company_id = '".$get['company_id']."'";
			$users=$callConfig->getAllRows($user);
			$return['users'] = $users;
			if($get['Rid'] !=""){
				$assetsql = "SELECT * FROM assets WHERE is_active =0 AND company_id = '".$get['company_id']."' AND id=".$get['Rid'];
				$assets=$callConfig->getRow($assetsql);
				$return['assets'] = $assets;
			}
			$assetlistsql = "SELECT id,asset_description,asset_code  FROM assets WHERE is_active =0 AND company_id = '".$get['company_id']."'";
			$assetslist=$callConfig->getAllRows($assetlistsql);
			$return['assets_list'] = $assetslist;
			$vendorsql = "SELECT id,display_name  FROM vendors WHERE is_active =0 AND company_id = '".$get['company_id']."'";
			$vendorlist=$callConfig->getAllRows($vendorsql);
			$return['vendors'] = $vendorlist;


			$depreciationmethodsql = "SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id = ".$get['company_id']." AND genc_code = 'DEPRECIATIONMETHOD' AND is_active =0";
		    $depreciationmethodres = $callConfig->getAllRows($depreciationmethodsql);
		    $return['depreciation_method']= $depreciationmethodres;

		     $chart_of_accountssql = "SELECT id, account_code, account_name FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND is_active =0 ORDER BY display_priority ASC";
		    $chart_of_accountsres = $callConfig->getAllRows($chart_of_accountssql);
		    $return['chart_of_accounts']= $chart_of_accountsres;
		      

		    $journalssql = "SELECT id, journal_name FROM `journals` WHERE company_id = ".$get['company_id']." AND is_active =0 ORDER BY journal_name ASC";
		    $journalsres = $callConfig->getAllRows($journalssql);
		    $return['journals']= $journalsres;
		}elseif($get['resultgettype'] =='INSERTASSETREQUESTS'){
			$asset_categorie = "SELECT id,category_description,depreciation_account,disposal_account,Journal_id,duration,duration_type FROM  asset_categories WHERE  company_id = '".$get['company_id']."' AND is_active =0";
			$asset_categorieres=$callConfig->getAllRows($asset_categorie);
			$return['asset_categories'] = $asset_categorieres; 
			
			$locationsql = "SELECT id,location_name FROM locations WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$locationsres=$callConfig->getAllRows($locationsql);
			$return['locations'] = $locationsres; 

			$applicationuserssql = "SELECT id, reports_to FROM application_users WHERE company_id = '".$get['company_id']."' AND id = '".$get['user_id']."' AND is_active =0";
			$applicationusersres=$callConfig->getRow($applicationuserssql);
			$return['reporting_manager'] = $applicationusersres; 
			//$attributessql = "SELECT id,name FROM asset_attributes WHERE is_active =0 AND company_id = '".$get['company_id']."' AND category_id IN(".$requestres->asset_category_id.")";
			//$attributesres=$callConfig->getAllRows($attributessql);
			//$returnresult['attributes'] = $attributesres;
		}elseif($get['resultgettype'] == 'ASSET_RETURNS'){
			if($get['admin_type'] !='1'){
	    		$codition = " AND approval_user_id = '".$get['user_id']."'";
	    	}

			$assetsql = "SELECT * FROM asset_returns WHERE company_id = '".$get['company_id']."' ".$codition." AND is_active =0 ";
			$assetsres=$callConfig->getAllRows($assetsql);
			// foreach($assetsres as $assetct){
			// 	$asset_returns[$assetct->asset_id] = $assetct;
			// }
			$return['asset_returns'] = $assetsres;




			// $asset_requestssql = "SELECT ass.asset_code, ass.asset_description, ass.category_id, aua.id, aua.asset_id, aua.start_date, aua.end_date, aua.current_status FROM user_assets aua , assets ass WHERE aua.company_id = '".$get['company_id']."' AND aua.user_id = '".$get['user_id']."' AND aua.company_id = ass.company_id AND aua.asset_id = ass.id  AND aua.is_active = ass.is_active AND ass.is_active =0";
			// 	$asset_requestsres=$callConfig->getAllRows($asset_requestssql);
			// 	$return['user_assets'] = $asset_requestsres;
			// 	$return['asset_requestssql'] = $asset_requestssql;


				


		}

		return $return;
	}
	function insertAssetRequest($get, $post){
		global $callConfig;
		$return['post'] = $post;
		$attributes =implode(",",(array)$post['attribute']);
		$insert_requst= array('company_id'=>$post['company_id'],'request_datetime'=>date("Y-m-d H:i:s"),'category_id'=>$post['category_id'],'originator'=>$post['user_id'],'asset_required_start_date'=>$this->dateconvertdbformat($post['asset_required_start_date']),'asset_required_end_date'=>$this->dateconvertdbformat($post['asset_required_end_date']),'description'=>$post['description'],'location_id'=>$post['location_id'],'attribute'=>$attributes,'delivery_type'=>$post['delivery_type'],'approval_user_id'=>$post['approval_user_id'],'quantity'=>$post['quantity'],'delivery_address'=>$post['delivery_address'],'is_active' =>'0','current_status'=>ST_INPROGRESS,'created_date'=>date('Y-m-d H:i:s'),'created_by'=>$post['user_id']);
		$insert_requstres = $callConfig->insertRecord('asset_requests',$insert_requst);
		$return['res']= $insert_requstres;
		// $returnresult['insert_request']  = $insert_requstres;
		if($insert_requstres['LASTID'] !=""){
			$user_ids = array('0'=>$post['approval_user_id']);
			$msg = "Asset Request ".$insert_requstres['LASTID'];
			$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$insert_requstres['LASTID'],'application_table_name'=>'asset_transfer_requests', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>'COM_LIST_ASSET_REQUESTS');
			$sendnotification = $this->tosendnotifiations($notdataa);
			$notdataa['type'] = "INFORMATION";
			$notdataa['notification_users'] = array('0'=>$post['user_id']);
			$sendnotification = $this->tosendnotifiations($notdataa);
		}
		return $return;
	}
	function getassetTransferRequests($get){
		global $callConfig;
		$return = array();
		if($get['rid'] !=""){
			$assettrareqsql = "SELECT * FROM asset_requests WHERE company_id = '".$get['company_id']."' AND  is_active =0 AND id = '".$get['rid']."'";
			$assettrareqres=$callConfig->getRow($assettrareqsql);
			$return['asset_requests'] = $assettrareqres;

			$asset_request_transferssql = "SELECT * FROM asset_request_transfers WHERE company_id = '".$get['company_id']."' AND  is_active =0 AND asset_request_id = '".$get['rid']."'";
			$asset_request_transfersres=$callConfig->getAllRows($asset_request_transferssql);
			$return['asset_request_transfers'] = $asset_request_transfersres;

			$allassetsql = "SELECT id, asset_description FROM assets WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$allassets=$callConfig->getAllRows($allassetsql);
			$all_assets = array();
			foreach($allassets as $allass){
				$all_assets[$allass->id] = $allass->asset_description;
			}
			$return['all_assets'] = $all_assets;

			$alreadyusedasset = $this->getactiveassetanystage($get);
			//$return['assetsql'] = $alreadyusedasset;

			if(isset($get['categroy_list']) AND $get['categroy_list'] !=""){
				$conditionrequest .= " AND category_id  IN (".$get['categroy_list'].")";
			}
			if(isset($get['asset_status']) AND $get['asset_status'] !=""){
				$conditionrequest .= " AND asset_status IN('".$get['asset_status']."')";
			}
			if(isset($get['asset_condition']) AND $get['asset_condition'] !=""){
				$conditionrequest .= " AND conditions IN ('".$get['asset_condition']."')";
			}
			$assetsql = "SELECT id, allocation_status, asset_img, asset_description, category_id, asset_status FROM assets WHERE company_id = '".$get['company_id']."' AND id NOT IN(".$alreadyusedasset.") AND is_active =0".$conditionrequest;
			$assets=$callConfig->getAllRows($assetsql);
			$return['assetsql'] = $assetsql;
			$return['assets'] = $assets;

			$assetstatussql = "SELECT id,name FROM asset_status WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$assetstatusres=$callConfig->getAllRows($assetstatussql);
			$return['asset_status'] = $assetstatusres; 

			$asset_condition_typesql = "SELECT id,condition_name FROM asset_condition_type WHERE is_active =0 AND company_id = '".$get['company_id']."'";
			$asset_condition_typeres=$callConfig->getAllRows($asset_condition_typesql);
			foreach($asset_condition_typeres as $assetct){
				$assetcondition[$assetct->id] = $assetct->condition_name;
			}
			$return['asset_condition_type'] = $assetcondition;
		
		}else{
			$assettrareqsql = "SELECT areq.*, (SELECT count(art.id) FROM asset_request_transfers art WHERE art.company_id = areq.company_id AND art.asset_request_id =areq.id AND art.is_active = 0) as asset_request_transfers_cnt FROM asset_requests areq WHERE areq.company_id = '".$get['company_id']."' AND areq.is_active =0 AND (areq.approval_user_id = '".$get['admin_buddy']."' OR 1 = '".$get['admin_type']."')";
			$assettrareqres=$callConfig->getAllRows($assettrareqsql);
			$return['asset_requests'] = $assettrareqres;
		}
		$asset_categorie = "SELECT id,category_description,depreciation_account,disposal_account,Journal_id,duration,duration_type FROM  asset_categories WHERE  company_id = '".$get['company_id']."' AND is_active =0";
		$asset_categorieres=$callConfig->getAllRows($asset_categorie);
		$asset_categories = array();
		foreach($asset_categorieres as $ascat){
			$asset_categories[$ascat->id] = $ascat;
		}
		$return['asset_categories'] = $asset_categories;

		$locationsql = "SELECT id,location_name FROM locations WHERE company_id = '".$get['company_id']."' AND is_active =0";
		$locationsres=$callConfig->getAllRows($locationsql);
		$locations = array();
		foreach($locationsres as $loc){
			$locations[$loc->id] = $loc;
		}
		$return['locations'] = $locations;
		$return['locations_sql'] = $locationsql;
		
		return $return;	
	}
	function insertAssetRequestTransfers($get, $post){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$return['post'] = $post;
		$assetids = json_decode($post['asset_id'],1);
		foreach($assetids as $assetid){
			$insert_array = array('company_id' =>$post['company_id'],'asset_request_id'=>$post['rid'],'asset_id'=>$assetid,'start_date'=>$this->dateconvertdbformat($post['start_date']),'end_date'=>$this->dateconvertdbformat($post['end_date']),'originator'=>$post['user_id'],'description'=>$post['description'],'asset_transfer_date'=>date('Y-m-d'),'delivery_type'=>$post['delivery_type'],'delivery_address'=>$post['delivery_address'],'is_active' =>'0','current_status'=>'0');
			$res = $callConfig->insertRecord('asset_request_transfers',$insert_array);
		}
		$return['res'] = $res;
		

		$asset_requestssql = "SELECT ar.originator FROM asset_request_transfers art, asset_requests ar WHERE art.company_id = '".$get['company_id']."' AND art.company_id = ar.company_id AND art.asset_request_id = ar.id AND ar.art = '".$get['rid']."' AND art.is_active = ar.is_active AND art.is_active = 0";
		$asset_requestsres=$callConfig->getRow($asset_requestssql);


		$user_ids = array('0'=>$post['user_id']);
		$msg = "Asset Request Transfer Successfully";
		$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$insert_requstres['LASTID'],'application_table_name'=>'asset_transfer_requests', 'type'=>'INFORMATION', 'notification_users'=>$user_ids,'option'=>'COM_LIST_ASSET_REQUESTS');
		$sendnotification = $this->tosendnotifiations($notdataa);
		$notdataa['option'] = "";
		$notdataa['type'] = "ACTION";
		$notdataa['notification_users'] = array('0'=>$asset_requestsres->originator);
		$sendnotification = $this->tosendnotifiations($notdataa);

		return $return;	
	}

	function getactiveassetanystage($get){
		global $callConfig;
		$sql1 = "SELECT asset_id FROM `asset_request_transfers` WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$res1=$callConfig->getAllRows($sql1);
		$asset_ids = array();
		foreach($res1 as $asres1){
			$asset_ids[$asres1->asset_id] = $asres1->asset_id;
		}
		$asset_id = implode(",",(array)$asset_ids);
		return $asset_id;
	}
	function insertAssetVerification($get,$post){
		global $callConfig;
		$assetary = explode(',', $post['asset_id']);
		foreach($assetary as $asset_id){
			$insert_array = array('company_id' =>$post['company_id'],'asset_id' =>$asset_id,'user_id'=>$post['verification_user_id'],'start_date_time'=>$post['start_date_time'],'end_date_time'=>$post['end_date_time'],'is_active'=>'0','current_status'=>'0','created_date'=>date('Y-m-d H:i:s'),'created_by'=>$post['user_id']);
			$res[] = $callConfig->insertRecord('asset_verifications',$insert_array);
		}

		$user_ids = array('0'=>$post['verification_user_id']);
		$msg = "Asset Verification Schedule From - ".$post['start_date_time']." TO ".$post['end_date_time'];
		$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$insert_requstres['LASTID'],'application_table_name'=>'asset_transfer_requests', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>'COM_VIEW_ASSET_VERIFICATIONS');
		$sendnotification = $this->tosendnotifiations($notdataa);
		return $res;
	}

	
	function getAssetVerifications($get){
		global $callConfig;
		$return = array();
		$todaytedate = date('Y-m-d');
		$tomorrowdate = date('Y-m-d', strtotime('+1 days', strtotime($todaytedate)));
		if($get['verification_id'] !=""){
			$query = "SELECT ass.*, (SELECT ac.category_description FROM asset_categories ac WHERE ac.company_id = ass.company_id AND ac.id = ass.category_id AND ac.is_active = ass.is_active) as category_name,(SELECT act.condition_name FROM asset_condition_type act WHERE act.company_id = ass.company_id AND act.id = ass.conditions AND act.is_active = ass.is_active) as condition_name FROM asset_verifications av, assets ass WHERE av.company_id=".$get['company_id']." AND av.company_id = ass.company_id AND av.asset_id = ass.id AND av.id='".$get['verification_id']."' AND av.is_active = ass.is_active AND av.is_active = '0'";
			$result = $callConfig->getRow($query);
			$return['asset_verifications']= $result;
			$asset_condition_typesql = "SELECT id,condition_name FROM asset_condition_type WHERE is_active =0 AND company_id = '".$get['company_id']."'";
			$asset_condition_typeres=$callConfig->getAllRows($asset_condition_typesql);
			foreach($asset_condition_typeres as $assetct){
				$assetcondition[$assetct->id] = $assetct->condition_name;
			}
			$return['asset_condition_type'] = $assetcondition;

			$locationsql = "SELECT id,location_name FROM locations WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$locationsres=$callConfig->getAllRows($locationsql);
			$locations = array();
			foreach($locationsres as $loc){
				$locations[$loc->id] = $loc;
			}
			$return['locations'] = $locations;
			$return['locations_sql'] = $locationsql;
		}else{
			$admin_access_employee = $get['admin_access_employee'];
			if($get['admin_type'] !=1 OR $admin_access_employee =='Y'){
	    		$codition = " AND av.user_id = '".$get['user_id']."'";
	    	}
	    	if(isset($get['type']) AND $get['type'] !=""){
	    		if($get['type'] == "IP"){
	    			$codition .= " AND av.start_date_time <= '".$todaytedate." 23:59:59'  AND av.current_status != '".ST_APPROVED."' ";
	    		}elseif($get['type'] == "UCV"){	
	    			$codition .= " AND  av.start_date_time >= '".$tomorrowdate." 00:00:00' AND av.current_status != '".ST_APPROVED."'";
	    		}elseif($get['type'] == "COMPL"){
	    			$codition .= " AND av.current_status = '".ST_APPROVED."'";
	    		}
	    	}
			$query = "SELECT av.id, av.asset_id, av.user_id, av.start_date_time, av.end_date_time, av.verification_date_time, av.verification_date_time, av.user_note,  av.current_status, ass.asset_code, ass.asset_description, ass.asset_location FROM asset_verifications av, assets ass WHERE av.company_id=".$get['company_id']." AND av.company_id = ass.company_id AND av.asset_id = ass.id ".$codition." AND av.is_active = ass.is_active AND av.is_active = '0'";
			$result = $callConfig->getAllRows($query);
			$return['query']= $query;
			$return['asset_verifications']= $result;

			$query = "SELECT count(id) as total, sum(if(start_date_time <= '".$todaytedate." 23:59:59'  AND current_status != '".ST_APPROVED."',1,0)) as inprocess_cnt, sum(if(start_date_time >= '".$tomorrowdate." 00:00:00' AND current_status != '".ST_APPROVED."',1,0)) as upcoming_cnt, sum(if(current_status = '".ST_APPROVED."',1,0)) as completed FROM `asset_verifications` WHERE company_id = '".$get['company_id']."' AND user_id = '".$get['user_id']."' AND is_active = 0";
			$result = $callConfig->getRow($query);
			$return['asset_verifications_cnt_sql']= $query;
			$return['asset_verifications_cnt']= $result;


			$locationsql = "SELECT id,location_name FROM locations WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$locationsres=$callConfig->getAllRows($locationsql);
			$return['locations'] = $locationsres; 
		}
		return $return;
	}	
	function updateAssetVerificationStatus($get, $post){
		global $callConfig;
		$return = array();
		$insert_array = array('verification_date_time'=>date('Y-m-d H:i:s'),'user_note'=>$post['user_note'],'current_status'=>ST_APPROVED,'updated_date'=>date('Y-m-d H:i:s'),'updated_by'=>$post['user_id']);
		$where = "id = '".$post['verification_id']."'";
		$res=$callConfig->updateRecord("asset_verifications",$insert_array,"","",$where);
		$return['asset_verifications'] =$res; 

		$insert_array = array('conditions'=>$post['conditions'],'asset_img'=>$post['asset_img'],'updated_date'=>date('Y-m-d H:i:s'),'updated_by'=>$post['user_id']);
		$where = "id = '".$post['asset_id']."'";
		$res=$callConfig->updateRecord("assets",$insert_array,"","",$where);
		$return['assets'] =$res; 
//'".$from." 00:00:00' and '".$to." 23:59:59'
		return $return;
	}
	function assetAccepts($get,$post){
		global $callConfig;
		$asset_request_transfer_id = $post['asset_request_transfer_id'];
		$asset_request_transferssql = "SELECT id, asset_id,  originator, start_date, end_date FROM asset_request_transfers WHERE company_id = '".$get['company_id']."' AND id = '".$asset_request_transfer_id."' AND is_active =0";
		$asset_request_transfersres=$callConfig->getRow($asset_request_transferssql);
		$returnresult['asset_request_transfers']  = $asset_request_transfersres;
		$assetupdatearray = array('updated_date'=>date('Y-m-d H:i:s'),'updated_by'=>$post['user_id'],'asset_accept_or_reject_date'=>$this->dateconvertdbformat($post['received_date']),'asset_accept_or_reject_note'=>$post['received_note'],'current_status'=>$post['current_status']);
		$where = "id = '".$post['asset_request_transfer_id']."'";
		$assetupdate=$callConfig->updateRecord("asset_request_transfers",$assetupdatearray,"","",$where);
		$returnresult['asset_request_transfers_update']  = $assetupdate;

		if($asset_request_transfersres->originator !=""){

			$insert_array = array('company_id' =>$post['company_id'],'asset_id'=>$asset_request_transfersres->asset_id,'user_id'=>$get['user_id'],'start_date'=>$asset_request_transfersres->start_date,'end_date'=>$asset_request_transfersres->end_date,'received_note'=>$post['received_note'],'received_date'=>$this->dateconvertdbformat($post['received_date']),'is_active' =>'0','current_status'=>'0');
			$res = $callConfig->insertRecord('user_assets',$insert_array);
			$returnresult['user_assets']  = $res;
			if($post['current_status'] == ST_APPROVED){
			 	$msg = "Asset Successfully Accepted";
			}else{
				$msg = "Asset Successfully Rejected";
			}
			$user_ids = array('0'=>$asset_request_transfersres->originator,'1'=>$post['user_id']);
			$option = "";
			$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$post['asset_id'],'application_table_name'=>'asset_request_transfers', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
			$sendnotification = $this->tosendnotifiations($notdataa);
		}

		// if($post['allocation_type'] == '2'){
		// 	$msg = "Asset Rejected";
		// 	$res['asset_trans_type'] = '4';
		// 	$assetupdatearray = array('conditions'=>$post['conditions'][$asset_idkey],'allocation_status'=>'N');	
		// }else{
		// 	$msg = "Asset Accepted";
		// 	$res['asset_trans_type'] = '5';
		// 	$assetupdatearray = array('conditions'=>$post['conditions'][$asset_idkey],'allocation_status'=>'Y');	
		// }	
		// $get['employee_id'] = $post['emp_id'];
		// $get['company_id'] = $post['company_id'];
		// $user_id  =  $this->getempconnect($get); 
		// $user_ids = array('0'=>$user_id['admin_connect']);
		// $option = "COM_LIST_ASSET_ALLOCATIONS";
		// $notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$post['asset_id'],'application_table_name'=>'user_assets', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
		// $sendnotification = $this->tosendnotifiations($notdataa);
		//$returnresult['history'] = $this->insertAssetTransaction($post,$table,$res,$assetupdatearray);
		return $returnresult;
	}
function updateAssetAllocation($get, $post){
		global $callConfig;
		$returncdc['post'] = $post;
		$table =  "Assets";
		foreach($post['asset_id'] as $key=>$rid){
			//$insert_array = array('asset_description' =>$post['asset_description'][$key],'category_id'=>$post['category_id'][$key],'serial_no' =>$post['serial_no'][$key],'asset_location'=>$post['asset_location'][$key],'asset_cost'=>$post['asset_cost'][$key],'accum_depn'=>$post['accum_depn'][$key],'purchase_date'=>$this->dateconvertdbformat($post['purchase_date'][$key]),'disposal_proceeds'=>$post['disposal_proceeds'][$key],'depn_rate'=>$post['depn_rate'][$key],'long_description'=>$post['long_description'][$key],'brand'=>$post['brand'][$key],'model'=>$post['model'][$key],'conditions'=>$post['conditions'][$key],'po_number'=>$post['po_number'][$key],'po_date'=>$this->dateconvertdbformat($post['po_date'][$key]),'invoice_number'=>$post['invoice_number'][$key],'Invoice_date'=>$this->dateconvertdbformat($post['Invoice_date'][$key]),'purchase_number'=>$post['purchase_number'][$key],'capitalization_price'=>$post['capitalization_price'][$key],'capitalization_date'=>$this->dateconvertdbformat($post['capitalization_date'][$key]),'end_of_life'=>$this->dateconvertdbformat($post['end_of_life'][$key]),'asset_status'=>$post['status'][$key]);
			$insert_array = array('asset_description' =>$post['asset_description'][$key],'category_id'=>$post['category_id'][$key],'serial_no' =>$post['serial_no'][$key],'asset_location'=>$post['asset_location'][$key],'asset_cost'=>$post['asset_cost'][$key],'accum_depn'=>$post['accum_depn'][$key],'purchase_date'=>$this->dateconvertdbformat($post['purchase_date'][$key]),'disposal_proceeds'=>$post['disposal_proceeds'][$key],'depn_rate'=>$post['depn_rate'][$key],'long_description'=>$post['long_description'][$key],'brand'=>$post['brand'][$key],'model'=>$post['model'][$key],'conditions'=>$post['conditions'][$key],'asset_status'=>$post['status'][$key]);
			$insert_array['updated_date'] = date('Y-m-d H:i:s');
			$insert_array['updated_by'] = $post['user_id'];
			$where = "id = '".$rid."'";
			$res=$callConfig->updateRecord("assets",$insert_array,"","",$where);
			$returncdc['assets_update'][] = $res;
			$returncdc['res'][] = $insert_array;
			$lastid['LASTID'] = $rid;
			$lastid['asset_id'] = $rid;
			$lastid['asset_trans_type'] = '2'; // Update Assets
			$returncdc['assettransaction'][] = $this->insertAssetTransaction($post,$table,$lastid);
		}
		return $returncdc;
	}
function insertAsset($post){
	global $callConfig;
	$returncdc['post'] = $post;
	$qruploadDir =  SITEROOT.'/uploads/equipments/qr_code/';
	$table = "assets";
	$attributes =implode(",",(array)$post['attribute']);
	foreach($post['depreciation_date'] as $keyid=>$valu){
		$depreciation_table[] =array('depreciation_date'=>$valu,'per_year_deprciation'=>$post['per_year_deprciation'][$keyid],'accumulated_depreciation'=>$post['accumulated_depreciation'][$keyid],'depreciation_price'=>$post['depreciation_price'][$keyid]); 
	}
	$depreciationtable = json_encode((array)$depreciation_table);

	$insert_array = array('company_id' =>$post['company_id'],'asset_description' =>$post['asset_description'],'category_id'=>$post['category_id'],'serial_no' =>$post['serial_no'],'is_active' =>'0','current_status'=>0,'asset_location'=>$post['asset_location'],'asset_cost'=>$post['asset_cost'],'accum_depn'=>$post['accum_depn'],'purchase_date'=>$this->dateconvertdbformat($post['purchase_date']),'disposal_proceeds'=>$post['disposal_proceeds'],'depn_rate'=>$post['depn_rate'],'long_description'=>$post['long_description'],'asset_img'=>$post['asset_img'],'brand'=>$post['brand'],'model'=>$post['model'],'conditions'=>$post['conditions'],'Parent_asset'=>$post['Parent_asset'],'vendor'=>$post['vendor'],'ownership'=>$post['ownership'],'rented_from'=>$post['rented_from'],'po_number'=>$post['po_number'],'po_date'=>$this->dateconvertdbformat($post['po_date']),'invoice_number'=>$post['invoice_number'],'Invoice_date'=>$this->dateconvertdbformat($post['Invoice_date']),'purchase_number'=>$post['purchase_number'],'capitalization_price'=>$post['capitalization_price'],'capitalization_date'=>$this->dateconvertdbformat($post['capitalization_date']),'end_of_life'=>$this->dateconvertdbformat($post['end_of_life']),'custom_field'=>$post['custom_field'],'attribute'=>$attributes,'asset_status'=>$post['status'],'asset_custodian'=>$post['asset_custodian'],'warranty_expiry_date'=>$this->dateconvertdbformat($post['warranty_expiry_date']),'depreciation_method'=>$post['depreciation_method'],'duration'=>$post['duration'],'depreciation_account'=>$post['depreciation_account'],'expense_account'=>$post['expense_account'],'journal_id'=>$post['journal_id'],'depreciation_table'=>$depreciationtable);
	if($post['Rid'] !=""){

		//QR Code Generater
		$assetssql = "SELECT id,asset_code,qr_code FROM assets WHERE company_id = '".$post['company_id']."' AND id = '".$post['Rid']."' AND is_active = 0";
		$assetsres = $callConfig->getRow($assetssql);
		if (file_exists($qruploadDir.$assetsres->qr_code)) {
			unlink($qruploadDir.$assetsres->qr_code);
		}
		$text = $assetsres->asset_code."-".$post['asset_description'];
		$qrfile_name = $this->creatQRCode($text,$qruploadDir); // Creater QR Code
		$insert_array['qr_code'] = $qrfile_name;
		//QR Code Generater

		$insert_array['updated_date'] = date('Y-m-d H:i:s');
		$insert_array['updated_by'] = $post['user_id'];
		$where = "id = '".$post['Rid']."'";
		$res=$callConfig->updateRecord("assets",$insert_array,"","",$where);
		$lastid['LASTID'] = $post['Rid'];
		$lastid['asset_id'] = $post['Rid'];
			$lastid['asset_trans_type'] = '2'; // Update Assets
			$returncdc['assettransaction'] = $this->insertAssetTransaction($post,$table,$lastid);
		}else{
			$assets_transaction_id = $this->toGetApplicationSerialNumber('assets');
			$insert_array['asset_code'] = $assets_transaction_id;
		//QR Code Generater
			$text = $assets_transaction_id."-".$post['asset_description'];
			$qrfile_name = $this->creatQRCode($text,$qruploadDir); // Creater QR Code
			$insert_array['qr_code'] = $qrfile_name;
		//QR Code Generater asset_code
			$insert_array['is_active'] = '0';
			$insert_array['created_date'] = date('Y-m-d H:i:s');
			$insert_array['created_by'] = $post['user_id']; 
			$insert_array['current_status'] = '0';
			$res = $callConfig->insertRecord('assets',$insert_array);
			if($res['status'] == 'sucess'){
				$res['asset_id']  = $res['LASTID'];
				$res['asset_trans_type'] = '1'; // Create Assets
				$returncdc['assettransaction'] = $this->insertAssetTransaction($post,$table,$res);
			}
		}
		$returncdc['insert_array'] = $insert_array;
		$returncdc['res'] = $res;
		return $returncdc;
	}


	function insertAssetReturn($get, $post){
		global $callConfig;
		$return['get'] = $get; 
		$return['post'] = $post;

		$user_assetssql = "SELECT * FROM user_assets WHERE company_id =".$post['company_id']." AND id = '".$post['user_asset_id']."' AND is_active =0";
		$user_assetsres = $callConfig->getRow($user_assetssql);
		$return['user_assetsres'] = $user_assetsres;
		if($user_assetsres->id !=""){
			if($post['accept_status'] == "Y"){

				$assetupdatearray = array('updated_date'=>date('Y-m-d H:i:s'),'updated_by'=>$post['user_id'],'receive_datetime'=>$this->dateconvertdbformat($post['received_date']),'receiver_comment'=>$post['received_note'],'current_status'=>$post['current_status']);
				$where = "id = '".$post['asset_return_id']."'";
				$assetupdate=$callConfig->updateRecord("asset_returns",$assetupdatearray,"","",$where);
				$returnresult['asset_request_transfers_update']  = $assetupdate;
				

				$assetupdatearray = array('updated_date'=>date('Y-m-d H:i:s'),'updated_by'=>$post['user_id'],'asset_return_status'=>'Y','asset_return_id'=>$post['asset_return_id']);
				$where = "id = '".$user_assetsres->id."'";
				$assetupdate=$callConfig->updateRecord("user_assets",$assetupdatearray,"","",$where);
				$returnresult['user_assets_update']  = $assetupdate;

				if($post['current_status'] == ST_APPROVED){
				 	$msg = "Asset Successfully Received";
				}else{
					$msg = "Asset Successfully Not-Received";
				}
				$user_ids = array('0'=>$user_assetsres->user_id,'1'=>$post['user_id']);
				$option = "";
				$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$post['asset_id'],'application_table_name'=>'asset_returns', 'type'=>'INFORMATION', 'notification_users'=>$user_ids,'option'=>$option);
				$sendnotification = $this->tosendnotifiations($notdataa);

			}else{
				$assetssql = "SELECT id, asset_custodian FROM assets WHERE company_id =".$post['company_id']." AND id = '".$user_assetsres->asset_id."' AND is_active =0";
				$assetsres = $callConfig->getRow($assetssql);
				$return['assets'] = $assetsres;
				$insert_array = array('company_id' =>$post['company_id'],'asset_id'=>$user_assetsres->asset_id,'user_asset_id'=>$user_assetsres->id,'originator'=>$post['user_id'],'return_date'=>$this->dateconvertdbformat($post['return_date']),'description'=>$post['received_note'],'approval_user_id'=>$assetsres->asset_custodian,'request_datetime'=>date('Y-m-d H:i:s'),'is_active' =>'0','current_status'=>'0');
				$res = $callConfig->insertRecord('asset_returns',$insert_array);
				$return['res']  =$res;

				//$this->insertAssetTransaction($post,$table,$res,$assetupdatearray);

				$user_ids = array('0'=>$assetsres->asset_custodian);
				$msg = "Asset Return Request - ".$res['LASTID'];
				$option = "COM_LIST_ASSET_RETURNS";
				$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$insert_requstres['LASTID'],'application_table_name'=>'asset_requests', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
				$returnresult['notification_array']  = $notdataa;
				$sendnotification = $this->tosendnotifiations($notdataa);
				$notdataa['option'] = "";
				$notdataa['type'] = "ACTION";
				$notdataa['notification_users'] = array('0'=>$user_assetsres->user_id);
				$sendnotification = $this->tosendnotifiations($notdataa);
			}	
		}
		return $return;		
	}






































	function getAssetAllocation_old($get){
		global $callConfig;
		$conditionassetlist = "";
		$empassetcoditon = "";
		$todaytedate = date('Y-m-d');
		$admin_access_employee = $get['admin_access_employee'];
		if($get['admin_type'] !=1 OR $admin_access_employee =='Y'){
    		$codition = " AND av.user_id = '".$get['user_id']."'";
    	}
		if($get['list']=='Y'){
			if(isset($get['assetid']) AND $get['assetid'] !=""){
				$conditionassetlist .= " AND ass.id IN(".$get['assetid'].")";
			}
			if(isset($get['ownership']) AND $get['ownership'] !=""){
				$conditionassetlist .= " AND ass.ownership  = '".$get['ownership']."'";
			}
			if(isset($get['allocation_status']) AND $get['allocation_status'] !=""){
				$conditionassetlist .= " AND ass.allocation_status = '".$get['allocation_status']."'";
			}
			if(isset($get['parent_asset']) AND $get['parent_asset'] !=""){
				if($get['parent_asset'] == "Y"){
					$conditionassetlist  .= " AND ass.parent_asset !=''";
				}else{
					$conditionassetlist .= " AND ass.parent_asset IS NULL";	
				}
			}
			if(isset($get['category']) AND $get['category'] !=""){
				$conditionassetlist .= " AND ass.category_id  = '".$get['category']."'";
			}
			if(isset($get['location']) AND $get['location'] !=""){
				$conditionassetlist .= " AND ass.asset_location  = '".$get['location']."'";
			}
			if(isset($get['asset_owner']) AND $get['asset_owner'] !=""){
				$conditionassetlist .= " AND emp_ass.user_id = '".$get['asset_owner']."'";
				//$empassetcoditon .=" AND emp_ass.emp_id = '".$get['employee']."'";
			}
			if(isset($get['status']) AND $get['status'] !=""){
				$conditionassetlist .= " AND ass.asset_status ='".$get['status']."'";
			}
			if(isset($get['candition']) AND $get['candition'] !=""){
				$conditionassetlist .= " AND ass.conditions  = '".$get['candition']."'";
			} 
			$assetsql = "SELECT ass.id,ass.asset_code,ass.asset_description,ass.category_id,ass.serial_no,ass.asset_location,ass.asset_img,ass.brand,ass.model,ass.conditions,ass.asset_status,ass.asset_owner,ass.allocation_status,ass.attribute, ass.ownership, ass.allocation_status, ass.Parent_asset, ass.current_status, emp_ass.end_date,emp_ass.remarks,emp_ass.allocation_type,emp_ass.id as emp_asset_id,emp_ass.attribute as emp_attribute, emp_ass.user_id as user_assets_user_id FROM assets ass LEFT JOIN user_assets emp_ass on ass.is_active =emp_ass.is_active AND ass.company_id = emp_ass.company_id AND ass.id = emp_ass.asset_id WHERE ass.is_active =0 AND ass.company_id = '".$get['company_id']."'".$conditionassetlist;
			$returnresult['list_assets_sql'] = $assetsql;
			$assets=$callConfig->getAllRows($assetsql);
			$returnresult['list_assets'] = $assets;
			$assetid = array();
			foreach($assets as $assets2){
				$assetid[$assets2->id] = $assets2->id;
			}
			$asidlist = implode(',', (array)$assetid);
			$sql1 ="SELECT sum(if(end_date <= '".$todaytedate."',1,0) AND allocation_type !=3) as overduecnt, sum(if(end_date >= '".$todaytedate."',1,0) AND allocation_type !=3) as duesooncnt FROM `user_assets` WHERE company_id = '".$get['company_id']."' AND asset_id IN(".$asidlist.") AND is_active = 0";
			$res1=$callConfig->getRow($sql1);
			$returnresult['user_asset_cnt'] = $res1;
			$sql2 ="SELECT sum(if(warranty_expiry_date <= '".$todaytedate."' AND  warranty_expiry_date !='0000-00-00',1,0)) as warranty_expiry_cnt, sum(if(end_of_life >= '".$todaytedate."'  AND end_of_life !='0000-00-00',1,0)) as end_of_life, sum(if(allocation_status ='N',1,0)) as unallocatedcnt FROM assets WHERE company_id = '".$get['company_id']."' AND id IN(".$asidlist.") AND is_active = 0";
			$res2=$callConfig->getRow($sql2);
			$returnresult['asset_cnt'] = $res2;
		}

		if($get['assetupdate']=='Y'){
			if(isset($get['assetid']) AND $get['assetid'] !=""){
				$conditionassetlist .= " AND ass.id IN(".$get['assetid'].")";
			}
			$assetsql = "SELECT ass.*,emp_ass.end_date,emp_ass.remarks,emp_ass.id as emp_asset_id,emp_ass.attribute as emp_attribute FROM assets ass LEFT JOIN user_assets emp_ass on ass.is_active =emp_ass.is_active AND ass.company_id = emp_ass.company_id  AND ass.id = emp_ass.asset_id WHERE ass.is_active =0 AND ass.company_id = '".$get['company_id']."'".$conditionassetlist;
			$assets=$callConfig->getAllRows($assetsql);
			$returnresult['list_assets'] = $assets;
		}

		if($get['assetverifications']=='Y'){
			$assetsql = "SELECT id, asset_code, asset_description, asset_owner, asset_custodian FROM assets WHERE is_active =0 AND company_id = '".$get['company_id']."' AND id IN(".$get['assetid'].")";
			$assets=$callConfig->getAllRows($assetsql);
			$returnresult['list_assets'] = $assets;

			$asset_verificationssql = "SELECT id, asset_id, start_date_time, end_date_time FROM asset_verifications WHERE is_active =0 AND company_id = '".$get['company_id']."' AND asset_id IN(".$get['assetid'].") AND current_status !='".ST_APPROVED."'";
			$asset_verificationsres=$callConfig->getAllRows($asset_verificationssql);
			foreach($asset_verificationsres as $asset_verifications){
				$asset_verificationsarray[$asset_verifications->asset_id] = $asset_verifications;
			}
			$returnresult['asset_verifications'] = $asset_verificationsarray;

		}

		if($get['momenthistory']=='Y'){
			$assetsql = "SELECT * FROM asset_transactions WHERE is_active =0 AND company_id = '".$get['company_id']."' AND asset_code  IN(".$get['assetid'].")";
			$assets=$callConfig->getAllRows($assetsql);
			$returnresult['momenthistory'] = $assets;
		}

		if($get['request']=='Y'){   // request page sqls
			$requestsql = "SELECT * FROM asset_transfer_requests WHERE is_active =0 AND company_id = '".$get['company_id']."' AND id  IN(".$get['req_id'].")";
			$requestres=$callConfig->getRow($requestsql);
			$returnresult['request'] = $requestres;
			$attributessql = "SELECT id,name FROM asset_attributes WHERE is_active =0 AND company_id = '".$get['company_id']."' AND category_id IN(".$requestres->asset_category_id.")";
			$attributesres=$callConfig->getAllRows($attributessql);
			$returnresult['attributes'] = $attributesres;

			if($requestres->asset_category_id != ""){
				$conditionrequest .= " AND ass.category_id  = '".$requestres->asset_category_id."'";
			}
			if(isset($get['categroy_list']) AND $get['categroy_list'] !=""){
				$conditionrequest .= " AND ass.category_id  IN (".$get['categroy_list'].")";
			}
			if(isset($get['asset_status']) AND $get['asset_status'] !=""){
				$conditionrequest .= " AND ass.asset_status IN('".$get['asset_status']."')";
			}
			if(isset($get['asset_condition']) AND $get['asset_condition'] !=""){
				$conditionrequest .= " AND ass.conditions IN ('".$get['asset_condition']."')";
			}
			$assetssql = "SELECT ass.id,ass.asset_code,ass.asset_description,ass.category_id,ass.serial_no,ass.asset_location,ass.asset_img,ass.brand,ass.model,ass.conditions,ass.asset_status,ass.asset_owner,emp_ass.end_date,ass.allocation_status FROM assets ass LEFT JOIN user_assets emp_ass on ass.is_active =emp_ass.is_active AND ass.company_id = emp_ass.company_id  AND ass.id = emp_ass.asset_id WHERE ass.is_active =0 AND ass.company_id = '".$get['company_id']."' ".$conditionrequest;
			$assetrespons=$callConfig->getAllRows($assetssql);
			$returnresult['request_list_asset'] = $assetrespons;
		}

	//used allocation popup
	$costcentersql = "SELECT id,cost_center_name FROM cost_centers WHERE company_id = '".$get['company_id']."' AND is_active =0";
	$costcenter=$callConfig->getAllRows($costcentersql);
	$returnresult['costcenter'] = $costcenter; 
	//$employeessql = "SELECT id,display_name FROM employees WHERE date_of_leaving IS NULL AND company_id = '".$get['company_id']."' AND is_active =0";

	$employeessql = "SELECT id,display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active =0";
	$employees=$callConfig->getAllRows($employeessql);
	$returnresult['employees'] = $employees; 
	//end allocation popup
	$asset_categorie = "SELECT id,category_description,depreciation_account,disposal_account,Journal_id,duration,duration_type FROM  asset_categories WHERE  company_id = '".$get['company_id']."' AND is_active =0";
	$asset_categorieres=$callConfig->getAllRows($asset_categorie);
	$returnresult['asset_categories'] = $asset_categorieres; 
	$locationsql = "SELECT id,location_name FROM locations WHERE company_id = '".$get['company_id']."' AND is_active =0";
	$locationsres=$callConfig->getAllRows($locationsql);
	$returnresult['locations'] = $locationsres; 
	$assetstatussql = "SELECT id,name FROM asset_status WHERE company_id = '".$get['company_id']."' AND is_active =0";
	$assetstatusres=$callConfig->getAllRows($assetstatussql);
	$returnresult['asset_status'] = $assetstatusres; 
	$departmentsql = "SELECT id,department_name FROM departments WHERE company_id = '".$get['company_id']."' AND is_active =0";
	$departmentres=$callConfig->getAllRows($departmentsql);
	$returnresult['departments'] = $departmentres; 

	$asset_condition_typesql = "SELECT id,condition_name FROM asset_condition_type WHERE is_active =0 AND company_id = '".$get['company_id']."'";
	$asset_condition_typeres=$callConfig->getAllRows($asset_condition_typesql);
	foreach($asset_condition_typeres as $assetct){
		$assetcondition[$assetct->id] = $assetct->condition_name;
	}
	$returnresult['asset_condition_type'] = $assetcondition;

	$user = "SELECT id,display_name FROM application_users WHERE is_active =0 AND company_id = '".$get['company_id']."'";
	$users=$callConfig->getAllRows($user);
	$returnresult['users'] = $users;
	if($get['Rid'] !=""){
		$assetsql = "SELECT * FROM assets WHERE is_active =0 AND company_id = '".$get['company_id']."' AND id=".$get['Rid'];
		$assets=$callConfig->getRow($assetsql);
		$returnresult['assets'] = $assets;
	}
	$assetlistsql = "SELECT id,asset_description,asset_code  FROM assets WHERE is_active =0 AND company_id = '".$get['company_id']."'";
	$assetslist=$callConfig->getAllRows($assetlistsql);
	$returnresult['assets_list'] = $assetslist;
	$vendorsql = "SELECT id,display_name  FROM vendors WHERE is_active =0 AND company_id = '".$get['company_id']."'";
	$vendorlist=$callConfig->getAllRows($vendorsql);
	$returnresult['vendors'] = $vendorlist;


	$depreciationmethodsql = "SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id = ".$get['company_id']." AND genc_code = 'DEPRECIATIONMETHOD' AND is_active =0";
    $depreciationmethodres = $callConfig->getAllRows($depreciationmethodsql);
    $returnresult['depreciation_method']= $depreciationmethodres;

     $chart_of_accountssql = "SELECT id, account_code, account_name FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND is_active =0 ORDER BY display_priority ASC";
    $chart_of_accountsres = $callConfig->getAllRows($chart_of_accountssql);
    $returnresult['chart_of_accounts']= $chart_of_accountsres;
      

    $journalssql = "SELECT id, journal_name FROM `journals` WHERE company_id = ".$get['company_id']." AND is_active =0 ORDER BY journal_name ASC";
    $journalsres = $callConfig->getAllRows($journalssql);
    $returnresult['journals']= $journalsres;

	return $returnresult;
} 

	function insertAssetAllocation($get,$post){
		global $callConfig;
		$table = "user_assets";
		if($post['discardasset']=="Y"){ //single asset alocation request page
			foreach($post['asset_id'] as $key=>$rid){
				$assetupdatearray = array('updated_date'=>date('Y-m-d H:i:s'),'updated_by'=>$post['user_id'],'discard_and_sales_date'=>$this->dateconvertdbformat($post['discard_and_sales_date']),'discard_and_sales_remarks'=>$post['discard_and_sales_remarks'],'is_active'=>NULL);
				$where = "id = '".$rid."'";
				$assetupdate=$callConfig->updateRecord("assets",$assetupdatearray,"","",$where);
			}
		}elseif($post['singleasset']=="Y"){ //single asset alocation request page
			$attributes =implode(",",(array)$post['attribute']);
			$emp_id = $post['employee_id'];
			// $getuseridemployeesql = "SELECT user_id FROM employees WHERE is_active =0 AND company_id = '".$get['company_id']."' AND id = '".$emp_id."'";
			// $useriid=$callConfig->getRow($getuseridemployeesql);
			$asset_id = $post['asset_id'];
			$insert_array = array('company_id' =>$post['company_id'],'is_active' =>'0','current_status'=>'0','asset_id'=>$asset_id,'user_id'=>$emp_id,'start_date'=>$this->dateconvertdbformat($post['start_date']),'end_date'=>$this->dateconvertdbformat($post['end_date']),'remarks'=>$post['remarks'],'attribute'=>$attributes,'Location'=>$post['location_id'],'conditions'=>$post['candition'],'asset_deliver'=>$post['asset_deliver'],'asset_img'=>$post['asset_img']);
			$res = $callConfig->insertRecord('user_assets',$insert_array);
			$returnresult['res'][]  =$res;
			$res['asset_id'] = $asset_id;
			$res['asset_trans_type'] = '3'; //allocation status 3 - history
			$requestassetary['updated_date'] = date('Y-m-d H:i:s');
			$requestassetary['updated_by'] = $post['user_id'];
			$requestassetary['request_status'] = '1';
			$requestassetary['transferred_qty'] = $post['transferred_qty'];
			$requestassetary['pending_qty'] = $post['pending_qty'];
			$where = "id = '".$post['req_id']."'";
			$assetupdate=$callConfig->updateRecord("asset_transfer_requests",$requestassetary,"","",$where);
			$returnresult['assetupdate'][]  =$assetupdate;
			$assetupdatearray = array('asset_location'=>$post['location_id'],'conditions'=>$post['candition'],'asset_status'=>$post['status'],'allocation_status'=>'Y','user_id'=>$emp_id);
			$returnresult['history'][] = $this->insertAssetTransaction($post,$table,$res,$assetupdatearray);	
			$user_ids = array('0'=>$useriid->user_id);
			$user_ids = array('0'=>$emp_id);
			$msg = "Asset Allocate ".$res['LASTID'];
			$option = "COM_LIST_ASSET_ALLOCATIONS";
			$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$res['LASTID'],'application_table_name'=>'user_assets', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
			$returnresult['notification_array']  = $notdataa;
			$sendnotification = $this->tosendnotifiations($notdataa);
		}else{
			if($post['allocation_type'] == "CC"){ //cost center
				$employeesql = "SELECT id FROM employees WHERE is_active =0 AND company_id = '".$post['company_id']."' AND cost_center_id = '".$post['coast_center_id']."' AND role_id = '".COST_CENTER_ROLE."'";
				$employees=$callConfig->getRow($employeesql);
				$emp_id = $employees->id;
			}elseif($post['allocation_type'] == "D"){ // Departments 
				$employeesql = "SELECT id FROM employees WHERE is_active =0 AND company_id = '".$post['company_id']."' AND department_id = '".$post['departent_id']."' AND role_id = '".DEPARTMENT_ROLE."'";
				$employees=$callConfig->getRow($employeesql);
				$emp_id = $employees->id;
			}elseif($post['allocation_type'] == "E"){ // employees
				$emp_id = $post['employee_id'];
			}
			$getuseridemployeesql = "SELECT user_id FROM employees WHERE is_active =0 AND company_id = '".$post['company_id']."' AND id = '".$emp_id."'";
			$useriid=$callConfig->getRow($getuseridemployeesql);
			$user_ids = array('0'=>$useriid->user_id);
			$option = "COM_LIST_ASSET_ALLOCATIONS";
			if($emp_id != ""){
				foreach($post['asset_id'] as $asset_idkey=>$asset_id){
					$insert_array = array('company_id' =>$post['company_id'],'is_active' =>'0','current_status'=>'0','asset_id'=>$asset_id,'user_id'=>$emp_id,'start_date'=>$this->dateconvertdbformat($post['start_date']),'end_date'=>$this->dateconvertdbformat($post['end_date']),'remarks'=>$post['remarks'],'Location'=>$post['location_id'],'conditions'=>$post['candition'],'asset_deliver'=>$post['asset_deliver']);
					$res = $callConfig->insertRecord('user_assets',$insert_array);
					$msg = "Asset Allocate ".$res['LASTID'];				
					$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$res['LASTID'],'application_table_name'=>'user_assets', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
					$returnresult['notification_array']  = $notdataa;
					$sendnotification = $this->tosendnotifiations($notdataa);
					$returnresult['res'][]  =$res;
					$res['asset_id'] = $asset_id;
					$res['asset_trans_type'] = '3'; //allocation status 3 - history
					$assetupdatearray = array('asset_location'=>$post['asset_location'][$asset_idkey],'conditions'=>$post['conditions'][$asset_idkey],'asset_status'=>$post['asset_status'][$asset_idkey],'allocation_status'=>'Y','user_id'=>$emp_id);
					$returnresult['history'][] = $this->insertAssetTransaction($post,$table,$res,$assetupdatearray);	
				}
			}else{
				$returnresult['error'] = '404';
			}
		}	
		return $returnresult;
	}
	function insertAssetTransaction($post,$table,$res,$assetupdatearray=""){
		global $callConfig;
		//print_r($res);
		$returnresult['resdd'][] =$res;
		$asset_id = $res['asset_id'];
		if($assetupdatearray !=""){
			$assetupdatearray['updated_date'] = date('Y-m-d H:i:s');
			$assetupdatearray['updated_by'] = $post['user_id'];
			$where = "id = '".$asset_id."'";
			$assetupdate=$callConfig->updateRecord("assets",$assetupdatearray,"","",$where);
		}
		$oldassetsql = "SELECT * FROM assets WHERE is_active =0 AND company_id = '".$post['company_id']."' AND id ='".$asset_id."'";
		$oldassetsqldetyails=$callConfig->getRow($oldassetsql);
		$oldassetarray = (array) $oldassetsqldetyails;
		$jsonold = json_encode($oldassetarray);
		$returnresult['oldassetsqldetyails'][]  = $jsonold;

		$insert_history = array('company_id' =>$post['company_id'],'is_active' =>'0','current_status'=>'0','asset_code'=>$asset_id,'trans_type'=>$table,'trans_ref'=>$res['LASTID'],'trans_date'=>date("Y-m-d"),'asset_detail'=>$jsonold,'created_date'=>date('Y-m-d H:i:s'),'created_by'=>$post['user_id'],'asset_trans_type'=>$res['asset_trans_type']);
		$insert_history_res = $callConfig->insertRecord('asset_transactions',$insert_history);
		$returnresult['insert_history_res'][]  =$insert_history;
		return $returnresult;
	}
	function insertAssetExtension($get,$post){
		global $callConfig;
		$get['employee_id'] = $post['emp_id'];
		$get['company_id'] = $post['company_id'];
		//$user_id  =  $this->getempconnect($get);
		$returnresult['admin_buddy']  = $user_id; 
		if($post['extension_type'] == "A"){
			$attributes =implode(",",(array)$post['attribute']);
		}else{
			$attributes = json_encode(array("date"=>$post['extension_date']));
		}
		if($user_id['admin_connect'] != "NOT-CONFIGURED"){
			$insert_requst= array('request_type'=>'M','request_date'=>date("Y-m-d"),'company_id' =>$post['company_id'],'is_active' =>'0','current_status'=>'0','admin_buddy'=>$user_id['admin_connect'],'created_date'=>date('Y-m-d H:i:s'),'created_by'=>$post['user_id'],'required_by'=>$post['emp_id'],'asset_deliver'=>$post['asset_deliver'],'asset_id'=>$post['assetid'],'extension_details'=>$attributes,'extension_type'=>$post['extension_type']);
			$insert_requstres = $callConfig->insertRecord('asset_transfer_requests',$insert_requst);
			$returnresult['insert_request']  = $insert_requstres;
			$user_ids = array('0'=>$user_id['admin_connect']);
			$msg = "Exten Asset Request ".$insert_requstres['LASTID'];
			$option = "COM_LIST_ASSET_REQUESTS";
			$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$insert_requstres['LASTID'],'application_table_name'=>'asset_transfer_requests', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
			$returnresult['notification_array']  = $notdataa;
			$sendnotification = $this->tosendnotifiations($notdataa);
		}else{
			$returnresult['error'] = "404";
		}
		return $returnresult;
	}

	function insertAssetRequestold($get, $post){
		global $callConfig;
		$returnresult['post'] = $post;
		$get['employee_id'] = $post['emp_id'];
		$get['company_id'] = $post['company_id'];
		//$user_id  =  $this->getempconnect($get);
		$returnresult['admin_buddy']  = $user_id; 
		if($user_id['admin_connect'] != "NOT-CONFIGURED"){
			$attributes =implode(",",(array)$post['attribute']);
			$insert_requst= array('request_type'=>'A','request_date'=>date("Y-m-d"),'company_id' =>$post['company_id'],'is_active' =>'0','current_status'=>'0','asset_category_id'=>$post['asset_category_id'],'admin_buddy'=>$user_id['admin_connect'],'start_date'=>$this->dateconvertdbformat($post['start_date']),'return_date'=>$this->dateconvertdbformat($post['end_date']),'description'=>$post['description'],'created_date'=>date('Y-m-d H:i:s'),'created_by'=>$post['user_id'],'required_by'=>$post['emp_id'],'asset_deliver'=>$post['asset_deliver'],'asset_id'=>$post['asset_list_id'],'attribute'=>$attributes,'location_id'=>$post['location_id'],'requested_qty'=>$post['requested_qty']);
			$insert_requstres = $callConfig->insertRecord('asset_transfer_requests',$insert_requst);
			$returnresult['insert_request']  = $insert_requstres;
			$user_ids = array('0'=>$user_id['admin_connect']);
			$msg = "Asset Request ".$insert_requstres['LASTID'];
			$option = "COM_LIST_ASSET_REQUESTS";
			$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$insert_requstres['LASTID'],'application_table_name'=>'asset_transfer_requests', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
			$returnresult['notification_array']  = $notdataa;
			$sendnotification = $this->tosendnotifiations($notdataa);
		}else{
			$returnresult['error'] = "404";
		}
		return $returnresult;
	}

	function getAssetAndAttributes($get){ 
		global $callConfig;
		$assetsql = "SELECT id,asset_description FROM assets WHERE is_active =0 AND company_id = '".$get['company_id']."' AND category_id ='".$get['cat_id']."'";
		$assetdertails=$callConfig->getAllRows($assetsql);
		$returnarray['assetlist']= $assetdertails; 
		$assetsql = "SELECT id,name FROM asset_attributes WHERE is_active =0 AND company_id = '".$get['company_id']."' AND category_id ='".$get['cat_id']."'";
		$assetdertails=$callConfig->getAllRows($assetsql);
		$returnarray['asset_attributes']= $assetdertails; 
		return $returnarray;
	}
	function getassetTransferRequestsold($get){
		global $callConfig;
		$assettrareqsql = "SELECT id,request_date,required_by,admin_buddy,request_status,description FROM asset_transfer_requests WHERE is_active =0 AND company_id = '".$get['company_id']."' AND (required_by = '".$get['required_by']."' OR admin_buddy = '".$get['admin_buddy']."')";
		$assettrareqres=$callConfig->getAllRows($assettrareqsql);
		return $assettrareqres;	
	}
	
	/*End asset allocation */ 
	function getemployeeassetandassetsdata($get, $post){
		global $callConfig;
		//$whr = "emp_id=".$get['empid']." AND is_active=0";
		$equery = "SELECT eas.id id, eas.asset_id,  eas.start_date, eas.end_date, eas.remarks, eas.current_status, ast.asset_code, ast.serial_no, ast.asset_description FROM user_assets eas, assets ast WHERE eas.company_id = ast.company_id AND eas.asset_id = ast.id AND eas.user_id = ".$get['empid']."  AND eas.is_active = 0"; 
		$return['emp_assets'] = $callConfig->getAllRows($equery); 
		$equery = "SELECT id,asset_code,asset_description,category_id,serial_no FROM assets WHERE id NOT IN (SELECT asset_id FROM user_assets WHERE current_status =".ST_ALLOCATE." AND is_active = 0)";
		$listassets = $callConfig->getAllRows($equery); 
		foreach($listassets as $listasset){
			$assets[$listasset->id] = $listasset;
		}
		$return['assets'] = $assets; 
		$equery = $callConfig->selectQuery('asset_categories','id,category_description','','','','');
		$return['asset_categories'] = $callConfig->getAllRows($equery);
		$equery = "SELECT emp.id, emp.emp_code,emp.display_name,emp.email_id,loc.location_name FROM `employees` emp , locations loc WHERE emp.company_id = loc.company_id AND emp.location_id = loc.id AND emp.company_id = ".$get['company_id']." AND emp.id = ".$get['empid'];
		$return['employee'] = $callConfig->getRow($equery);		 
		return 	$return;
	}
	function addupdateassetsallocation($post, $get){
		global $callConfig;
		foreach($post['asset_id'] as $key=>$asset){
			if($asset!=""){
				$query = "SELECT id,current_status FROM user_assets WHERE company_id =".$get['company_id']." AND asset_id =".$asset." AND user_id =".$post['emp_id']." AND is_active =0";
				$adata = $callConfig->getRow($query);	

				if($post['start_date'][$key]!=""){  $start_date = date('Y-m-d', strtotime(str_replace('/', '-', $post['start_date'][$key]))); }else{ $start_date = $post['start_date'][$key]; }
				if($post['end_date'][$key]!=""){ $end_date = date('Y-m-d', strtotime(str_replace('/', '-', $post['end_date'][$key]))); }else{ $end_date = $post['end_date'][$key]; }
				if($adata->id!=""){
					$updatequery = "UPDATE user_assets SET start_date='".$start_date."',end_date='".$end_date."',remarks='".$post['remarks'][$key]."',updated_by=".$get['user_id'].",current_status=".$post['current_status'][$key]." WHERE id=".$adata->id;
					$res = $callConfig->executeQuery($updatequery);
					$resquery[] = $updatequery;
					if($post['current_status'][$key] == ST_RETURNED && $adata->current_status != ST_RETURNED){
						$rlist_assets[] = $asset;
					}
				}else{					
					$fieldnames=array('company_id'=>$get['company_id'],'user_id'=>$post['emp_id'],'asset_id'=>$asset,'start_date'=>$start_date,'end_date'=>$end_date,'remarks'=>$post['remarks'][$key],'is_active'=>'0','current_status'=>ST_ALLOCATE,'created_by'=>$get['user_id'],'created_date'=>date('Y-m-d h:i:s'));
					$res=$callConfig->insertRecord('user_assets',$fieldnames);
					$alist_assets[] = $asset;
				} 
			}
		}
		$query = "SELECT id,display_name,user_id,it_buddy,admin_buddy FROM employees WHERE id =".$post['emp_id'];
		$emp_data = $callConfig->getRow($query);
		if($emp_data->it_buddy!="") { $adminusers[] = $emp_data->it_buddy; }
		if($emp_data->admin_buddy!="") { $adminusers[] = $emp_data->admin_buddy; }
		$query = "SELECT id,display_name,user_id,it_buddy,admin_buddy FROM employees WHERE id IN(".implode(",",(array)$adminusers).")";
		$empusser_data = $callConfig->getAllRows($query); 
		foreach($empusser_data as $empusser_da){
			$adminusers[]=$empusser_da->user_id;
		}
		if(count((array)$alist_assets)>0){
			$query = "SELECT id,asset_code,asset_description FROM assets WHERE id IN(".implode(",",(array)$alist_assets).")";
			$asdata = $callConfig->getAllRows($query);
			foreach($asdata as $astdata){
				$namerassets[] = $astdata->asset_description." - ".$astdata->asset_code;
			}
			$notiarray = array('notification_message'=>'Following asset(s) allocated to you '.implode(",",(array)$namerassets),'notification_users'=>array($emp_data->user_id),'id'=>$res['LASTID'],'application_table_name'=>'user_assets','company_id'=>$get['company_id'],'option'=>'','notification_datetime'=>date('y-m-d h:i:s'),'user_name'=>$emp_data->display_name,'list_assets'=>implode(",",(array)$namerassets),'email_content_id'=>25);
			$this->tosendnotifiations($notiarray);
			$notiarray = array('notification_message'=>'Following asset(s) allocated to '.$emp_data->display_name.' - '.implode(",",(array)$namerassets),'notification_users'=>$adminusers,'id'=>$res['LASTID'],'application_table_name'=>'user_assets','company_id'=>$get['company_id'],'option'=>'','notification_datetime'=>date('y-m-d h:i:s'),'user_name'=>$emp_data->display_name,'list_assets'=>implode(",",(array)$namerassets),'email_content_id'=>26);
			$this->tosendnotifiations($notiarray);
		}
		if(count((array)$rlist_assets)>0){
			$query = "SELECT id,asset_code,asset_description FROM assets WHERE id IN(".implode(",",(array)$rlist_assets).")";
			$asdata = $callConfig->getAllRows($query);
			foreach($asdata as $astdata){
				$nameassets[] = $astdata->asset_description." - ".$astdata->asset_code;
			}
			$notiarray = array('notification_message'=>'Following asset(s) retured by you'.implode(",",(array)$nameassets),'notification_users'=>array($emp_data->user_id),'id'=>$res['LASTID'],'application_table_name'=>'user_assets','company_id'=>$get['company_id'],'option'=>'','notification_datetime'=>date('y-m-d h:i'),'user_name'=>$emp_data->display_name,'list_assets'=>implode(",",(array)$nameassets),'email_content_id'=>23);
			$this->tosendnotifiations($notiarray);
			$notiarray = array('notification_message'=>'Following asset(s) retured by '.$emp_data->display_name.' - '.implode(",",(array)$nameassets),'notification_users'=>array($emp_data->user_id),'id'=>$res['LASTID'],'application_table_name'=>'user_assets','company_id'=>$get['company_id'],'option'=>'','notification_datetime'=>date('y-m-d h:i'),'user_name'=>$emp_data->display_name,'list_assets'=>implode(",",(array)$nameassets),'email_content_id'=>24);
			$this->tosendnotifiations($notiarray);
		}
		return array('post'=>$post,'query'=>$resquery);
	}
	function  vmssingleemployeedashoard($get){
		global $callConfig;
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
	function togetassetsempassetdetails($get,$post){
		global $callConfig;
			//print_r($get);
		$emp_assetssql = " SELECT emp_a.id, emp_a.start_date, emp_a.end_date, emp_a.current_status, asset.asset_code , asset.asset_description FROM `user_assets` emp_a, assets asset WHERE emp_a.company_id = '".$get['company_id']."' AND emp_a.is_active = 0 AND emp_a.asset_id = asset.id AND emp_a.user_id =  '".$get['empid']."'";
		$emp_assetres = $callConfig->getAllRows($emp_assetssql);
		return $emp_assetres;
	}
	function togetassestdashboarddetails($get,$post){
		global $callConfig;
		$query = "SELECT sum(if(current_status=31,1,0)) allocated, sum(if(current_status=32,1,0)) returned FROM `user_assets` WHERE is_active =0";
		$data1 = $callConfig->getRow($query);
		$return['top']['allocated'] = $data1->allocated;
		$return['top']['returned'] = $data1->returned;
		$query = "SELECT count(id) total FROM assets WHERE is_active =0";
		$data11 = $callConfig->getRow($query);
		$return['top']['total'] = $data11->total;
		$month_start = date('Y-m-01');
		$mont_last = date('Y-m-t');
		$last_start = date("Y-m-d", strtotime('-6 month', strtotime($month_start)));
		$query ="SELECT sum(if(current_status=31,1,0)) allocated, sum(if(current_status=32,1,0)) returned, month(`start_date`) month, year(`start_date`) evyear FROM `user_assets` WHERE `start_date` >= '2022-12-01' AND `start_date` <= '2023-05-31' AND is_active =0 GROUP by month,evyear";
		$data2 = $callConfig->getAllRows($query);
		$return['sec'] = $data2;
		$equery = $callConfig->selectQuery('asset_categories','id,category_description','','','','');
		$return['asset_categories'] = $callConfig->getAllRows($equery);
		$last_start = date("Y-m-d", strtotime('-6 month', strtotime($month_start)));
		$qurey = "SELECT COUNT(id) cc FROM assets WHERE company_id = ".$get['company_id']." AND created_date < '".$last_start."' AND is_active = 0";
		$ccmm = $callConfig->getRow($qurey);
		$count_old = $ccmm->cc;
		$qurey = "SELECT COUNT(id) cc, MONTH(created_date) month, YEAR(created_date) eyear FROM assets WHERE company_id = ".$get['company_id']." AND created_date >= '".$last_start."' AND created_date < '".$mont_last."' AND is_active = 0 GROUP BY eyear,month";
		$allassetcounts = $callConfig->getAllRows($qurey);
		foreach($allassetcounts as $allassetcount){
			$montyear = $allassetcount->eyear."-".$allassetcount->month;
			$listcount[$montyear] = $count_old+$allassetcount->cc;
			$count_old = $count_old+$allassetcount->cc;
		}
		$return['all_total'] = $listcount;
		$qurey = "SELECT emp.id emp_id, emp.emp_code, emp.display_name, ast.id asset_id, ast.category_id, ast.asset_code, ast.asset_description,eas.start_date, eas.end_date, eas.current_status  FROM user_assets eas, employees emp, assets ast WHERE eas.company_id = emp.company_id AND emp.company_id = ast.company_id AND eas.company_id=".$get['company_id']." AND eas.user_id = emp.id AND eas.asset_id = ast.id AND eas.is_active = 0";
		$allassets = $callConfig->getAllRows($qurey);
		foreach($allassets as $allasset){
			$cat[$allasset->category_id][] = $allasset;
		}
		$return['emp_asset'] = $cat;
		$query = "SELECT ast.asset_code, ast.asset_description, ast.id asset_id, ema.start_date, ema.end_date, ast.current_status, ema.user_id FROM user_assets ema, assets ast WHERE ema.company_id = ast.company_id AND ema.company_id = 1 AND ema.asset_id = ast.id AND ema.current_status = ".ST_ALLOCATE." AND ema.is_active =0 ORDER BY start_date DESC LIMIT 0,10";
		$return['allocated'] = $callConfig->getAllRows($qurey);
		$query = "SELECT ast.asset_code, ast.asset_description, ast.id asset_id, ema.start_date, ema.end_date, ast.current_status, ema.user_id FROM user_assets ema, assets ast WHERE ema.company_id = ast.company_id AND ema.company_id = 1 AND ema.asset_id = ast.id AND ema.current_status = ".ST_RETURNED." AND ema.is_active =0 ORDER BY start_date DESC LIMIT 0,10";
		$return['returned'] = $callConfig->getAllRows($qurey);
		$query = "SELECT emp.id emp_id, emp.emp_code, emp.display_name, ast.id asset_id, ast.category_id, ast.asset_code, ast.asset_description FROM user_assets eas, employees emp, assets ast WHERE eas.company_id = emp.company_id AND emp.company_id = ast.company_id AND eas.company_id=1 AND eas.user_id = emp.id AND eas.asset_id = ast.id AND emp.date_of_leaving != NULL AND emp.date_of_leaving <= now() AND eas.current_status = ".ST_ALLOCATE." AND emp.is_active = 0 AND eas.is_active =0";
		$return['pending'] = $callConfig->getAllRows($query);
		return $return;
	}

}	
?>