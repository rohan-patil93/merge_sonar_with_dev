<?php 
class reactClass extends userClass{
	function dynamicadddatareact($get, $data){
		global $callConfig;
		
		$data['company_id'] = $get['company_id'];
		$data['is_active'] = '0';
		$data['current_status'] = '0';
		$data['created_by'] = $get['user_id']; 
		$data['created_date'] = date("Y-m-d H:i:s");
    	//print_r($post); exit;
		$res=$callConfig->insertRecord($get['table'],$data);
		return $res;
	}
	function dynamicupdateatareact($get, $data){
		global $callConfig;
		//echo "___________________"; 		echo "array"; print_r($data); exit;
		$rid = $get['rid'];
		$fid = $get['fid'] !='' ? $get['fid'] : 'id';
		//echo "array".$get['table'];  print_r($get); print_r($data); echo "fid".$fid."  rid".$rid; exit;
		$res=$callConfig->updateRecord($get['table'],$data,$fid,$rid);
		return $res;
	}
	function dynamicdeletebyridreact($get){
		global $callConfig;
		//echo "___________________";echo "array"; print_r($get); exit;
		$rid = $get['rid'];
		$fid = $get['fid'] !='' ? $get['fid'] : 'id';
		$res=$callConfig->updateRecord($get['table'],array('is_active'=>NULL),$fid,$rid);
		return $res;
	}
	function dynamiclistatreact($get){
		global $callConfig;
		$whr ='is_active=0';
		$field = '*';
		if($_GET['fid'] !='' && $_GET['rid'] !=''){
			
			$rid = $_GET['rid'] !='' ? $_GET['rid'] : '';
			$fid = $_GET['fid'] !='' ? $_GET['fid'] : '';
			$whr = $fid."='".$rid."'";
		}
		if($_GET['orderby'] !=''){
			$orderby = urldecode($_GET['orderby']);
		}
		if($_GET['field'] !='' && $_GET['field'] !=''){
			
			$field = $_GET['field'] !='' ? $_GET['field'] : '*';
			
		}
		//echo "hai".$orderby; exit;
		$res = $this->getAllTablesData($_GET['table'],'*',$whr,$orderby);
		return $res;
	}
	function dyanamicautosuggictionselectdata($get){
		global $callConfig;
		$query = "SELECT *  FROM `display_attribute_types` WHERE `application_table_name` = '".$get['table']."' AND `field_name` = '".$get['view']."'";
		$dis_data = $callConfig->getRow($query);
		$selet = urldecode($dis_data->content);
		// $result = strstr($selet, 'ORDER', true);
		$query =  explode('ORDER', $selet);
		$query['0']; //exit;
		$search = " AND ".$dis_data->display_field." LIKE '%".$get['search_key']."%' ";
		$select = $query['0']." ".$search." ORDER ".$query['1'];
		$array['value_field'] = $dis_data->value_field;
		$array['display_field'] = $dis_data->display_field;
		$listdata = $callConfig->getAllRows($select); 
		$array['data'] = $listdata;
		return $array;
		//print_r($get); exit;*/

	}
	function modulelistreact($get){
		global $callConfig;
		$whr = "";
		/*if($all == 'Y'){
			$whr = "";
		}else{
			$whr = "AND module_id IN(".implode(',',$modules).")";
		}*/
		$module_query = "SELECT module_id,module_name as display_name, display_name as full_name, module_logo FROM module_setup WHERE company_id = ".$get['company_id']." AND linking =1  AND is_active =0 ".$whr;
		$module_data = $callConfig->getAllRows($module_query);
		return $module_data;
	}
	function getReactMenuList($get){
		global $callConfig;
		$return = array();
		//echo  "<pre>";
		$defaultmoduleid = $get['module_id'];
		$admin_type = $get['admin_type'];
		$get['defaultmoduleid'] = $defaultmoduleid;
		$get['working_role_short_code'] = $get['working_role_short_code'];
		$rolepermission = $this->toGetRoledata($get['user_id'],$get);
		$rolepermission['role_permission'] = $rolepermission['permission'];
		$rolepermission['role_page_permission'] = $rolepermission['page_permission'];
		$rolepermission['role_permission_pages'] = $rolepermission['permission'];

		$query = "SELECT * FROM application_menu WHERE company_id =".$get['company_id']." AND aplication_module = '".$defaultmoduleid."' AND is_active = 0 ORDER BY display_priority ASC";
		$application_menu = $callConfig->getAllRows($query); 
		$rolepermission['application_menu'] = $application_menu;

		$whrs = " company_id =".$get['company_id']." AND is_active= 0";
		$query = $callConfig->selectQuery('application_pages','*',$whrs,'','','');
		$pagesrow = $callConfig->getAllRows($query); 
		$rolepermission['application_pages'] = $pagesrow;

		$return['application_menu_LN'] = $this->toGETNavDetails('LN',$admin_type,$rolepermission);
		$return['application_menu_TN'] = $this->toGETNavDetails('TN',$admin_type,$rolepermission);
		$return['application_menu_TNL'] = $this->toGETNavDetails('TNL',$admin_type,$rolepermission);
		$return['application_menu_TNC'] = $this->toGETNavDetails('TNC',$admin_type,$rolepermission);
		$return['application_menu_TNR'] = $this->toGETNavDetails('TNR',$admin_type,$rolepermission);
        // $_SESSION['application_menu_TNC'] = resequenceMenuList($_SESSION['application_menu_TNC']);
		//echo "<pre>"; print_r($return);
		return $return;
	}
	function toGETNavDetails($type,$admin_type,$rolepermission){
		$menu  = $rolepermission['application_menu'];
	//echo $type."<pre>";print_r($menu); //exit;

		foreach ($menu as $key => $value) { 
			if($value->menu_location == $type && $value->parent_menu == 0){

				if($admin_type=='0'){
			  //echo "Ravi";
			  //echo "if the loopt".$key;	
					if(in_array($value->page_name,(array)$rolepermission['application_no_permission_pages']['option'])){
						$res = 1;
					}else{
						$res = $this->checkPremission($value->page_name,$admin_type,$rolepermission);
					}

				}else{
					$res = 1;
				}
				$child = $this->getParentidmenus($value->id,1,$admin_type,$rolepermission);
				if(count((array)$child)>0){
					$res = 1;
				}
			}
			if($value->menu_location == $type && $value->parent_menu == 0 && $res == 1){
				$array[$value->id]['id'] =  $value->id;
				$array[$value->id]['Label'] =  $value->menu_label;
				$array[$value->id]['Menu_Icon'] =  $value->menu_icon;
				$array[$value->id]['Page_Name'] =  $value->page_name;
				$array[$value->id]['dashboard'] =  $value->dashboard;
				$array[$value->id]['child'] = $child;
			} 
		}
		if($admin_type=='0'){
			$array = $this->menurecheck($array);
		// if($type=='LN' && $_SESSION['admin_type'] != 1){
		// 	$array = tocheckmenurolepermssion($array);
		// }
		}

		return $array;
	}
	function menurecheck($data){
		$array= array();
		foreach($data as $key=>$dat){
			if(count((array)$dat['child'])>0){
	   //$array[$key]=$dat;
				foreach($dat['child'] as $ddkey=>$dd){
					if(count((array)$dd['child'])>0){
						$array[$key]['id'] =  $dat['id'];
						$array[$key]['Label'] =  $dat['Label'];
						$array[$key]['Menu_Icon'] =  $dat['Menu_Icon'];
						$array[$key]['Page_Name'] =  $dat['Page_Name'];
						$array[$key]['dashboard'] = $dat['dashboard'];
						$array[$key]['child'][$ddkey]=$dd;
					}else{
						if($dd['Page_Name']!=""){
							$array[$key]['id'] =  $dat['id'];
							$array[$key]['Label'] =  $dat['Label'];
							$array[$key]['Menu_Icon'] =  $dat['Menu_Icon'];
							$array[$key]['Page_Name'] =  $dat['Page_Name'];
							$array[$key]['dashboard'] = $dat['dashboard'];
							$array[$key]['child'][$ddkey]=$dd;
						}
					}
				}

			}else{
				if($dat['Page_Name']!=""){
					$array[$key]=$dat;
				}
			}
		}
		return $array;
	}

	function checkPremission($name,$admin_type,$rolepermission){
		$return = 0;
	//echo  $name; //exit;
	//echo "<pre>"; print_r($_SESSION['role_permission']);print_r($_SESSION['role_permission']);
	//rohan added page with paramiter passe tis time not working 
		if(strpos($name,'&')){
			$explode = explode('&', $name);
			$name = $explode[0];
		}
	//print_r($value);
		//	rohan adde

		foreach ($rolepermission['application_pages'] as  $value) {
			if($value->list_page_name==$name || $value->insert_page_name==$name || $value->edit_page_name==$name || $value->view_page_name==$name){
				$table = $value->application_table_name;
				if($value->list_page_name==$name){	$type = 'permission_list';	}
				if($value->insert_page_name==$name){	$approvaltype = 'permission_approval';	}
				if($value->insert_page_name==$name){	$type = 'permission_insert'; }
				if($value->edit_page_name==$name){	$type = 'permission_update';	}
				if($value->view_page_name==$name){	$type = 'permission_list';	}
			}
		}

		if(isset($rolepermission['role_permission'][$table][$type]) && $rolepermission['role_permission'][$table][$type] == 0){
			$return =1;
		}
		if($return == 0){
			if( isset($rolepermission['role_page_permission'][$name]) && $rolepermission['role_page_permission'][$name]['permission_list'] == 0){
				$return =1;	
			}
		}

	//approval check
		if(isset($rolepermission['role_permission'][$table][$approvaltype]) && $rolepermission['role_permission'][$table][$approvaltype] == 0){
			$return =1;
		}
	//print_r($_SESSION['role_permission']);	
	//echo $table." -- ".$type." --- ".$return."</br></br>"; //exit;
	//approval check
		return $return; 
	}



	function getParentidmenus($pid,$len,$admin_type,$rolepermission){
		$menu  = $rolepermission['application_menu'];
		$array1 = array();
		foreach ($menu as $key => $value) {
		//$res = checkPremission($value['page_name']);

			$res = 1;
			if($value->parent_menu == $pid && $admin_type=='0'){
				$child = array();
				$child = $this->getParentidmenuscheck($value->id,$len+1,$admin_type,$rolepermission);
				if(count((array)$child)>0){
					$res=1;
				}else{
					if(in_array($value->page_name,(array)$rolepermission['application_no_permission_pages']['option'])){
						$res = 1;
					}else{
						$res=$this->checkPremission($value->page_name,$admin_type,$rolepermission);
					}
				}
			}
		//echo $value->page_name." --  --- ".$res."</br></br>"; //exit;
			if($value->parent_menu == $pid && $res == 1){
				$array1[$value->id]['id'] =  $value->id;
				$array1[$value->id]['Label'] =  $value->menu_label;
				$array1[$value->id]['Menu_Icon'] =  $value->menu_icon;
				$array1[$value->id]['Page_Name'] =  $value->page_name;
				$array1[$value->id]['dashboard'] =  $value->dashboard;
				$array1[$value->id]['child'] = $this->getParentidmenus($value->id,$len+1,$admin_type,$rolepermission);
			//$array1[$value->id]['child'] = $rolepermission;
			}
		}
		return $array1; 
	} 
	function getParentidmenuscheck($pid,$len,$admin_type,$rolepermission){
		$menu  = $rolepermission['application_menu'];
		foreach ($menu as $key => $value) {
			if($value->parent_menu == $pid){
				$array1[$value->id]['id'] =  $value->id;
				$array1[$value->id]['Label'] =  $value->menu_label;
				$array1[$value->id]['Menu_Icon'] =  $value->menu_icon;
				$array1[$value->id]['Page_Name'] =  $value->page_name;
		//$array1[$value[id]]['child'] = getParentidmenus($value['id'],$len+1);
				break;
			}
		}
		return $array1;
	}
	function applicationpagenamedetails($get){
		global $callConfig;
		$name = $get['page_name'];
		$query = "SELECT id, application_table_name, list_page_name, list_page_type, insert_page_name, insert_page_type, edit_page_name, edit_page_type, view_page_name, view_page_type FROM application_pages WHERE company_id =".$get['company_id']." AND  ( list_page_name = '".$get['page_name']."' OR insert_page_name = '".$get['page_name']."' OR edit_page_name = '".$get['page_name']."' OR view_page_name = '".$get['page_name']."') AND is_active =0";
			$res = $callConfig->getRow($query);
    //print_r($res);
			if($res->id !=""){
    	//echo "iiiiii";
				$value = (array)$res;	    
				if($value['list_page_name']==$name){	$type = 'list';	}
				if($value['insert_page_name']==$name){	$approvaltype = 'approval';	}
				if($value['insert_page_name']==$name){	$type = 'insert'; }
				if($value['edit_page_name']==$name){	$type = 'update';	}
				if($value['view_page_name']==$name){	$type = 'list';	}
				$return['work_page'] = $type;
				$return['application_table_name'] = $value['application_table_name'];
				$page_type = $type."_page_type";
				$return['page_type'] = $value[$page_type];
				$pp_name = strtolower(str_replace("COM","CUSTOME",$name));
				$return['page'] = $pp_name;
				if($value[$page_type]==2){
					$disptemp = "views/".$pp_name.".php";
					$return['page'] = strtolower(str_replace("COM","CUSTOME",$name)); 
					/*if (!file_exists($disptemp)){ 
						$return['page'] = '404.php'; 
					}*/
				}
				$return['other'] = $value;
			}else{
			//echo "iiiiiisss";
				$disptemp = "views/".$name.".php";
				$return['page'] = strtolower(str_replace("COM","CUSTOME",$name)); 
					/*if (!file_exists($disptemp)){ 
						$return['page'] = '404.php'; 
					}*/
				}
				return $return;
			}
			
			function insertcontractsreact($get, $post){
				global $callConfig;
				$return = [];
				$return['post'] = $post;
				$return['get'] = $get;
				$contract_creation_or_updation = "";
				$locations = '';
				if (isset($post['locations']) && is_array($post['locations'])) {
					$locations = json_encode($post['locations']);
				}
				$contract_services_id = '';
				if (isset($post['contract_services_id']) && is_array($post['contract_services_id'])) {
					$contract_services_id = json_encode($post['contract_services_id']);
				}

				$contact_personsary = array();

				if (isset($post['contact_persons']) && is_array($post['contact_persons'])) {
					$contact_personsary = array();
					foreach($post['contact_persons']['name'] as $key=>$conpers){
						if($post['contact_persons']['name'][$key] !="" AND $post['contact_persons']['number'][$key]!=""){
							$contact_personsary[] =  array('name'=>$post['contact_persons']['name'][$key],'email'=>$post['contact_persons']['email'][$key],'number'=>$post['contact_persons']['number'][$key]);
						}
					}	
				}
				$contact_persons = json_encode($contact_personsary);

				$contract_type = 'Contract';
				if (!empty($post['parent_contract_id']) && $post['parent_contract_id'] != 0) {
					$contract_type = 'SOW';
				}
				$insert_array = array(
					'contract_name' => $post['contract_name'],
					'renewal_frequency' => $post['renewal_frequency'],
					'parent_contract_id' => $post['parent_contract_id'],
					'contract_type' => $contract_type,
					'invoice_number' => $post['invoice_number'],
					'rt_number' => $post['rt_number'],
					'business_code' => $post['business_code'],
					'party_type' => $post['party_type'],
					'contract_owner_name' => $post['contract_owner_name'],
					'contract_owner_email' => $post['contract_owner_email'],
					'contract_owner_number' => $post['contract_owner_number'],
					'party_id' => $post['party_id'],
					'department_id' => $post['department_id'],
					'type_of_contract_id' => $post['type_of_contract_id'],
					'start_date' => $this->dateconvertdbformat($post['start_date']),
					'end_date' => $this->dateconvertdbformat($post['end_date']),
					'contract_amount' => $post['contract_amount'],
					'description' => $post['description'],
					'additional_notes' => $post['additional_notes'],
					'termination_condition' => $post['termination_condition'],
					'contract_status' => $post['contract_status'],
					'current_status' => 0,
					'auto_renew' => (isset($post['auto_renew']) && ($post['auto_renew'] == '1' || $post['auto_renew'] == 'on' || $post['auto_renew'] === true || $post['auto_renew'] == 'Y')) ? 'Y' : 'N',
					'renew_type' => $post['renew_type'],
					'state' => $post['state'],
					'isp' => $post['isp'],
					'paid_by' => $post['paid_by'],
					'internet_charges' => $post['internet_charges'],
					'hub_spoc_name' => $post['hub_spoc_name'],
					'hub_spoc_number' => $post['hub_spoc_number'],
					'wifi_pass' => $post['wifi_pass'],
					'wifi_provider' => $post['wifi_provider'],
					'additional_document' => $post['additional_document'],
					'contract_payment_terms' => $post['contract_payment_terms'],
					'typeofaggrements' => $post['typeofaggrements'],
					'description_id' => $post['description_id'],
					'locations' => $locations,
					'is_oem' => (isset($post['is_oem']) && ($post['is_oem'] == 'Yes' || $post['is_oem'] == 'Y')) ? 'Y' : 'N',
					'oem_registration_number' => $post['oem_registration_number'] ?? '',
					'oem_distributor_name' => $post['oem_distributor_name'] ?? '',
					'oem_product_category' => $post['oem_product_category'] ?? '',
					'cancel_status' => 'N',
					'contract_services_id' => $contract_services_id,
					'contact_persons' => $contact_persons
				);

				if (!empty($post['Rid'])) {
					$edit_option = $post['edit_option'] ?? 'SaveUpdate';
					if ($edit_option === '1')
						$edit_option = 'Amendment';
					if ($edit_option === '2')
						$edit_option = 'Addendum';

					if ($edit_option === 'Amendment' || $edit_option === 'Addendum') {  // Amendment/Addendum Contract
						$original_q = "SELECT * FROM contracts WHERE id = '" . $post['Rid'] . "'";
						$original_data = (array) $callConfig->getRow($original_q);
						$final_data = $original_data;
						foreach ($insert_array as $key => $val) {
							if ($val !== "" && $val !== null) {
								$final_data[$key] = $val;
							}
						}
						unset($final_data['id']);
						$final_data['company_id'] = $get['company_id'];
						$final_data['is_active'] = 0;
						$final_data['transaction_no'] = $this->toGetApplicationSerialNumber('contracts', $post);
						$final_data['created_by'] = $get['user_id'];
						$final_data['originator'] = $get['user_id'];
						$final_data['created_date'] = date('Y-m-d H:i:s');
						$final_data['parent_contract_id'] = $post['Rid'];
						$final_data['parent_contract'] = $post['Rid'];
						$final_data['contract_type'] = $edit_option;
						foreach ($final_data as $key => $value) {
							if ($value === "" || $value === null) {
								if (in_array($key, ['renewal_frequency', 'parent_contract_id', 'party_id', 'current_status'])) {
									$final_data[$key] = 0;
								} else if (in_array($key, ['contract_amount'])) {
									$final_data[$key] = 0.00;
								} else if (in_array($key, ['start_date', 'end_date'])) {
									$final_data[$key] = date('Y-m-d');
								} else {
									$final_data[$key] = $value ?? '';
								}
							}
						}
						$insert_data = $callConfig->insertRecord('contracts', $final_data);
						$contract_id = $insert_data['LASTID'];
						$return['contract_result'] = $insert_data;
						$return['contract_result11'] = $insert_data;
						$contract_creation_or_updation = "Created";
						if ($edit_option === 'Amendment' && ($insert_data['status'] === 'sucess' || $insert_data['status'] === 'success')) {
							$update_original = [
								'is_active' => null,
								'contract_status' => 'Expired',
								'updated_by' => $get['user_id'],
								'updated_date' => date('Y-m-d H:i:s')
							];
							$callConfig->updateRecord('contracts', $update_original, 'id', $post['Rid']);
						}
						if ($insert_data['status'] === 'sucess' || $insert_data['status'] === 'success') {
							$related_tables = [
								'contract_documents',
								'contract_permissions',
								'contract_recharge_details',
								'workflows_contract'
							];
							foreach ($related_tables as $table) {
								$q = "SELECT * FROM {$table} WHERE contract_id = '" . $post['Rid'] . "'";
								$rows = $callConfig->getAllRows($q);
								if (!empty($rows)) {
									foreach ($rows as $row) {
										$row = (array) $row;
										unset($row['id']);
										$row['contract_id'] = $contract_id;
										$row['created_date'] = date('Y-m-d H:i:s');
										$row['created_by'] = $get['user_id'];
										$callConfig->insertRecord($table, $row);
									}
								}
							}
						}
					} else {  // Update Contract
						$insert_array['updated_by'] = $get['user_id'];
						$insert_array['updated_date'] = date('Y-m-d H:i:s');

						$curr_q = "SELECT contract_type FROM contracts WHERE id = '" . $post['Rid'] . "'";
						$curr_row = $callConfig->getRow($curr_q);
						if ($curr_row) {
							$insert_array['contract_type'] = $curr_row->contract_type;
						}

						$insert_data = $callConfig->updateRecord('contracts', $insert_array, 'id', $post['Rid']);
						$contract_id = $post['Rid'];
						$return['contract_result'] = $insert_data;
						$contract_creation_or_updation = "Updated";
					}
				} else { // Insert Contract
					$insert_array['company_id'] = $get['company_id'];
					$insert_array['is_active'] = 0;
					$insert_array['transaction_no'] = $this->toGetApplicationSerialNumber('contracts', $post);
					$insert_array['created_by'] = $get['user_id'];
					$insert_array['originator'] = $get['user_id'];
					$insert_array['created_date'] = date('Y-m-d H:i:s');
					$insert_data = $callConfig->insertRecord('contracts', $insert_array);
					$contract_id = $insert_data['LASTID'];
					$return['contract_result'] = $insert_data;
					$contract_creation_or_updation = "Created";

				}

				if($insert_data['status'] === 'sucess' || $insert_data['status'] === 'success') {
					$selected_location_ids = array();
					if(isset($post['locations']) && count((array)$post['locations']) > 0){
						foreach($post['locations'] as $loc_id){
							if($loc_id != ''){
								$selected_location_ids[] = (int)$loc_id;
							}
						}
					}
					$admin_cond = "1=0";
					if(count((array)$selected_location_ids) > 0){
						$admin_cond = "(JSON_SEARCH(role_id, 'one', 'CONTRACT_ADMIN') IS NOT NULL AND location_id IN (".implode(',', $selected_location_ids)."))";
					}
					if($insert_array['originator'] !="" AND $get['user_id'] !=""){
						$conditins = " AND (".$admin_cond." OR id = '".$insert_array['originator']."' OR id = '".$get['user_id']."')";
					}elseif($insert_array['originator'] !=""){
						$conditins = " AND (".$admin_cond." OR id = '".$insert_array['originator']."')";
					}elseif($get['user_id'] !=""){
						$conditins = " AND (".$admin_cond." OR id = '".$get['user_id']."')";
					}else{
						$conditins = " AND ".$admin_cond;
					}
					$seluser = "SELECT id, company_id, user_email, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0".$conditins;
					$return['seluser'] = $seluser;
					$seluserres = $callConfig->getAllRows($seluser);
					$emailuserdetails = array();
					foreach($seluserres as $seluser){
						if($seluser->id == $insert_array['originator']){
								$originatordetailary = $seluser;
						} 
						if($seluser->id == $get['user_id']){
								$updatedby = $seluser;
						} 
						$emailuserdetails[$seluser->id] = $seluser;
					}
					foreach($emailuserdetails as $seluser){
						$notification_users = array($seluser->id);
						$emailarray  = $post;
						$emailarray['user_email_id'] = $seluser->user_email;
						$emailarray['user_display_name'] = $seluser->display_name;
						$emailarray['to_company_id'] = $seluser->company_id;
						$emailarray['session_company_id'] = $seluser->company_id;
						$emailarray['default_module_id'] = 40;
						$emailarray['created_by'] = $get['user_id'];
						$emailarray['contract_creation_or_updation'] = $contract_creation_or_updation;		
						$emailarray['contract_name'] = $insert_array['transaction_no']." - ".$insert_array['contract_name'];
						$emailarray['contract_user_name'] = $originatordetailary->display_name;;
						$emailarray['contract_update_datetime'] = date('Y-m-d H:i:s');
						$emailarray['contract_update_by'] = $updatedby->display_name;
						
						$msg = "Contract ".$insert_array['contract_name']." - ".$insert_array['transaction_no']." has been created successfully.";
						$notarry = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$contract_id,'application_table_name'=>'contracts','type'=>'INFORMATION', 'notification_users'=>$notification_users,'option'=>$option,'assigner_id'=>$get['user_id']);
						$sendnotification =$this->sendNotificationAndEmails($notarry,$emailarray,75);
					}
				}	
				$control_array = array(
					'permissions' => isset($post['permission_data']) ? json_encode($post['permission_data']) : json_encode([]),
					'workflow' => isset($post['workflow_data']) ? json_encode($post['workflow_data']) : json_encode([]),
					'documents' => isset($post['document_data']) ? json_encode($post['document_data']) : json_encode([]),
					'contract_details' => json_encode($insert_array),
					'version_id' => $post['version_id'] ?? 0,
					'company_id' => $get['company_id'] ?? 0,
					'contract_id' => $contract_id,
					'is_active' => 0,
					'created_by' => $get['user_id'] ?? 0,
					'created_date' => date('Y-m-d H:i:s')
				);
				$control_insert = $callConfig->insertRecord('contract_version', $control_array);
				$return['control_version_result'] = $control_insert;

				// ---- Contract Activity ----
				$endDate = !empty($post['end_date']) ? $post['end_date'] : 'N/A';
				if (empty($post['Rid'])) {
					$activity_text = "Contract Created (Valid until {$endDate})";
				} else {
					$edit_option = $post['edit_option'] ?? 'SaveUpdate';
					if ($edit_option === '1')
						$edit_option = 'Amendment';
					if ($edit_option === '2')
						$edit_option = 'Addendum';

					if ($edit_option === 'Amendment') {
						$activity_text = "Contract Amended (New Version Created, Valid until {$endDate})";
					} else if ($edit_option === 'Addendum') {
						$activity_text = "Contract Addendum Added (New Version Created, Valid until {$endDate})";
					} else {
						$activity_text = "Contract Updated (Standard Save, Valid until {$endDate})";
					}
				}
				$activity_array = array('company_id' => $get['company_id'] ?? 0, 'activity' => $activity_text, 'contract_id' => $contract_id, 'activity_date' => date('Y-m-d H:i:s'), 'is_active' => 0, 'created_by' => $get['user_id'] ?? 0, 'created_date' => date('Y-m-d H:i:s'));
				$activity_insert = $callConfig->insertRecord('contract_activity', $activity_array);
				$return['activity_result'] = $activity_insert;

				// Log on Parent Contract if it's an Amendment or Addendum
				if (!empty($post['Rid']) && ($edit_option === 'Amendment' || $edit_option === 'Addendum')) {
					$parent_msg = ($edit_option === 'Amendment') ? "Contract Amended (New Version Created, Valid until {$endDate})" : "Contract Addendum Added (New Version Created, Valid until {$endDate})";
					$parent_activity = array('company_id' => $get['company_id'] ?? 0, 'activity' => $parent_msg, 'contract_id' => $post['Rid'], 'activity_date' => date('Y-m-d H:i:s'), 'is_active' => 0, 'created_by' => $get['user_id'] ?? 0, 'created_date' => date('Y-m-d H:i:s'));
					$callConfig->insertRecord('contract_activity', $parent_activity);
				}

				if (isset($post['milestones']) && is_array($post['milestones']) && count($post['milestones']) > 0) {

					if (!empty($post['Rid'])) {
						$updateQuery = "UPDATE  contract_milestones SET is_active = NULL, updated_date = '" . date('Y-m-d H:i:s') . "',  updated_by = '" . addslashes($get['user_id']) . "' WHERE transaction_id = '" . addslashes($contract_id) . "'";
						$callConfig->executeQuery($updateQuery);
					}

					foreach ($post['milestones'] as $milestone) {
						if (empty(trim($milestone['milestone_name'] ?? '')))
							continue;
						$milestoneData = ['company_id' => $get['company_id'], 'transaction_id' => $contract_id, 'transaction_date' => date('Y-m-d H:i:s'), 'milestone' => trim($milestone['milestone_name']), 'milestone_date' => !empty($milestone['milestone_date']) ? $this->dateconvertdbformat($milestone['milestone_date']) : null, 'milestone_price' => !empty($milestone['milestone_price']) ? (float) $milestone['milestone_price'] : 0, 'is_active' => 0, 'current_status' => 0, 'status_date' => date('Y-m-d H:i:s'), 'created_date' => date('Y-m-d H:i:s'), 'created_by' => $get['user_id'], 'updated_by' => $get['user_id'], 'updated_date' => date('Y-m-d H:i:s'),];

						$milestoneInsert = $callConfig->insertRecord('contract_milestones', $milestoneData);
						$return['milestone_inserts'][] = $milestoneInsert;
					}

				} else {
					$return['milestone_inserts'] = 'No milestones found in POST.';
				}
				if (($insert_array['is_amc_contract'] ?? 'N') == 'N' && !empty($post['Rid'])) {
				    $amcCleanupNow = date('Y-m-d H:i:s');
				    $amcCleanupUser = addslashes($get['user_id'] ?? 0);
				    $amcCleanupContractId = addslashes($contract_id);

				    $callConfig->executeQuery("UPDATE contract_amc_locations SET is_active = NULL, updated_by = '" . $amcCleanupUser . "', updated_date = '" . $amcCleanupNow . "' WHERE contract_id = '" . $amcCleanupContractId . "' AND is_active = 0");
				    $callConfig->executeQuery("UPDATE contract_amc_assets SET is_active = NULL, updated_by = '" . $amcCleanupUser . "', updated_date = '" . $amcCleanupNow . "' WHERE contract_id = '" . $amcCleanupContractId . "' AND is_active = 0");
				    $callConfig->executeQuery("UPDATE contract_amc_service_schedules SET is_active = NULL, updated_by = '" . $amcCleanupUser . "', updated_date = '" . $amcCleanupNow . "' WHERE contract_id = '" . $amcCleanupContractId . "' AND is_active = 0");
				}
				
				$return['commercial_details_result'] = $this->saveContractCommercialDetails($contract_id, $get, $post);

				return $return;
			}
			function adminOrUserOrFM($get){
				$whr = '';
				if($get['admin_type'] != 1){
					$whr = " AND created_by='{$get['user_id']}'";
				}
				return $whr;
			}

			function getcontractslistpost($get, $post){
				return $this->getcontractslist($post);
			}
			function getcontractslist($get){
				global $callConfig;
				$return = array();
				$return['get'] = $get;
				$admin_or_user_whr = $this->adminOrUserOrFM($get); 
				if($get['api_type'] == "listpage"){

						$paymenttermssql = "SELECT `id`, `payment_term_name` FROM payment_terms WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ORDER BY payment_term_name ASC";
						$paymenttermsres = $callConfig->getAllRows($paymenttermssql);
						$payment_terms = array();
						foreach($paymenttermsres as $red){
							$payment_terms[$red->id] = $red;
						}
						$return['payment_terms'] = $payment_terms;

						$locationssql = "SELECT `id`, `location_name` FROM locations WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ORDER BY location_name ASC";
						$locationsres = $callConfig->getAllRows($locationssql);
						$locations = array();
						foreach($locationsres as $red){
							$locations[$red->id] = $red;
						}
						$return['locations'] = $locations;
						
						$gencsql = "SELECT id AS genc_detail_code, agreement_name AS genc_detail_name FROM contract_agreement_types WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ORDER BY agreement_name ASC";
						$gencres = $callConfig->getAllRows($gencsql);
						if (count((array)$gencres) == 0) {
							$gencsql = "SELECT genc_detail_code, `genc_detail_name` FROM general_code_details WHERE company_id =" . $get['company_id'] . " AND genc_code = 'AGREEMENTS_TYPE' AND is_active = 0";
							$gencres = $callConfig->getAllRows($gencsql);
						}
						$agreement_type = array();
						foreach($gencres as $red){
							$agreement_type[$red->genc_detail_code] = $red;
						}
						$return['agreement_type'] = $agreement_type;						
						$sql = "SELECT id, department_name FROM departments WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
						$res = $callConfig->getAllRows($sql);
						$departments = array();
						foreach($res as $red){
							$departments[$red->id] = $red;
						}
						$return['departments'] = $departments;

						$gencsql = "SELECT genc_detail_code, `genc_detail_name` FROM general_code_details WHERE company_id =" . $get['company_id'] . " AND genc_code = 'CONTRACT_TYPE' AND is_active = 0";
						$gencres = $callConfig->getAllRows($gencsql);
						$contract_types = array();
						foreach($gencres as $red){
							$contract_types[$red->genc_detail_code] = $red;
						}
						$return['contract_types'] = $contract_types;

						$conservsql = "SELECT id, service_name FROM contract_services WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
						$conservres = $callConfig->getAllRows($conservsql);
						$contract_services = array();
						foreach($conservres as $red){
							$contract_services[$red->id] = $red;
						}
						$return['contract_services'] = $contract_services;

						$vendorssql = "SELECT id, company FROM vendors WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
						$vendorsres = $callConfig->getAllRows($vendorssql);
						$vendors = array();
						foreach($vendorsres as $red){
							$vendors[$red->id] = $red;
						}
						$return['vendors'] = $vendors;

						
						$condition = "";
						if($get['party_id'] !=""){
							$condition .= " AND party_id = '".$get['party_id']."'";
						}
						if($get['contract_services_id'] !=""){
							$condition .= " AND JSON_SEARCH(contract_services_id, 'one', '".$get['contract_services_id']."') IS NOT NULL";
						}
						if($get['typeofaggrements'] !=""){
							$condition .= " AND typeofaggrements = '".$get['typeofaggrements']."'";
						}
						if($get['locations'] !=""){
							$condition .= " AND JSON_SEARCH(locations, 'one', '".$get['locations']."') IS NOT NULL";
						}
						if($get['department_id'] !=""){
							$condition .= " AND department_id = '".$get['department_id']."'";
						}
						if($get['type_of_contract_id'] !=""){
							$condition .= " AND type_of_contract_id = '".$get['type_of_contract_id']."'";
						}
						if($get['contract_payment_terms'] !=""){
							$condition .= " AND contract_payment_terms = '".$get['contract_payment_terms']."'";
						}
						if($get['transaction_no_and_name'] !=""){
							$condition .= " AND (transaction_no LIKE '%".$get['transaction_no_and_name']."%' OR contract_name LIKE '%".$get['transaction_no_and_name']."%') ";
						}
			 			
			 			$cntsql = "SELECT COUNT(id) as cnt FROM contracts WHERE company_id =" . $get['company_id'] . " AND is_active = 0".$condition.$admin_or_user_whr;
			     		$cntres =  $callConfig->getRow($cntsql);
			     		$return['total_record_cnt'] = $cntres;
			     		$respons_data_cnt =  ($cntres->cnt!="" AND $cntres->cnt !=0)?$cntres->cnt:0;
				     	$divide_limit = $respons_data_cnt/$get['limit_select'];
						$total_pages = ceil($respons_data_cnt / $get['limit_select']);
						$current_page = isset($get['current_page']) ? (int)$get['current_page'] : 1;
						$current_page = max(1, min($current_page, $total_pages));
						$start_from = ($current_page - 1) * $get['limit_select'];
						$return['total_pages'] = $total_pages;
							
						$contractssql = "SELECT * FROM contracts WHERE company_id =" . $get['company_id'] . " AND is_active = 0".$condition.$admin_or_user_whr." LIMIT ".$start_from.", ".$get['limit_select'];
						$contractsres = $callConfig->getAllRows($contractssql);
						$return['contracts'] = $contractsres;
				}else{
					$contractssql = "SELECT * FROM contracts WHERE company_id =" . $get['company_id'] . " AND is_active = 0".$admin_or_user_whr."  ORDER BY id ASC";
					$contractsres = $callConfig->getAllRows($contractssql);
					$return['result'] = $contractsres;
				}
				return $return;
			}
			function getEditContracts($get)
			{
				global $callConfig;
				$return = array();
				$rid = $get['rid'];
				$contractssql = "SELECT * FROM contracts WHERE company_id =" . $get['company_id'] . " AND id = '" . $rid . "' AND is_active = 0";
				$contractsres = $callConfig->getRow($contractssql);
				$return['result'] = $contractsres;
				return $return;
			}
			function getuserslistreact($get)
			{
				global $callConfig;
				$return = array();
				$userssql = "SELECT * FROM application_users WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ORDER BY id ASC";
				$useres = $callConfig->getAllRows($userssql);
				$return['result'] = $useres;
				return $return;
			}
			function getdocumentlistreact($get)
			{
				global $callConfig;
				$return = array();
				$contractssql = "SELECT * FROM document_lists WHERE company_id =" . $get['company_id'] . " AND category_id = 5 AND is_active = 0 ORDER BY id ASC";
				$contractsres = $callConfig->getAllRows($contractssql);
				$return['result'] = $contractsres;
				return $return;
			}
			function insertdocumnentreact($get, $post, $files)
			{
				global $callConfig;
				$return = [];
				$return['get'] = $get;
				$return['post'] = $post; 
				if (!empty($post['documents_meta'])) {
					$documentsMeta = json_decode($post['documents_meta'], true);
					if (json_last_error() !== JSON_ERROR_NONE) {
						$return['error'] = "Invalid JSON in documents_meta";
						return $return;
					} 
					$processedDocumentTypes = [];
					if (!empty($post['processed_document_types'])) {
						$processedDocumentTypes = json_decode($post['processed_document_types'], true) ?: [];
					} 
					foreach ($processedDocumentTypes as $documentTypeId) { 
						$existingQuery = "SELECT id, file_name FROM contract_documents WHERE contract_id = '" . addslashes($post['contract_id']) . "' AND document_list_id = '" . addslashes($documentTypeId) . "' AND is_active = 0";
						$existingDocs = $callConfig->getAllRows($existingQuery); 
						$keepDocIds = [];
						foreach ($documentsMeta as $doc) {
							if ($doc['document_list_id'] == $documentTypeId && !empty($doc['Rid'])) {
								$keepDocIds[] = $doc['Rid'];
							}
						} 
						foreach ($existingDocs as $existingDoc) {
							if (!in_array($existingDoc->id, $keepDocIds)) { 
								$deleteArray = [
									'is_active' => NULL, 'updated_by' => $post['user_id'], 'updated_date' => date('Y-m-d H:i:s')
								];
								$deleteRes = $callConfig->updateRecord('contract_documents', $deleteArray, 'id', $existingDoc->id);
								$return['deleted_results'][] = ['id' => $existingDoc->id, 'file_name' => $existingDoc->file_name, 'result' => $deleteRes];

								$filePath = "./uploads/contracts/" . $existingDoc->file_name;
								if (file_exists($filePath)) {
									unlink($filePath);
								}
							}
						}
					} 
					foreach ($documentsMeta as $index => $doc) {
						$action = $doc['action'] ?? 'new_upload'; 
						if ($action === 'keep_existing' && !empty($doc['Rid'])) { 
							$fileName = $doc['file_name'] ?? null;
							if ($fileName) {
								$update_array = ['category_id' => $post['category_id'], 'comments' => $doc['comments'], 'document_list_id' => $doc['document_list_id'], 'contract_id' => $post['contract_id'], 'file_name' => $fileName, 'updated_by' => $post['user_id'], 'updated_date' => date('Y-m-d H:i:s') ];
								$res = $callConfig->updateRecord('contract_documents', $update_array, 'id', $doc['Rid']);
								$return['update_results'][] = ['Rid' => $doc['Rid'], 'result' => $res];
							}
						} elseif ($action === 'new_upload') { 
							$fileName = $doc['file_name'] ?? null;
							if ($fileName) {
								$insert_array = ['category_id' => $post['category_id'], 'comments' => $doc['comments'], 'document_list_id' => $doc['document_list_id'], 'contract_id' => $post['contract_id'], 'file_name' => $fileName, 'company_id' => $post['company_id'], 'is_active' => 0, 'created_by' => $post['user_id'], 'created_date' => date('Y-m-d H:i:s') ];
								$insert_data = $callConfig->insertRecord('contract_documents', $insert_array);
								$return['insert_results'][] = $insert_data;

								$activity_array = ['company_id' => $get['company_id'] ?? ($post['company_id'] ?? 0), 'activity' => 'Document Upload', 'contract_id' => $post['contract_id'] ?? 0, 'activity_date' => date('Y-m-d H:i:s'), 'is_active' => 0, 'created_by' => $get['user_id'] ?? ($post['user_id'] ?? 0), 'created_date' => date('Y-m-d H:i:s') ];
								$activityRes = $callConfig->insertRecord('contract_activity', $activity_array);
								$return['activity_results'][] = $activityRes;
							}
						}
					}
				} else {
					$return['error'] = "No documents_meta found in POST data";
					return $return;
				} 
				return $return;
			}

/**
	function insertdocumnentreact($get, $post, $files)
	{
		global $callConfig;
		$return = array();

		$return['get'] = $get;
		$return['post'] = $post;
		$return['files'] = $files;

		$ridArray = [];
		$docsWithoutRid = [];
		$lastDocuments = [];

		foreach ($post['documents'] as $index => $doc) {
			$lastDocuments[$doc['document_list_id']] = [
				'index' => $index,
				'doc' => $doc
			];
		}

		foreach ($lastDocuments as $document) {
			$index = $document['index'];
			$doc = $document['doc'];

			if (isset($doc['Rid']) && $doc['Rid'] !== "") {
				$ridArray[] = ['Rid' => $doc['Rid'], 'index' => $index, 'doc' => $doc];
			} else {
				$docsWithoutRid[] = ['index' => $index, 'doc' => $doc];
			}
		}

		if (count($ridArray) > 0) {
			foreach ($ridArray as $data) {
				$index = $data['index'];
				$rid = $data['Rid'];
				$doc = $data['doc'];

				if (isset($files['documents']['name'][$index]['file_name'])) {
					$fileName = $files['documents']['name'][$index]['file_name'];
					$tmpFile = $files['documents']['tmp_name'][$index]['file_name'];

					$uploadDir = "./uploads/contracts/";
					$filePath = $uploadDir . $fileName;

					if (move_uploaded_file($tmpFile, $filePath)) {
						$update_array = array(
							'category_id' => $post['category_id'],
							'comments' => $doc['comments'],
							'document_list_id' => $doc['document_list_id'],
							'contract_id' => $post['contract_id'],
							'file_name' => $fileName,
							'updated_by' => $post['user_id'],
							'updated_date' => date('Y-m-d H:i:s')
						);

						$res = $callConfig->updateRecord('contract_documents', $update_array, 'id', $rid);
						$return['update_results'][] = array('Rid' => $rid, 'result' => $res);
					} else {
						$return['update_failed'][] = "File upload failed for Rid: $rid";
					}
				}
			}
			$return['update_status'] = "success";
		}

		if (count($docsWithoutRid) > 0) {
			foreach ($docsWithoutRid as $data) {
				$index = $data['index'];
				$doc = $data['doc'];

				if (isset($files['documents']['name'][$index]['file_name'])) {
					$fileName = $files['documents']['name'][$index]['file_name'];
					$tmpFile = $files['documents']['tmp_name'][$index]['file_name'];

					$uploadDir = "./uploads/contracts/";
					$filePath = $uploadDir . $fileName;

					if (move_uploaded_file($tmpFile, $filePath)) {
						$insert_array = array(
							'category_id' => $post['category_id'],
							'comments' => $doc['comments'],
							'document_list_id' => $doc['document_list_id'],
							'contract_id' => $post['contract_id'],
							'file_name' => $fileName,
							'company_id' => $post['company_id'],
							'is_active' => 0,
							'created_by' => $post['user_id'],
							'created_date' => date('Y-m-d H:i:s')
						);

						$insert_data = $callConfig->insertRecord('contract_documents', $insert_array);
						$return['insert_results'][] = $insert_data;
					} else {
						$return['insert_failed'][] = "File upload failed for new document.";
					}
				}
			}
			$return['insert_status'] = "success";
		}

		return $return;
	}**/
	function insertworkflowreact($get, $post)
	{
		global $callConfig;
		$return = array();
		$return['post'] = $post;
		$return['get'] = $get;

		try { 
			$Rid = $post['Rid'] ?? null;

			$stepsPermissionsValue = null;
			if (isset($post['steps_permissions'])) {
				if (is_string($post['steps_permissions'])) {
					$decoded = json_decode($post['steps_permissions'], true);
					$stepsPermissionsValue = (json_last_error() === JSON_ERROR_NONE)
					? $post['steps_permissions']
					: json_encode($post['steps_permissions']);
				} else {
					$stepsPermissionsValue = json_encode($post['steps_permissions']);
				}
			}

			$workflow_array = array( 'contract_id' => $post['contract_id'] ?? null, 'workflow_steps' => $post['workflow_steps'] ?? null, 'steps_permissions' => $stepsPermissionsValue,);

			if (!empty($Rid)) { 
				$workflow_array['updated_by'] = $get['user_id'] ?? ($post['user_id'] ?? 459);
				$workflow_array['updated_date'] = date('Y-m-d H:i:s');
				$res = $callConfig->updateRecord('workflows_contract', $workflow_array, 'id', $Rid);
				$return['workflow_result'] = $res;
			} else { 
				$workflow_array['company_id'] = $get['company_id'] ?? ($post['company_id'] ?? 1);
				$workflow_array['is_active'] = 0;
				$workflow_array['created_by'] = $get['user_id'] ?? ($post['user_id'] ?? 459);
				$workflow_array['created_date'] = date('Y-m-d H:i:s');
				$res = $callConfig->insertRecord('workflows_contract', $workflow_array);
				$return['workflow_result'] = $res;
			}

			$version_array = array( 'permissions' => isset($post['permission_data']) ? json_encode($post['permission_data']) : json_encode([]), 'workflow' => isset($post['workflow_data']) ? json_encode($post['workflow_data']) : json_encode([]), 'documents' => isset($post['document_data']) ? json_encode($post['document_data']) : json_encode([]), 'contract_details' => isset($post['contract_data']) ? json_encode($post['contract_data']) : json_encode([]), 'version_id' => $post['version_id'] ?? 0, 'company_id' => $get['company_id'] ?? ($post['company_id'] ?? 0), 'contract_id' => $post['contract_id'] ?? 0, 'is_active' => 0, 'created_by' => $get['user_id'] ?? ($post['user_id'] ?? 0), 'created_date' => date('Y-m-d H:i:s') );
			$versionRes = $callConfig->insertRecord('contract_version', $version_array);
			$return['version_result'] = $versionRes;

			$activity_array = array( 'company_id' => $get['company_id'] ?? ($post['company_id'] ?? 0), 'activity' => 'workflow', 'contract_id' => $post['contract_id'] ?? 0, 'activity_date' => date('Y-m-d H:i:s'), 'is_active' => 0, 'created_by' => $get['user_id'] ?? ($post['user_id'] ?? 0), 'created_date' => date('Y-m-d H:i:s'));
			$activityRes = $callConfig->insertRecord('contract_activity', $activity_array);
			$return['activity_result'] = $activityRes;

			$return['status'] = "success";

		} catch (Exception $e) {
			$return['status'] = "error";
			$return['error'] = $e->getMessage();
		}

		return $return;
	}
	function getEditdocumnentreact($get)
	{
		global $callConfig;
		$return = array();
		$rid = $get['rid'];
		$documnentsql = "SELECT * FROM contract_documents WHERE company_id =" . $get['company_id'] . " AND contract_id = '" . $rid . "' AND is_active = 0";
		$documnentres = $callConfig->getAllRows($documnentsql);
		$return['result'] = $documnentres;
		return $return;
	}
	function getEditworkflowreact($get){
		global $callConfig;
		$return = array();
		$rid = $get['rid'];
		$documnentsql = "SELECT * FROM workflows_contract WHERE company_id =" . $get['company_id'] . " AND contract_id = '" . $rid . "' AND is_active = 0";
		$documnentres = $callConfig->getAllRows($documnentsql);
		$return['result'] = $documnentres;
		return $return;
	}
	function insertpermissionreact($get, $post)
	{
		global $callConfig;
		$return = [];

		$basic_permissions = json_encode($post['permissions']);
		$document_permissions = json_encode($post['document_permissions']);

		$insert_array = array( 'contract_id' => $post['contract_id'], 'user' => $post['user'], 'basic_permissions' => $basic_permissions, 'document_permissions' => $document_permissions );

		if (!empty($post['Rid'])) { 
			$insert_array['updated_by'] = $post['user_id'];
			$insert_array['updated_date'] = date('Y-m-d H:i:s');
			$res = $callConfig->updateRecord('contract_permissions', $insert_array, 'id', $post['Rid']);
			$return['permission_result'] = $res;
		} else { 
			$insert_array['company_id'] = $post['company_id'];
			$insert_array['is_active'] = 0;
			$insert_array['created_by'] = $post['user_id'];
			$insert_array['created_date'] = date('Y-m-d H:i:s');
			$insert_data = $callConfig->insertRecord('contract_permissions', $insert_array);
			$return['permission_result'] = $insert_data;
		}

		$control_array = array( 'permissions' => isset($post['permission_data']) ? json_encode($post['permission_data']) : json_encode([]), 'workflow' => isset($post['workflow_data']) ? json_encode($post['workflow_data']) : json_encode([]), 'documents' => isset($post['document_data']) ? json_encode($post['document_data']) : json_encode([]), 'contract_details' => isset($post['contract_data']) ? json_encode($post['contract_data']) : json_encode([]), 'version_id' => $post['version_id'] ?? 0, 'company_id' => $post['company_id'] ?? 0, 'contract_id' => $post['contract_id'] ?? 0, 'is_active' => 0, 'created_by' => $post['user_id'] ?? 0, 'created_date' => date('Y-m-d H:i:s') );
		$control_insert = $callConfig->insertRecord('contract_version', $control_array);
		$return['control_version_result'] = $control_insert;

		$activity_array = array( 'company_id' => $post['company_id'] ?? 0, 'activity' => $post['acitivity_data'] ?? 'permission', 'contract_id' => $post['contract_id'], 'activity_date' => date('Y-m-d H:i:s'), 'is_active' => 0, 'created_by' => $post['user_id'] ?? 0, 'created_date' => date('Y-m-d H:i:s'));
		$activity_insert = $callConfig->insertRecord('contract_activity', $activity_array);
		$return['activity_result'] = $activity_insert;

		return $return;
	}


	function getEditpermissionreact($get)
	{
		global $callConfig;
		$return = array();
		$rid = $get['rid'];
		$permissionsql = "SELECT * FROM contract_permissions WHERE company_id =" . $get['company_id'] . " AND contract_id = '" . $rid . "' AND is_active = 0";
		$permissionres = $callConfig->getAllRows($permissionsql);
		$return['result'] = $permissionres;
		return $return;
	}
	function insertworkflowactivityreact($get, $post)
	{
		global $callConfig;
		$return = array();

		$inputJSON = file_get_contents('php://input');
		$post = json_decode($inputJSON, true);

		$return['post'] = $post;

		$steps_permissions = json_encode($post['steps_permissions']);

		$insert_array = array(
			'contract_id' => $post['contract_id'],
			'user_id' => $get['user_id'],
			'user' => $get['user'],
			'changed_fields' => $steps_permissions,
			'new_status' => $post['current_status'],
		);

		$insert_array['company_id'] = 1;
		$insert_array['is_active'] = 0;
		$insert_array['created_by'] = $post['user_id'];
		$insert_array['created_date'] = date('Y-m-d H:i:s');
		$insert_data = $callConfig->insertRecord('workflow_activity', $insert_array);
		$return['result'] = $insert_data;

		return $return;
	}
	function getListworkflowactivityreact($get)
	{
		global $callConfig;
		$return = array();
		$rid = $get['rid'];
		$workflowactivitysql = "SELECT * FROM workflow_activity WHERE company_id =" . $get['company_id'] . " AND workflow_id = '" . $rid . "' AND is_active = 0";
		$workflowactivityres = $callConfig->getAllRows($workflowactivitysql);
		$return['result'] = $workflowactivityres;
		return $return;
	}
	function getAssociatedcontract($get)
	{
		global $callConfig;
		$return = array();
		$rid = $get['rid'];
		$Associatedcontractsql = "SELECT * FROM contracts WHERE company_id =" . $get['company_id'] . " AND parent_contract_id = '" . $rid . "' AND is_active = 0";
		$Associatedcontractres = $callConfig->getAllRows($Associatedcontractsql);
		$return['result'] = $Associatedcontractres;

		return $return;
	}
	function insertcontrolversionreact($get, $post)
	{
		global $callConfig;
		$return = array();
		$inputJSON = file_get_contents('php://input');
		$post = json_decode($inputJSON, true);
		error_log(print_r($post, true));
		$return['post'] = $post;
		$return['get'] = $get;
		$permission = isset($post['permission_data']) ? json_encode($post['permission_data']) : json_encode([]);
		$workflow = isset($post['workflow_data']) ? json_encode($post['workflow_data']) : json_encode([]);
		$document = isset($post['document_data']) ? json_encode($post['document_data']) : json_encode([]);
		$contract = isset($post['contract_data']) ? json_encode($post['contract_data']) : json_encode([]);
		$version_id = $post['version_id'];
		$insert_array = array(
			'permissions' => $permission,
			'workflow' => $workflow,
			'documents' => $document,
			'contract_details' => $contract,
			'version_id' => $version_id,
			'company_id' => $get['company_id'] ?? 0,
			'contract_id' => $post['contract_id'] ?? 0,
			'is_active' => 0,
			'created_by' => $get['user_id'] ?? 0,
			'created_date' => date('Y-m-d H:i:s')
		);
		$insert_data = $callConfig->insertRecord('contract_version', $insert_array);
		if (!$insert_data) {
			$return['error'] = "Database insertion failed.";
		} else {
			$return['result'] = $insert_data;
		}
		return $return;
	}
	function insertactivityreact($get, $post){
		global $callConfig;
		$return = array();
		$inputJSON = file_get_contents('php://input');
		$post = json_decode($inputJSON, true);
		error_log(print_r($post, true));
		$return['post'] = $post;
		$return['get'] = $get;
		$acitivity_data = $post['acitivity_data'];
		$contract_id = $post['contract_id'];

		$insert_array = array(
			'company_id' => $get['company_id'] ?? 0,
			'activity' => $acitivity_data,
			'contract_id' => $contract_id,
			'activity_date' => date('Y-m-d H:i:s'),
			'is_active' => 0,
			'created_by' => $get['user_id'] ?? 0,
			'created_date' => date('Y-m-d H:i:s')
		);
		$insert_data = $callConfig->insertRecord('contract_activity', $insert_array);
		if (!$insert_data) {
			$return['error'] = "Database insertion failed.";
		} else {
			$return['result'] = $insert_data;
		}
		return $return;
	}
	function getactivityreact($get)
	{
		global $callConfig;
		$return = array();
		$rid = $get['rid'];
		$activitysql = "SELECT * FROM contract_activity WHERE company_id =" . $get['company_id'] . " AND contract_id = '" . $rid . "' AND is_active = 0";
		$activityres = $callConfig->getAllRows($activitysql);
		$return['result'] = $activityres;
		return $return;
	}
	
	function insertCustomersreact($get, $post)
	{
		global $callConfig;
		$return = array();
		if (empty($post)) {
			$inputJSON = file_get_contents('php://input');
			$decoded = json_decode($inputJSON, true);
			if (json_last_error() === JSON_ERROR_NONE) {
				$post = $decoded;
			}
		}
		$return['post'] = $post;
		$return['get'] = $get;
		$firstName = $post['first_name'] ?? '';
		$middleName = $post['middle_name'] ?? '';
		$lastName = $post['last_name'] ?? '';

		if (empty($firstName) && !empty($post['party_name'])) {
			$nameParts = explode(' ', trim($post['party_name']));
			$firstName = $nameParts[0];
			if (count($nameParts) > 2) {
				$middleName = $nameParts[1];
				$lastName = $nameParts[count($nameParts) - 1];
			} else if (count($nameParts) == 2) {
				$lastName = $nameParts[1];
			}
		}

		$insertary = array(
			'company' => $post['company'] ?? '',
			'first_name' => $firstName,
			'middle_name' => $middleName,
			'last_name' => $lastName,
			'display_name' => $firstName . ' '. $lastName,
			'email' => $post['email'] ?? '',
			'phone' => $post['phone'] ?? '',
			'billing_address' => $post['address'] ?? '',
			'shipping_address' => $post['address'] ?? '',
			'created_date' => date("Y-m-d H:i:s"),
			'created_by' => $get['user_id'] ?? 0,
			'company_id' => $get['company_id'] ?? 1,
		);

		if (!empty($post['rid'])) {
			$insertary['updated_date'] = date('Y-m-d H:i:s');
			$insertary['updated_by'] = $get['user_id'] ?? 0;
			$res = $callConfig->updateRecord('customers', $insertary, 'id', $post['rid']);
			$return['res'] = $res;
			$return['message'] = "Customer updated successfully";
		} else {
			$insertary['customer_since'] = date('Y-m-d');
			$insertary['is_active'] = 0;
			$insertary['current_status'] = 0;
			$res = $callConfig->insertRecord('customers', $insertary);
			$return['res'] = $res;
			$return['message'] = "Customer created successfully";
		}
		return $return;
	}

	function insertVendorsreact($get, $post)
	{
		global $callConfig;
		$return = array();
		if (empty($post)) {
			$inputJSON = file_get_contents('php://input');
			$decoded = json_decode($inputJSON, true);
			if (json_last_error() === JSON_ERROR_NONE) {
				$post = $decoded;
			}
		}
		$return['post'] = $post;
		$return['get'] = $get;
		$firstName = $post['first_name'] ?? '';
		$middleName = $post['middle_name'] ?? '';
		$lastName = $post['last_name'] ?? '';

		if (empty($firstName) && !empty($post['party_name'])) {
			$nameParts = explode(' ', trim($post['party_name']));
			$firstName = $nameParts[0];
			if (count($nameParts) > 2) {
				$middleName = $nameParts[1];
				$lastName = $nameParts[count($nameParts) - 1];
			} else if (count($nameParts) == 2) {
				$lastName = $nameParts[1];
			}
		}

		$insertary = array(
			'company' => $post['company'] ?? '',
			'first_name' => $firstName,
			'middle_name' => $middleName,
			'last_name' => $lastName,
			'display_name' => $firstName . ' '. $lastName,
			'email' => $post['email'] ?? '',
			'phone' => $post['phone'] ?? '',
			'billing_address' => $post['address'] ?? '',
			'shipping_address' => $post['address'] ?? '',
			'created_date' => date("Y-m-d H:i:s"),
			'created_by' => $get['user_id'] ?? 0,
			'company_id' => $get['company_id'] ?? 1,
			'bp_code' => $post['bp_code'] ?? '',
		);

		if (!empty($post['rid'])) {
			$insertary['updated_date'] = date('Y-m-d H:i:s');
			$insertary['updated_by'] = $get['user_id'] ?? 0;
			$res = $callConfig->updateRecord('vendors', $insertary, 'id', $post['rid']);
			$return['res'] = $res;
			$return['message'] = "Vendor updated successfully";
		} else {
			$insertary['vendor_since'] = date('Y-m-d');
			$insertary['user_id'] = $get['user_id'] ?? 0;
			$insertary['is_active'] = 0;
			$insertary['current_status'] = 0;
			$res = $callConfig->insertRecord('vendors', $insertary);
			$return['res'] = $res;
			$return['message'] = "Vendor created successfully";
		}
		return $return;
	}

	function getcustomerlist($get){
		global $callConfig;
		$return = array();
		$activitysql = "SELECT * FROM customers WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
		$activityres = $callConfig->getAllRows($activitysql);
		$return['result'] = $activityres;
		return $return;
	}
	function getvendorslist($get){
		global $callConfig;
		$return = array();
		$activitysql = "SELECT * FROM vendors WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
		$activityres = $callConfig->getAllRows($activitysql);
		$return['result'] = $activityres;

		return $return;
	}
	function getcustomerdetailsreact($get){
		global $callConfig;
		$return = array();
		$rid = $get['rid'];
		$customersql = "SELECT * FROM customers WHERE company_id =" . $get['company_id'] . " AND id = '" . $rid . "' AND is_active = 0";
		$customerres = $callConfig->getAllRows($customersql);
		$return['result'] = $customerres;

		return $return;
	}
	function getpermissiondetailsreact($get){
		global $callConfig;
		$return = array();
		$user_id = $get['user_id'];
		$rid = $get['rid'];
		$customersql = "SELECT * FROM contract_permissions WHERE company_id =" . $get['company_id'] . " AND user = '" . $user_id . "' AND contract_id = '" . $rid . "' AND is_active = 0";
		$customerres = $callConfig->getAllRows($customersql);
		$return['result'] = $customerres;

		return $return;
	}
	function getfullpermissiondetailsreact($get){
		global $callConfig;
		$return = array();
		$user_id = $get['user_id'];
		$customersql = "SELECT * FROM contract_permissions WHERE company_id =" . $get['company_id'] . " AND user = '" . $user_id . "' AND is_active = 0";
		$customerres = $callConfig->getAllRows($customersql);
		$return['result'] = $customerres;
		return $return;
	}
	function getdeleterowreact($get){
		global $callConfig;
		$return = array();
		$rid = $get['rid'];
		$insertary = array(
			'is_active' => 1,
		);
		$res = $callConfig->updateRecord('contracts', $insertary, 'id', $rid);
		$return['res'] = $res;
		return $return;
	}
	function getdashboardreact($get)
	{
		global $callConfig;
		$return = array();
		$hwr ="";
		if($get['admin_type']!=1){
			$hwr = " AND originator = ".$get['user_id'];
		}
		if($get['LOCID']!=''){
			$hwr = " AND JSON_SEARCH(locations, 'one', '".$get['LOCID']."') IS NOT NULL";
		}


		$allcontractssql = "SELECT id, `originator`, `contract_name`, `transaction_no`, `party_type`, `party_id`, `parent_contract_id`, `start_date`, `end_date`, `description`, `additional_notes`, `contract_payment_terms`, `termination_condition`, `contract_status`, `auto_renew`, `renew_type`, `owner_user_id`, `contract_owner_name`, `contract_owner_email`, `contract_owner_number`, `rt_number`, `current_status`,  `created_date`, locations, contract_type, updated_date,is_amc_contract  FROM contracts WHERE company_id =" . $get['company_id'] . " ".$hwr." AND (cancel_status != 'Y' OR cancel_status IS NULL) AND is_active = 0";
		$allcontractsres = $callConfig->getAllRows($allcontractssql);
		$return['allcontracts'] = $allcontractsres;

		$allcustomerssql = "SELECT id, company, display_name, created_date FROM customers WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
		$allcustomersres = $callConfig->getAllRows($allcustomerssql);
		$return['allcustomers'] = $allcustomersres;

		$allvendorssql = "SELECT id, company, display_name, created_date FROM vendors WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
		$allvendorsres = $callConfig->getAllRows($allvendorssql);
		$return['allvendors'] = $allvendorsres;

		$allstatussql = "SELECT id, name FROM status WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
		$allstatusres = $callConfig->getAllRows($allstatussql);
		$return['allstatus'] = $allstatusres;

		$allpermissionssql = "SELECT * FROM contract_permissions WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
		$allpermissionsres = $callConfig->getAllRows($allpermissionssql);
		$return['allpermissions'] = $allpermissionsres;

		$alldocumentssql = "SELECT * FROM contract_documents WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
		$alldocumentsres = $callConfig->getAllRows($alldocumentssql);
		$return['alldocuments'] = $alldocumentsres;

		$permissionssql = "SELECT * FROM contract_permissions WHERE company_id =" . $get['company_id'] . " AND user = '" . $get['user_id'] . "' AND is_active = 0";
		$permissionsres = $callConfig->getAllRows($permissionssql);
		$return['permissions'] = $permissionsres;

		$locationssql = "SELECT id, location_name FROM locations WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
		$locationsres = $callConfig->getAllRows($locationssql);
		$locations = array();
		foreach($locationsres as $loc){
			$locations[$loc->id] = $loc;
		}
		$return['locations'] = $locations;

        // $contractssql = "SELECT * FROM contracts WHERE company_id =" . $get['company_id'] . " AND originator = '" . $get['user_id'] . "' AND is_active = 0";
        // $contractsres = $callConfig->getAllRows($contractssql);
        // $return['contracts'] = $contractsres;

		return $return;
	}
	function getDocumentDetailsReact($get){
		global $callConfig;
		$return = array();
		$contract_id = $get['contract_id'];
		$customersql = "SELECT * FROM contract_documents WHERE company_id ='".$get['company_id']."' AND contract_id = '".$contract_id."' AND is_active = 0";
		$customerres = $callConfig->getAllRows($customersql);
		$return['result'] = $customerres;
		return $return;
	}
	function insertContractRechargeDetails($get,$post){
		global $callConfig;
		$return =array();
		$return['post'] = $post;
		$insert_array = array('contract_id'=>$post['contract_id'],'transaction_date'=>date('Y-m-d H:i:s'),'activation_date'=>$this->dateconvertdbformat($post['activation_date']),'recharge_date'=>$this->dateconvertdbformat($post['recharge_date']),'expiry_date'=>$this->dateconvertdbformat($post['expiry_date']),'plan_duration'=>$this->dateconvertdbformat($post['plan_duration']));
		if($post['Rid']!=""){ 
			$insert_array['updated_by']=$get['user_id'];
			$insert_array['updated_date']=date('Y-m-d H:i:s');
			$res=$callConfig->updateRecord('contract_recharge_details',$insert_array,'id',$post['Rid']);
			$return['result'] = $res;
		}else{
			$insert_array['company_id']=$get['company_id'];
			$insert_array['is_active']=0;
			$insert_array['created_by']=$get['user_id'];
			$insert_array['created_date']=date('Y-m-d H:i:s');
			$insert_data=$callConfig->insertRecord('contract_recharge_details',$insert_array);
			$return['result'] = $insert_data;
		}
		return $return;
	}

	function getcontractdetailsreactamc($get){
		global $callConfig;
		$return = array();
		$rid = $get['rid'] ?? 0;


		$displayattributessql = "SELECT `id`, `list_attributes`, `insert_attributes`, `additional_attributes`, `file_upload`, `help_text` FROM display_attributes WHERE company_id =" . $get['company_id'] . " AND application_table_name = 'contracts' AND is_active = 0 ORDER BY id ASC";
		$displayattributesres = $callConfig->getRow($displayattributessql);
		$return['displayattributesres'] = $displayattributesres;

		$admin_or_user_whr = $this->adminOrUserOrFM($get); 
		
		$contractssql = "SELECT * FROM contracts WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ".$admin_or_user_whr." ORDER BY id ASC";
		$contractsres = $callConfig->getAllRows($contractssql);
		$return['contractsres'] = $contractsres;

		$paymenttermssql = "SELECT `id`, `payment_term_name` FROM payment_terms WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ORDER BY id ASC";
		$paymenttermsres = $callConfig->getAllRows($paymenttermssql);
		$return['paymenttermsres'] = $paymenttermsres;

		$locationssql = "SELECT `id`, `location_name` FROM locations WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ORDER BY id ASC";
		$locationsres = $callConfig->getAllRows($locationssql);
		$return['locationsres'] = $locationsres;

		$userssql = "SELECT `id`, `company_id`, `user_name`, `first_name`, `middle_name`, `last_name`, `display_name`, `user_email`, `user_mobile`, `user_password`, `role_id`, `role_group_id`, `cost_center`, `location_id`, `default_warehouse_id`, `work_station_id`, `remember_token`, `last_logged_in_at`, `user_photo`, `pref_language`, `confirmed_basic_ino`, `user_activated`, `country_id`, `default_state_id`, `timezone`, `default_currency_id`, `hr_connect`, `finance_connect`, `admin_connect`, `project_connect`, `location_connect`, `esop_connect`, `legal_connect`, `reports_to`, `approval_manager`, `user_field_work_type`, `live_lcation`, `user_type`, `mapping_code`, `is_active` FROM application_users WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ORDER BY id ASC";
		$useres = $callConfig->getAllRows($userssql);
		$return['useres'] = $useres;

		$documentsql = "SELECT `id`, `company_id`, `category_id`, `parent_document_id`, `document_name`, `document_list_types`, `submit_to_user_id`, `mandatory`, `display_position`, `page`, `is_active` FROM document_lists WHERE company_id =" . $get['company_id'] . " AND category_id = 5 AND is_active = 0 ORDER BY id ASC";
		$documentres = $callConfig->getAllRows($documentsql);
		$return['documentres'] = $documentres;

		$Associatedcontractsql = "SELECT `id`, `company_id`, `originator`, `contract_name`, `transaction_no`, `contract_owner_name`, `contract_owner_email`, `contract_owner_number`, `invoice_number`, `rt_number`, `party_type`, `party_id`, `parent_contract_id`, `start_date`, `end_date`, `description`, `additional_notes`, `payment_term_id`, `termination_condition`, `contract_status`, `auto_renew`, `renew_type`, `contract_payment_terms`, `is_active` FROM contracts WHERE company_id =" . $get['company_id'] . " AND parent_contract_id = '" . $rid . "' AND is_active = 0";
		$Associatedcontractres = $callConfig->getAllRows($Associatedcontractsql);
		$return['associatedcontractres'] = $Associatedcontractres;

		$editcontractssql = "SELECT * FROM contracts WHERE company_id =" . $get['company_id'] . " AND id = '" . $rid . "' AND is_active = 0";
		$editcontractsres = $callConfig->getRow($editcontractssql);
		$return['editcontractsres'] = $editcontractsres;

		if (!empty($rid) && !empty($editcontractsres)) {
			$return['amccontractres'] = $this->getAmcContractEditData($get['company_id'], $rid);
		}

		$editdocumnentsql = "SELECT `id`, `company_id`, `category_id`, `document_list_id`, `contract_id`, `file_name`, `comments`, is_active FROM contract_documents WHERE company_id =" . $get['company_id'] . " AND contract_id = '" . $rid . "' AND is_active = 0";
		$editdocumnentres = $callConfig->getAllRows($editdocumnentsql);
		$return['editdocumnentres'] = $editdocumnentres;

		$editpermissionsql = "SELECT `id`, `company_id`, `contract_id`, `user`, `basic_permissions`, `document_permissions`, is_active FROM contract_permissions WHERE company_id =" . $get['company_id'] . " AND contract_id = '" . $rid . "' AND is_active = 0";
		$editpermissionres = $callConfig->getAllRows($editpermissionsql);
		$return['editpermissionres'] = $editpermissionres;

		$editworkflowsql = "SELECT `id`, `company_id`, `contract_id`, `workflow_steps`, `steps_permissions`, `is_active` FROM workflows_contract WHERE company_id =" . $get['company_id'] . " AND contract_id = '" . $rid . "' AND is_active = 0";
		$editworkflowres = $callConfig->getAllRows($editworkflowsql);
		$return['editworkflowres'] = $editworkflowres;

		$activitysql = "SELECT `id`, `company_id`, `contract_id`, `activity`, `activity_date`, `is_active` FROM contract_activity WHERE company_id =" . $get['company_id'] . " AND contract_id = '" . $rid . "' AND is_active = 0 order BY id DESC";
		$activityres = $callConfig->getAllRows($activitysql);
		$return['activityres'] = $activityres;

		$milestonesql = "SELECT `id`, `milestone`, `milestone_date`, `milestone_price` FROM contract_milestones WHERE company_id =" . $get['company_id'] . " AND transaction_id = '" . $rid . "' AND is_active = 0";
		$milestoneres = $callConfig->getAllRows($milestonesql);
		$return['milestoneres'] = $milestoneres;

		$gencsql = "SELECT id AS genc_detail_code, agreement_name AS genc_detail_name FROM contract_agreement_types WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ORDER BY agreement_name ASC";
		$gencres = $callConfig->getAllRows($gencsql);
		if (count((array)$gencres) == 0) {
			$gencsql = "SELECT genc_detail_code, `genc_detail_name` FROM general_code_details WHERE company_id =" . $get['company_id'] . " AND genc_code = 'AGREEMENTS_TYPE' AND is_active = 0";
			$gencres = $callConfig->getAllRows($gencsql);
		}
		$return['gencres'] = $gencres;

		$dessql = "SELECT `id`, `description`, `description_type` FROM description_contracts WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
		$desres = $callConfig->getAllRows($dessql);
		$return['desres'] = $desres;

		$today = date('Y-m-d'); 
		$contractsres['renewal_frequency'] = "quaterly";
		switch (strtolower($contractsres['renewal_frequency'])) 
		{ 
			case 'onday': 
			 	$reminderDays = 1; 
				break; 
			case 'weekly': 
				$reminderDays = 7; 
				break; 
			case 'monthly': 
				$reminderDays = 30; 
				break; 
			case 'quarterly': 
				$reminderDays = 90; 
				break; 
			default: 
				$reminderDays = 90;
		}

		$expirySql = "SELECT id, company_id, contract_name, end_date, originator FROM contracts WHERE company_id = " . $get['company_id'] . " AND is_active = 0 AND end_date IS NOT NULL AND DATEDIFF(end_date, '$today') BETWEEN 1 AND $reminderDays ";
		$expiringContracts = $callConfig->getAllRows($expirySql);
		$return['expiringContracts'] = $expirySql;

		if (!empty($expiringContracts)) { 
			// $contractNames = implode(', ', array_column($expiringContracts, 'contract_name'));
			// $msglist = "The following contracts are expiring soon: $contractNames.";
			// $useridd[$user_id] = $get['user_id']; 
			// $notarry = array('company_id' => $get['company_id'], 'notification_message' => $msglist, 'id' => '', 'application_table_name' => 'contracts', 'type' => 'INFORMATION', 'notification_users' => $useridd, 'option' => '', 'assigner_id' => $get['user_id'] ); 
			// $return['notarry'] = $notarry;
			// $this->sendNotificationAndEmails($notarry, '', '');
		}
 	
 		if($rid != ""){
    		$historySql = "SELECT contract_name,transaction_no, start_date, end_date, parent_contract ,  contract_amount FROM contracts WHERE company_id = " . $get['company_id'] . " AND parent_contract = $rid AND is_active = 0";
            $historyContracts = $callConfig->getAllRows($historySql);
            $return['historySql'] = $historySql;
            $return['historycontractsres'] = $historyContracts;
       
            $childrenSql = "SELECT parent_contract FROM contracts WHERE company_id = " . $get['company_id'] . " AND id = " . $rid . " AND is_active = 0";
            $childrenData = $callConfig->getRow($childrenSql);
            $return['childrenData'] = $childrenData;
            $parentId = $childrenData->parent_contract;
            $return['parentId'] = $parentId;
       
            if (!empty($parentId) && intval($parentId) > 0) {
                $parentSql = "SELECT * FROM contracts WHERE company_id = " . intval($get['company_id']) . " AND id = " . intval($parentId) . " AND is_active = 0";
                $parentContracts = $callConfig->getAllRows($parentSql);
                $return['parentSql'] = $parentSql;
                $return['parentcontractsres'] = $parentContracts;
           
            } else {
                $return['parentcontractsres'] = [];
            }
		}

		$sql = "SELECT id, department_name FROM departments WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
    $res = $callConfig->getAllRows($sql);
    $return['departmentsres'] = $res;


    $gencsql = "SELECT genc_detail_code, `genc_detail_name` FROM general_code_details WHERE company_id =" . $get['company_id'] . " AND genc_code = 'CONTRACT_TYPE' AND is_active = 0";
		$gencres = $callConfig->getAllRows($gencsql);
		$return['contracttypesres'] = $gencres;

		$conservsql = "SELECT id, service_name FROM contract_services WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
		$conservres = $callConfig->getAllRows($conservsql);
		$return['contract_services'] = $conservres;
		
		return $return;
	}
	/**/
		function getAmcContractEditData($company_id, $contract_id)
	{
		global $callConfig;
		$return = array('details' => array(), 'locations' => array());
		if (empty($company_id) || empty($contract_id)) {
			return $return;
		}

		$detailsSql = "SELECT * FROM contract_amc_details WHERE company_id = " . intval($company_id) . " AND contract_id = '" . addslashes($contract_id) . "' AND is_active = 0 ORDER BY id DESC LIMIT 1";
		$detailsRow = $callConfig->getRow($detailsSql);
		if ($detailsRow) {
			$details = (array) $detailsRow;
			$details['reminder_days'] = !empty($details['reminder_days']) ? (json_decode($details['reminder_days'], true) ?: array()) : array();
			$details['completion_rules'] = !empty($details['completion_rules']) ? (json_decode($details['completion_rules'], true) ?: array()) : array();
			$details['escalation_config'] = !empty($details['escalation_config']) ? (json_decode($details['escalation_config'], true) ?: array()) : array();
			$return['details'] = $details;
		}

		$locationsSql = "SELECT * FROM contract_amc_locations WHERE company_id = " . intval($company_id) . " AND contract_id = '" . addslashes($contract_id) . "' AND is_active = 0 ORDER BY id ASC";
		$locationRows = $callConfig->getAllRows($locationsSql);

		$assetsSql = "SELECT * FROM contract_amc_assets WHERE company_id = " . intval($company_id) . " AND contract_id = '" . addslashes($contract_id) . "' AND is_active = 0 ORDER BY id ASC";
		$assetRows = $callConfig->getAllRows($assetsSql);
		$assetsByLocation = array();
		if (!empty($assetRows)) {
			foreach ($assetRows as $assetRow) {
				$asset = is_array($assetRow) ? $assetRow : (array) $assetRow;
				$locKey = $asset['contract_amc_location_id'] ?? 0;
				if (!isset($assetsByLocation[$locKey])) {
					$assetsByLocation[$locKey] = array();
				}
				$assetsByLocation[$locKey][] = array(
					'id' => $asset['id'] ?? '',
					'asset_id' => $asset['asset_id'] ?? '',
					'asset_name' => $asset['asset_name'] ?? '',
					'service_frequency' => $asset['service_frequency'] ?? '',
					'visits_per_interval' => $asset['visits_per_interval'] ?? 1,
					'service_start_date' => $asset['service_start_date'] ?? '',
					'service_end_date' => $asset['service_end_date'] ?? '',
					'service_slots' => !empty($asset['service_slots']) ? (json_decode($asset['service_slots'], true) ?: array()) : array(),
				);
			}
		}

		if (!empty($locationRows)) {
			foreach ($locationRows as $locationRow) {
				$loc = is_array($locationRow) ? $locationRow : (array) $locationRow;
				$amcLocId = $loc['id'] ?? 0;
				$return['locations'][] = array(
					'location_id' => $loc['location_id'] ?? '',
					'service_start_date' => $loc['service_start_date'] ?? '',
					'service_end_date' => $loc['service_end_date'] ?? '',
					'assets' => $assetsByLocation[$amcLocId] ?? array(),
				);
			}
		}

		return $return;
	}
/***/


	function getcontractdetailsreact($get){
		global $callConfig;
		$return = array();
		$rid = $get['rid'] ?? 0;

		$displayattributessql = "SELECT `id`, `list_attributes`, `insert_attributes`, `additional_attributes`, `file_upload`, `help_text` FROM display_attributes WHERE company_id =" . $get['company_id'] . " AND application_table_name = 'contracts' AND is_active = 0 ORDER BY id ASC";
		$displayattributesres = $callConfig->getRow($displayattributessql);
		$return['displayattributesres'] = $displayattributesres;

		$contractssql = "SELECT * FROM contracts WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ORDER BY id ASC";
		$contractsres = $callConfig->getAllRows($contractssql);
		$return['contractsres'] = $contractsres;

		$paymenttermssql = "SELECT `id`, `payment_term_name` FROM payment_terms WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ORDER BY id ASC";
		$paymenttermsres = $callConfig->getAllRows($paymenttermssql);
		$return['paymenttermsres'] = $paymenttermsres;

		$locationssql = "SELECT `id`, `location_name` FROM locations WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ORDER BY id ASC";
		$locationsres = $callConfig->getAllRows($locationssql);
		$return['locationsres'] = $locationsres;

		$userssql = "SELECT `id`, `company_id`, `user_name`, `first_name`, `middle_name`, `last_name`, `display_name`, `user_email`, `user_mobile`, `user_password`, `role_id`, `role_group_id`, `cost_center`, `location_id`, `default_warehouse_id`, `work_station_id`, `remember_token`, `last_logged_in_at`, `user_photo`, `pref_language`, `confirmed_basic_ino`, `user_activated`, `country_id`, `default_state_id`, `timezone`, `default_currency_id`, `hr_connect`, `finance_connect`, `admin_connect`, `project_connect`, `location_connect`, `esop_connect`, `legal_connect`, `reports_to`, `approval_manager`, `user_field_work_type`, `live_lcation`, `user_type`, `mapping_code`, `is_active` FROM application_users WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ORDER BY id ASC";
		$useres = $callConfig->getAllRows($userssql);
		$return['useres'] = $useres;

		$documentsql = "SELECT `id`, `company_id`, `category_id`, `parent_document_id`, `document_name`, `document_list_types`, `submit_to_user_id`, `mandatory`, `display_position`, `page`, `is_active` FROM document_lists WHERE company_id =" . $get['company_id'] . " AND category_id = 5 AND is_active = 0 ORDER BY id ASC";
		$documentres = $callConfig->getAllRows($documentsql);
		$return['documentres'] = $documentres;

		$Associatedcontractsql = "SELECT `id`, `company_id`, `originator`, `contract_name`, `transaction_no`, `contract_owner_name`, `contract_owner_email`, `contract_owner_number`, `invoice_number`, `rt_number`, `party_type`, `party_id`, `parent_contract_id`, `start_date`, `end_date`, `description`, `additional_notes`, `contract_payment_terms`, `termination_condition`, `contract_status`, `auto_renew`, `renew_type`, `contract_payment_terms`, `is_active` FROM contracts WHERE company_id =" . $get['company_id'] . " AND parent_contract_id = '" . $rid . "' AND is_active = 0";
		$Associatedcontractres = $callConfig->getAllRows($Associatedcontractsql);
		$return['associatedcontractres'] = $Associatedcontractres;

		$editcontractssql = "SELECT * FROM contracts WHERE company_id =" . $get['company_id'] . " AND id = '" . $rid . "' AND is_active = 0";
		$editcontractsres = $callConfig->getRow($editcontractssql);
		$return['editcontractsres'] = $editcontractsres;

		$editdocumnentsql = "SELECT `id`, `company_id`, `category_id`, `document_list_id`, `contract_id`, `file_name`, `comments`, is_active FROM contract_documents WHERE company_id =" . $get['company_id'] . " AND contract_id = '" . $rid . "' AND is_active = 0";
		$editdocumnentres = $callConfig->getAllRows($editdocumnentsql);
		$return['editdocumnentres'] = $editdocumnentres;

		$editpermissionsql = "SELECT `id`, `company_id`, `contract_id`, `user`, `basic_permissions`, `document_permissions`, is_active FROM contract_permissions WHERE company_id =" . $get['company_id'] . " AND contract_id = '" . $rid . "' AND is_active = 0";
		$editpermissionres = $callConfig->getAllRows($editpermissionsql);
		$return['editpermissionres'] = $editpermissionres;

		$editworkflowsql = "SELECT `id`, `company_id`, `contract_id`, `workflow_steps`, `steps_permissions`, `is_active` FROM workflows_contract WHERE company_id =" . $get['company_id'] . " AND contract_id = '" . $rid . "' AND is_active = 0";
		$editworkflowres = $callConfig->getAllRows($editworkflowsql);
		$return['editworkflowres'] = $editworkflowres;

		$activitysql = "SELECT `id`, `company_id`, `contract_id`, `activity`, `activity_date`, `is_active` FROM contract_activity WHERE company_id =" . $get['company_id'] . " AND contract_id = '" . $rid . "' AND is_active = 0 order BY id DESC";
		$activityres = $callConfig->getAllRows($activitysql);
		$return['activityres'] = $activityres;

		$milestonesql = "SELECT `id`, `milestone`, `milestone_date`, `milestone_price` FROM contract_milestones WHERE company_id =" . $get['company_id'] . " AND transaction_id = '" . $rid . "' AND is_active = 0";
		$milestoneres = $callConfig->getAllRows($milestonesql);
		$return['milestoneres'] = $milestoneres;

		$gencsql = "SELECT genc_detail_code, `genc_detail_name` FROM general_code_details WHERE company_id =" . $get['company_id'] . " AND genc_code = 'AGREEMENTS_TYPE' AND is_active = 0";
		$gencres = $callConfig->getAllRows($gencsql);
		$return['gencres'] = $gencres;

		$dessql = "SELECT `id`, `description`, `description_type` FROM description_contracts WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
		$desres = $callConfig->getAllRows($dessql);
		$return['desres'] = $desres;

		$today = date('Y-m-d'); 
		$contractsres['renewal_frequency'] = "quaterly";
		switch (strtolower($contractsres['renewal_frequency'])) 
		{ 
			case 'onday': 
			$reminderDays = 1; 
			break; 
			case 'weekly': 
			$reminderDays = 7; 
			break; 
			case 'monthly': 
			$reminderDays = 30; 
			break; 
			case 'quarterly': 
			$reminderDays = 90; 
			break; 
			default: 
			$reminderDays = 90;
		}

		$expirySql = "SELECT id, company_id, contract_name, end_date, originator FROM contracts WHERE company_id = " . $get['company_id'] . " AND is_active = 0 AND end_date IS NOT NULL AND DATEDIFF(end_date, '$today') BETWEEN 1 AND $reminderDays ";
		$expiringContracts = $callConfig->getAllRows($expirySql);
		$return['expiringContracts'] = $expirySql;

		if (!empty($expiringContracts)) { 
			// $contractNames = implode(', ', array_column($expiringContracts, 'contract_name'));
			// $msglist = "The following contracts are expiring soon: $contractNames.";
			// $useridd[$user_id] = $get['user_id']; 
			// $notarry = array('company_id' => $get['company_id'], 'notification_message' => $msglist, 'id' => '', 'application_table_name' => 'contracts', 'type' => 'INFORMATION', 'notification_users' => $useridd, 'option' => '', 'assigner_id' => $get['user_id'] ); 
			// $return['notarry'] = $notarry;
			// $this->sendNotificationAndEmails($notarry, '', '');
		}

		if($rid != ""){
			$historySql = "SELECT contract_name,transaction_no, start_date, end_date, parent_contract ,  contract_amount FROM contracts WHERE company_id = " . $get['company_id'] . " AND parent_contract = $rid AND is_active = 0";
			$historyContracts = $callConfig->getAllRows($historySql);
			$return['historySql'] = $historySql;
			$return['historycontractsres'] = $historyContracts;

			$childrenSql = "SELECT parent_contract FROM contracts WHERE company_id = " . $get['company_id'] . " AND id = " . $rid . " AND is_active = 0";
			$childrenData = $callConfig->getRow($childrenSql);
			$return['childrenData'] = $childrenData;
			$parentId = $childrenData->parent_contract;
			$return['parentId'] = $parentId;

			if (!empty($parentId) && intval($parentId) > 0) {
				$parentSql = "SELECT * FROM contracts WHERE company_id = " . intval($get['company_id']) . " AND id = " . intval($parentId) . " AND is_active = 0";
				$parentContracts = $callConfig->getAllRows($parentSql);
				$return['parentSql'] = $parentSql;
				$return['parentcontractsres'] = $parentContracts;

			} else {
				$return['parentcontractsres'] = [];
			}
		}

		$sql = "SELECT id, department_name FROM departments WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
		$res = $callConfig->getAllRows($sql);
		$return['departmentsres'] = $res;


		$gencsql = "SELECT genc_detail_code, `genc_detail_name` FROM general_code_details WHERE company_id =" . $get['company_id'] . " AND genc_code = 'CONTRACT_TYPE' AND is_active = 0";
		$gencres = $callConfig->getAllRows($gencsql);
		$return['contracttypesres'] = $gencres;

		$conservsql = "SELECT id, service_name FROM contract_services WHERE company_id =" . $get['company_id'] . " AND is_active = 0";
		$conservres = $callConfig->getAllRows($conservsql);
		$return['contract_services'] = $conservres;

		/**
		$historySql = "SELECT contract_name,transaction_no, start_date, end_date, contract_amount FROM contracts WHERE company_id = " . $get['company_id'] . " AND parent_contract = " . $rid . " AND is_active = 0 ";
		$historyContracts = $callConfig->getAllRows($historySql);
		$return['historycontractsres'] = $historyContracts;

		if (empty($historyContracts)) {
		    $parentSql = " SELECT contract_name, transaction_no FROM contracts WHERE company_id = " . intval($get['company_id']) . " AND parent_contract = " . intval($editcontractsres->parent_contract ?? 0) . " AND is_active = 0 ";
		    $parentContracts = $callConfig->getAllRows($parentSql);
		    $return['parentcontractsres'] = $parentContracts;
		} else {
		    $return['parentcontractsres'] = [];
		  } **/

		  return $return;
		}

		function renewcontractreact($get, $post)
		{
			global $callConfig;

			$return = [];
			$return['post'] = $post;
			$return['get'] = $get;

			$locations = '';
			if (isset($post['locations']) && is_array($post['locations'])) {
				$locations = json_encode($post['locations']);
			}

			$insert_array = array(
				'contract_name' => $post['contract_name'],
				'renewal_frequency' => $post['renewal_frequency'],
				'parent_contract_id' => $post['parent_contract_id'],
				'contract_type' => 'Renewal',
				'transaction_no' => $this->toGetApplicationSerialNumber('contracts', $post),
				'invoice_number' => $post['invoice_number'],
				'rt_number' => $post['rt_number'],
				'business_code' => $post['business_code'],
				'party_type' => $post['party_type'],
				'contract_owner_name' => $post['contract_owner_name'],
				'contract_owner_email' => $post['contract_owner_email'],
				'contract_owner_number' => $post['contract_owner_number'],
				'party_id' => $post['party_id'],
				'department_id' => $post['department_id'] ?? null,
				'type_of_contract_id' => $post['type_of_contract_id'] ?? null,
				'start_date' => $this->dateconvertdbformat($post['start_date']),
				'end_date' => $this->dateconvertdbformat($post['end_date']),
				'contract_amount' => $post['contract_amount'],
				'description' => $post['description'],
				'additional_notes' => $post['additional_notes'],
				'termination_condition' => $post['termination_condition'],
				'contract_status' => $post['contract_status'],
				'current_status' => 0,
				'auto_renew' => (isset($post['auto_renew']) && ($post['auto_renew'] == '1' || $post['auto_renew'] == 'on' || $post['auto_renew'] === true || $post['auto_renew'] == 'Y')) ? 'Y' : 'N',
				'renew_type' => $post['renew_type'],
				'state' => $post['state'],
				'isp' => $post['isp'],
				'paid_by' => $post['paid_by'],
				'internet_charges' => $post['internet_charges'],
				'hub_spoc_name' => $post['hub_spoc_name'],
				'hub_spoc_number' => $post['hub_spoc_number'],
				'wifi_pass' => $post['wifi_pass'],
				'wifi_provider' => $post['wifi_provider'],
				'additional_document' => $post['additional_document'],
				'contract_payment_terms' => $post['contract_payment_terms'],
				'typeofaggrements' => $post['typeofaggrements'],
				'description_id' => $post['description_id'],
				'locations' => $locations,
				'parent_contract' => $post['Rid'],
				'is_oem' => (isset($post['is_oem']) && ($post['is_oem'] == 'Yes' || $post['is_oem'] == 'Y')) ? 'Y' : 'N',
				'oem_registration_number' => $post['oem_registration_number'] ?? '',
				'oem_distributor_name' => $post['oem_distributor_name'] ?? '',
				'oem_product_category' => $post['oem_product_category'] ?? '',
				'cancel_status' => 'N'
			);

			$original_q = "SELECT * FROM contracts WHERE id = '" . $post['Rid'] . "'";
			$original_data = (array) $callConfig->getRow($original_q);

			$final_data = $original_data;
			foreach ($insert_array as $key => $val) {
				if ($val !== "" && $val !== null) {
					$final_data[$key] = $val;
				}
			}

			unset($final_data['id']);

			$final_data['company_id'] = $get['company_id'];
			$final_data['is_active'] = 0;
			$final_data['created_by'] = $get['user_id'];
			$final_data['originator'] = $get['user_id'];
			$final_data['created_date'] = date('Y-m-d H:i:s');
			$final_data['parent_contract_id'] = $post['Rid'];
			$final_data['parent_contract'] = $post['Rid'];
			$old_contract_type = $original_data['contract_type'] ?? 'Contract';
			$new_contract_type = 'Renewal';
			if ($old_contract_type == 'Contract' || $old_contract_type == 'Parent' || $old_contract_type == 'Contract+ Renewal') {
				$new_contract_type = 'Contract+ Renewal';
			} elseif ($old_contract_type == 'SOW' || $old_contract_type == 'associated' || $old_contract_type == 'SOW+ Renewal') {
				$new_contract_type = 'SOW+ Renewal';
			} elseif ($old_contract_type == 'Amendment' || $old_contract_type == 'Amendment+ Renewal') {
				$new_contract_type = 'Amendment+ Renewal';
			} elseif ($old_contract_type == 'Addendum' || $old_contract_type == 'Addendum+ Renewal') {
				$new_contract_type = 'Addendum+ Renewal';
			}

			$final_data['contract_type'] = $new_contract_type;

			foreach ($final_data as $key => $value) {
				if ($value === "" || $value === null) {
					if (in_array($key, ['renewal_frequency', 'parent_contract_id', 'party_id', 'contract_payment_terms', 'current_status'])) {
						$final_data[$key] = 0;
					} else if (in_array($key, ['contract_amount'])) {
						$final_data[$key] = 0.00;
					} else if (in_array($key, ['start_date', 'end_date'])) {
						$final_data[$key] = date('Y-m-d');
					} else {
						$final_data[$key] = $value ?? '';
					}
				}
			}

			$insert_data = $callConfig->insertRecord('contracts', $final_data);
			$renew_id = $insert_data['LASTID'];
			$return['contract_result'] = $insert_data;
			$return['renew_id'] = $renew_id;

			if (!empty($post['Rid'])) {
				$rid = $post['Rid'];
				$contract_query = "SELECT renew_data, contract_type FROM contracts WHERE id = '{$rid}'";

				$contract_row = $callConfig->getRow($contract_query);

				$new_renew_info = [
					'renew_id' => $renew_id,
					'start_date' => $this->dateconvertdbformat($post['start_date']),
					'end_date'
					=> $this->dateconvertdbformat($post['end_date']),
					'contract_amount' => $post['contract_amount']
				];

				$return['new_renew_info'] = $new_renew_info;
				$return['contract_row'] = $contract_row;

				$renew_history = [];
				if (!empty($contract_row->renew_data)) {
					$decoded = json_decode($contract_row->renew_data, true);
					if (is_array($decoded))
						$renew_history = $decoded;
				}
				$renew_history[] = $new_renew_info;

				$update_data = [
					'renew_data' => json_encode($renew_history),
					'updated_by' =>
					$get['user_id'],
					'updated_date' => date('Y-m-d H:i:s')
				];
				$callConfig->updateRecord('contracts', $update_data, 'id', $rid);
			}

			$related_tables = ['contract_documents', 'contract_permissions', 'contract_recharge_details', 'workflows_contract'];

			foreach ($related_tables as $table) {
				$q = "SELECT * FROM {$table} WHERE contract_id = '" . $post['Rid'] . "'";
				$rows = $callConfig->getAllRows($q);
				if (!empty($rows)) {
					foreach ($rows as $row) {
						$row = (array) $row;
						unset($row['id']);
						$row['contract_id'] = $renew_id;
						$row['created_date'] = date('Y-m-d H:i:s');
						$row['created_by'] = $get['user_id'];
						$callConfig->insertRecord($table, $row);
					}
				}
			}

			$control_array = array(
				'permissions' => isset($post['permission_data']) ? json_encode($post['permission_data']) :
				json_encode([]),
				'workflow' => isset($post['workflow_data']) ? json_encode($post['workflow_data']) : json_encode([]),
				'documents' => isset($post['document_data']) ? json_encode($post['document_data']) : json_encode([]),
				'contract_details'
				=> json_encode($insert_array),
				'version_id' => $post['version_id'] ?? 0,
				'company_id' => $get['company_id'] ?? 0,
				'contract_id' => $renew_id,
				'is_active' => 0,
				'created_by' => $get['user_id'] ?? 0,
				'created_date' => date('Y-m-d
					H:i:s')
			);
			$control_insert = $callConfig->insertRecord('contract_version', $control_array);
			$return['control_version_result'] = $control_insert;

			$endDate = !empty($post['end_date']) ? $post['end_date'] : 'N/A';
			$activity_array = array(
				'company_id' => $get['company_id'] ?? 0,
				'activity' => "Contract Renewed (New End Date:
				{$endDate})",
				'contract_id' => $renew_id,
				'activity_date' => date('Y-m-d H:i:s'),
				'is_active' => 0,
				'created_by' => $get['user_id'] ?? 0,
				'created_date' =>
				date('Y-m-d H:i:s')
			);
			$activity_insert = $callConfig->insertRecord('contract_activity', $activity_array);
			$return['activity_result'] = $activity_insert;

			if (!empty($post['Rid'])) {
				$parent_activity = array(
					'company_id' => $get['company_id'] ?? 0,
					'activity' => "Contract Renewed (New Version Created,
					New End Date: {$endDate})",
					'contract_id' => $post['Rid'],
					'activity_date' => date('Y-m-d H:i:s'),
					'is_active' => 0,
					'created_by' => $get['user_id'] ?? 0,
					'created_date' => date('Y-m-d H:i:s')
				);
				$callConfig->insertRecord('contract_activity', $parent_activity);
			}
			if (isset($post['milestones']) && is_array($post['milestones']) && count($post['milestones']) > 0) {
				if (!empty($post['Rid'])) {
					$updateQuery = "UPDATE contract_milestones SET is_active = NULL, updated_date = '" . date('Y-m-d H:i:s') . "',
					updated_by = '" . addslashes($get['user_id']) . "' WHERE transaction_id = '" . addslashes($renew_id) . "'";
					$callConfig->executeQuery($updateQuery);
				}
				foreach ($post['milestones'] as $milestone) {
					if (empty(trim($milestone['milestone_name'] ?? '')))
						continue;
					$milestoneData = [
						'company_id' => $get['company_id'],
						'transaction_id' => $renew_id,
						'transaction_date' => date('Y-m-d H:i:s'),
						'milestone' => trim($milestone['milestone_name']),
						'milestone_date' => !empty($milestone['milestone_date']) ? $milestone['milestone_date'] : null,
						'milestone_price' => !empty($milestone['milestone_price']) ? (float)  $milestone['milestone_price'] : 0,
						'is_active' => 0,
						'current_status' => 0,
						'status_date' => date('Y-m-d H:i:s'),
						'created_date' => date('Y-m-d H:i:s'),
						'created_by' => $get['user_id'],
						'updated_by' => $get['user_id'],
						'updated_date' => date('Y-m-d H:i:s'),
					];

					$milestoneInsert = $callConfig->insertRecord('contract_milestones', $milestoneData);
					$return['milestone_inserts'][] = $milestoneInsert;
				}
			} else {
				$return['milestone_inserts'] = 'No milestones found in POST.';
			}

			if (!empty($post['is_amc_contract']) && $post['is_amc_contract'] == 'Y' && !empty($renew_id)) {
				$return['amc_contract_result'] = $this->saveAmcContractData($renew_id, $get, $post);
			}

			return $return;
		}

// 	function renewcontractreact($get, $post)
// {
// 	global $callConfig;

// 	$return = [];
// 	$return['post'] = $post;
// 	$return['get'] = $get;

// 	$locations = '';
// 	if (isset($post['locations']) && is_array($post['locations'])) { 
// 		$locations = json_encode($post['locations']); 
// 	}

// 	$insert_array = array(
// 		'contract_name' => $post['contract_name'],
// 		'renewal_frequency' => $post['renewal_frequency'], 
// 		'parent_contract_id' => $post['parent_contract_id'], 
// 		'transaction_no' => $this->toGetApplicationSerialNumber('contracts',$post), 
// 		'invoice_number' => $post['invoice_number'], 
// 		'rt_number' => $post['rt_number'], 
// 		'business_code' => $post['business_code'], 
// 		'party_type' => $post['party_type'], 
// 		'contract_owner_name' => $post['contract_owner_name'], 
// 		'contract_owner_email' => $post['contract_owner_email'], 
// 		'contract_owner_number' => $post['contract_owner_number'], 
// 		'party_id' => $post['party_id'], 
// 		'start_date' => $this->dateconvertdbformat($post['start_date']), 
// 		'end_date' => $this->dateconvertdbformat($post['end_date']), 
// 		'contract_amount' => $post['contract_amount'], 
// 		'description' => $post['description'], 
// 		'additional_notes' => $post['additional_notes'], 
// 		'payment_term_id' => $post['payment_term_id'], 
// 		'termination_condition' => $post['termination_condition'], 
// 		'contract_status' => $post['contract_status'], 
// 		'current_status' => 0, 
// 		'auto_renew' => $post['auto_renew'], 
// 		'renew_type' => $post['renew_type'], 
// 		'state' => $post['state'], 
// 		'isp' => $post['isp'], 
// 		'paid_by' => $post['paid_by'], 
// 		'internet_charges' => $post['internet_charges'], 
// 		'hub_spoc_name' => $post['hub_spoc_name'], 
// 		'hub_spoc_number' => $post['hub_spoc_number'], 
// 		'wifi_pass' => $post['wifi_pass'], 
// 		'wifi_provider' => $post['wifi_provider'], 
// 		'additional_document' => $post['additional_document'], 
// 		'contract_payment_terms' => $post['contract_payment_terms'], 
// 		'typeofaggrements' => $post['typeofaggrements'], 
// 		'description_id' => $post['description_id'], 
// 		'locations' => $locations,
// 		'parent_contract' => $post['Rid'],'is_oem' => $post['is_oem'] ?? 'No', 'oem_registration_number' => $post['oem_registration_number'] ?? '', 'oem_distributor_name' => $post['oem_distributor_name'] ?? '', 'oem_product_category' => $post['oem_product_category'] ?? ''); // additional document stores comma-separated filenames

// 	$insert_array['company_id'] = $get['company_id'];
// 	$insert_array['is_active'] = 0;
// 	$insert_array['created_by'] = $get['user_id'];
// 	$insert_array['originator'] = $get['user_id'];
// 	$insert_array['created_date'] = date('Y-m-d H:i:s');

// 	$insert_data = $callConfig->insertRecord('contracts', $insert_array);
// 	$renew_id = $insert_data['LASTID']; 
// 	$return['contract_result'] = $insert_data;
// 	$return['renew_id'] = $renew_id;

// 	if (!empty($post['Rid'])) {
// 		$rid = $post['Rid'];
// 		$contract_query = "SELECT renew_data FROM contracts WHERE id = '{$rid}'";
		
// 		$contract_row = $callConfig->getRow($contract_query);

// 		$new_renew_info = [
// 			'renew_id' => $renew_id,
// 			'start_date' => $this->dateconvertdbformat($post['start_date']),
// 			'end_date' => $this->dateconvertdbformat($post['end_date']),
// 			'contract_amount' => $post['contract_amount']
// 		];
		
// 		$return['new_renew_info'] = $new_renew_info;
// 		$return['contract_row'] = $contract_row;

// 		$renew_history = [];  
// 		if (!empty($contract_row->renew_data)) { 
// 			$decoded = json_decode($contract_row->renew_data, true);
// 			if (is_array($decoded)) $renew_history = $decoded; 
// 		}
// 		$renew_history[] = $new_renew_info;  

// 		$update_data = [
// 			'renew_data' => json_encode($renew_history),
// 			'updated_by' => $get['user_id'],
// 			'updated_date' => date('Y-m-d H:i:s')
// 		];
// 		$callConfig->updateRecord('contracts', $update_data, 'id', $rid); 
// 	} 

// 	$related_tables = [
// 		'contract_documents',
// 		'contract_permissions',
// 		'contract_recharge_details',
// 		'workflows_contract'
// 	];

// 	foreach ($related_tables as $table) {
// 		$q = "SELECT * FROM {$table} WHERE contract_id = '" .$post['Rid'] . "'";
// 		$rows = $callConfig->getAllRows($q);
// 		if (!empty($rows)) {
// 			foreach ($rows as $row) {
// 				$row = (array)$row; 
// 				unset($row['id']); 
// 				$row['contract_id'] = $renew_id;
// 				$row['created_date'] = date('Y-m-d H:i:s');
// 				$row['created_by'] = $get['user_id']; 
// 				$callConfig->insertRecord($table, $row);
// 			}
// 		}
// 	}

// 		$control_array = array(
// 			'permissions' => isset($post['permission_data']) ? json_encode($post['permission_data']) : json_encode([]),
// 			'workflow' => isset($post['workflow_data']) ? json_encode($post['workflow_data']) : json_encode([]),
// 			'documents' => isset($post['document_data']) ? json_encode($post['document_data']) : json_encode([]),
// 			'contract_details' => json_encode($insert_array), 'version_id' => $post['version_id'] ?? 0, 'company_id' => $get['company_id'] ?? 0, 'contract_id' => $renew_id, 'is_active' => 0, 'created_by' => $get['user_id'] ?? 0, 'created_date' => date('Y-m-d H:i:s') );
// 		$control_insert = $callConfig->insertRecord('contract_version', $control_array);
// 		$return['control_version_result'] = $control_insert;

// 		// ---- Contract Activity ----
// 		$activity_array = array( 'company_id' => $get['company_id'] ?? 0, 'activity' => 'contract', 'contract_id' => $renew_id, 'activity_date' => date('Y-m-d H:i:s'), 'is_active' => 0, 'created_by' => $get['user_id'] ?? 0, 'created_date' => date('Y-m-d H:i:s') );
// 		$activity_insert = $callConfig->insertRecord('contract_activity', $activity_array);
// 		$return['activity_result'] = $activity_insert;

// 		if (isset($post['milestones']) && is_array($post['milestones']) && count($post['milestones']) > 0) {

// 			if (!empty($post['Rid'])) {
// 				$updateQuery = "UPDATE milestone_contracts SET is_active = NULL, updated_date = '" . date('Y-m-d H:i:s') . "',  updated_by = '" . addslashes($get['user_id']) . "' WHERE transaction_id = '" . addslashes($renew_id) . "'";
// 				$callConfig->executeQuery($updateQuery);
// 			}

// 			foreach ($post['milestones'] as $milestone) { 
// 				if (empty(trim($milestone['milestone_name'] ?? '')))
// 					continue; 
// 				$milestoneData = ['company_id' => $get['company_id'], 'transaction_id' => $renew_id, 'transaction_date' => date('Y-m-d H:i:s'), 'milestone' => trim($milestone['milestone_name']), 'milestone_date' => !empty($milestone['milestone_date']) ? $milestone['milestone_date'] : null, 'milestone_price' => !empty($milestone['milestone_price']) ? (float) $milestone['milestone_price'] : 0, 'is_active' => 0, 'current_status' => 0, 'status_date' => date('Y-m-d H:i:s'), 'created_date' => date('Y-m-d H:i:s'), 'created_by' => $get['user_id'], 'updated_by' => $get['user_id'], 'updated_date' => date('Y-m-d H:i:s'), ];

// 				$milestoneInsert = $callConfig->insertRecord('milestone_contracts', $milestoneData);
// 				$return['milestone_inserts'][] = $milestoneInsert;
// 			}

// 		} else {
// 			$return['milestone_inserts'] = 'No milestones found in POST.';
// 		}

// 	return $return;
// }


		function cancelcontractreact($get, $post)
		{
			global $callConfig;

			$contract_id = $post['Rid'] ?? 0;
			$reason = $post['cancel_reason'] ?? '';

			if (empty($contract_id)) {
				return ['status' => 'error', 'message' => 'Contract ID is required'];
			}

			if (empty($reason)) {
				return ['status' => 'error', 'message' => 'Cancellation reason is required'];
			}

    // Bypassing updateRecord due to broken action_log table columns in the core db.class.php
			$sql = "UPDATE contracts SET 
			cancel_status = 'Y',
			cancel_reason = '" . mysqli_real_escape_string($callConfig->con, $reason) . "',
			cancel_date = '" . date('Y-m-d') . "',
			updated_by = '" . mysqli_real_escape_string($callConfig->con, (string) ($get['user_id'] ?? 0)) . "',
			updated_date = '" . date('Y-m-d H:i:s') . "'
			WHERE id = '" . mysqli_real_escape_string($callConfig->con, $contract_id) . "'";

			$res = $callConfig->executeQuery($sql);

			if (isset($res['status']) && ($res['status'] === 'sucess' || $res['status'] === 'success')) {
				$activity_array = [
					'company_id' => $get['company_id'] ?? 0,
					'activity' => "Contract Cancelled (Reason: {$reason})",
					'contract_id' => $contract_id,
					'activity_date' => date('Y-m-d H:i:s'),
					'is_active' => 0,
					'created_by' => $get['user_id'] ?? 0,
					'created_date' => date('Y-m-d H:i:s')
				];
				$callConfig->insertRecord('contract_activity', $activity_array);
			}

			return [
				'status' => (isset($res['status']) && ($res['status'] === 'sucess' || $res['status'] === 'success')) ? 'success' :
				'error',
				'message' => (isset($res['status']) && ($res['status'] === 'sucess' || $res['status'] === 'success')) ? 'Contract
				cancelled successfully' : 'Failed to cancel contract',
				'result' => $res
			];
		}

		
		function sendCronJobContractExpiryNotifications($get){
			global $callConfig;
			$startdate =  date('Y-m-d');
			$enddate =  date('Y-m-d', strtotime('+3 months'));
			$consql = "SELECT cn.contract_name, cn.transaction_no, cn.start_date, cn.end_date, cn.locations FROM contracts cn WHERE cn.company_id = '".$get['company_id']."' AND cn.end_date >= '".$startdate."' AND cn.end_date <= '".$enddate."' AND (cn.cancel_status = 'N' OR cn.cancel_status IS NULL) AND cn.is_active = 0";
			$consres = $callConfig->getAllRows($consql);	

			$encoded = urlencode(json_encode((array)$consres));
			$insert_array = array('company_id'=>$get['company_id'], 'start_date_time'=>date('Y-m-d h:i:s'), 'command'=> urlencode($consql), 'api_data'=> $encoded, 'is_active'=>'0', 'current_status'=>'0', 'created_date'=>date('Y-m-d h:i:s'));
	 		$ccres = $callConfig->insertRecord('cron_jobs', $insert_array);

	 		$locationssql = "SELECT `id`, `location_name` FROM locations WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ORDER BY location_name ASC";
			$locationsres = $callConfig->getAllRows($locationssql);
			$locations = array();
			foreach($locationsres as $red){
				$locations[$red->id] = $red;
			}
	 		foreach ($consres as $contract) {
	 			$locationsid = json_decode($contract->locations ,1);
             	$locationnames = "";
            	foreach($locationsid as $locid){ 
            		if($locations[$locid]->location_name !=""){
            			$locationnames .= $locations[$locid]->location_name.", "; 
            		}
            	}
            	$contract->location_name  = $locationnames;
			    $days_remaining = floor((strtotime($contract->end_date) - strtotime($startdate)) / 86400);
			    // Expiring Today
			    if ($days_remaining == 0) {
			        $todayContracts[] = $contract;
			    }
			    // Expiring in Next 7 Days
			    if ($days_remaining > 0 && $days_remaining <= 7) {
			        $weeklyContracts[] = $contract;
			    }
			    // Expiring in Next 30 Days
			    if ($days_remaining > 0 && $days_remaining <= 30) {
			        $monthlyContracts[] = $contract;
			    }
			    // Expiring in Next 90 Days
			    if ($days_remaining > 0 && $days_remaining <= 90) {
			        $quarterlyContracts[] = $contract;
			    }
			    // Already Expired
			    if ($days_remaining < 0) {
			        $expiredContracts[] = $contract;
			    }
			}
			$email_body .= $this->sendCronJobContractExpiryNotificationsExtrafuncall('Contracts Expiring Today', $todayContracts);
			$email_body .= $this->sendCronJobContractExpiryNotificationsExtrafuncall('Contracts Expiring Within 7 Days', $weeklyContracts);
			$email_body .= $this->sendCronJobContractExpiryNotificationsExtrafuncall('Contracts Expiring Within 1 Month', $monthlyContracts);
			$email_body .= $this->sendCronJobContractExpiryNotificationsExtrafuncall('Contracts Expiring Within 3 Months', $quarterlyContracts);
			echo $email_body;
			$conditins = " AND JSON_SEARCH(role_id, 'one', 'CONTRACT_ADMIN') IS NOT NULL";
			$seluser = "SELECT id, company_id, user_email, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0".$conditins;
			$return['seluser'] = $seluser;
			$seluserres = $callConfig->getAllRows($seluser);
			$emailuserdetails = array();
			foreach($seluserres as $seluser){
				$emailarray  = $post;
				$emailarray['user_email_id'] = $seluser->user_email;
				$emailarray['user_display_name'] = $seluser->display_name;
				$emailarray['to_company_id'] = $seluser->company_id;
				$emailarray['session_company_id'] = $seluser->company_id;
				$emailarray['default_module_id'] = 40;
				$emailarray['created_by'] = $get['user_id'];
				$emailarray['extra_informations'] = $email_body;		
				$notarry = array();
				$sendnotification[] =$this->sendNotificationAndEmails($notarry,$emailarray,76);
			}
			if($ccres['LASTID'] !=""){	
				$encoded = urlencode(json_encode((array)$sendnotification));	
			 	$update_array = array('end_date_time'=>date('Y-m-d h:i:s'),'result_data'=>$encoded,'updated_date'=>date('Y-m-d h:i:s'));
				$wh = "id = '".$ccres['LASTID']."'";
				$ccresupdate = $callConfig->updateRecord('cron_jobs',$update_array,'','',$wh);
			}
			return $get;
		}	

		function sendCronJobContractExpiryNotificationsExtrafuncall($title, $contracts){
		    $html = "<h3>{$title}</h3>";
		    if (empty($contracts)) {
		        return $html . "<p>No contracts found.</p>";
		    }
		    $html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">
		                <tr>
		                	<th>Contract No</th>
		                    <th>Contract Name</th>
		                    <th>Location</th>
		                    <th>Start Date</th>
		                    <th>End Date</th>
		                </tr>';
		    foreach ($contracts as $contract) {
		        $html .= '<tr>
		                    <td>'.$contract->transaction_no.'</td>
		                    <td>'.$contract->contract_name.'</td>
		                    <td>'.$contract->location_name.'</td>
		                    <td>'.$contract->start_date.'</td>
		                    <td>'.$contract->end_date.'</td>
		                  </tr>';
		    }
		    $html .= '</table><br>';
		    return $html;
		}












		////////////// kotesss

	function AMCInsertContractsReact($get, $post){
		global $callConfig;

		$return = [];
		$return['post'] = $post;
		$return['get'] = $get;

		$locations = '';
		if (isset($post['locations']) && is_array($post['locations'])) {
			$locations = json_encode($post['locations']);
		}

		$contract_services_id = '';
		if (isset($post['contract_services_id']) && is_array($post['contract_services_id'])) {
			$contract_services_id = json_encode($post['contract_services_id']);
		}

		$contact_personsary = array();
		if (isset($post['contact_persons']) && is_array($post['contact_persons']) && count((array)($post['contact_persons']['name'] ?? array())) > 0) {
			foreach ($post['contact_persons']['name'] as $key => $conpers) {
				if ($post['contact_persons']['name'][$key] != "" && $post['contact_persons']['number'][$key] != "") {
					$contact_personsary[] = array(
						'name' => $post['contact_persons']['name'][$key],
						'email' => $post['contact_persons']['email'][$key],
						'number' => $post['contact_persons']['number'][$key]
					);
				}
			}
		}
		$contact_persons = json_encode($contact_personsary);

		$contract_type = 'Contract';
		if (!empty($post['parent_contract_id']) && $post['parent_contract_id'] != 0) {
			$contract_type = 'SOW';
		}

		$insert_array = array(
			'contract_name' => $post['contract_name'],
			'renewal_frequency' => $post['renewal_frequency'],
			'parent_contract_id' => $post['parent_contract_id'],
			'contract_type' => $contract_type,
			'invoice_number' => $post['invoice_number'],
			'rt_number' => $post['rt_number'],
			'business_code' => $post['business_code'],
			'party_type' => $post['party_type'],
			'contract_owner_name' => $post['contract_owner_name'],
			'contract_owner_email' => $post['contract_owner_email'],
			'contract_owner_number' => $post['contract_owner_number'],
			'party_id' => $post['party_id'],
			'department_id' => $post['department_id'],
			'type_of_contract_id' => $post['type_of_contract_id'],
			'start_date' => $this->dateconvertdbformat($post['start_date']),
			'end_date' => $this->dateconvertdbformat($post['end_date']),
			'contract_amount' => $post['contract_amount'],
			'description' => $post['description'],
			'additional_notes' => $post['additional_notes'],
			'payment_term_id' => $post['payment_term_id'],
			'termination_condition' => $post['termination_condition'],
			'contract_status' => $post['contract_status'],
			'current_status' => 0,
			'auto_renew' => (isset($post['auto_renew']) && ($post['auto_renew'] == '1' || $post['auto_renew'] == 'on' || $post['auto_renew'] === true || $post['auto_renew'] == 'Y')) ? 'Y' : 'N',
			'renew_type' => $post['renew_type'],
			'state' => $post['state'],
			'isp' => $post['isp'],
			'paid_by' => $post['paid_by'],
			'internet_charges' => $post['internet_charges'],
			'hub_spoc_name' => $post['hub_spoc_name'],
			'hub_spoc_number' => $post['hub_spoc_number'],
			'wifi_pass' => $post['wifi_pass'],
			'wifi_provider' => $post['wifi_provider'],
			'additional_document' => $post['additional_document'],
			'contract_payment_terms' => $post['contract_payment_terms'],
			'typeofaggrements' => $post['typeofaggrements'],
			'description_id' => $post['description_id'],
			'locations' => $locations,
			'is_oem' => (isset($post['is_oem']) && ($post['is_oem'] == 'Yes' || $post['is_oem'] == 'Y')) ? 'Y' : 'N',
			'oem_registration_number' => $post['oem_registration_number'] ?? '',
			'oem_distributor_name' => $post['oem_distributor_name'] ?? '',
			'oem_product_category' => $post['oem_product_category'] ?? '',
			'cancel_status' => 'N',
			'is_amc_contract' => (!empty($post['is_amc_contract']) && in_array($post['is_amc_contract'], array('Y', 'Yes', '1', 1), true)) ? 'Y' : 'N',
			'contract_services_id' => $contract_services_id,
			'contact_persons' => $contact_persons
		);

		if (!empty($post['Rid'])) {
			$edit_option = $post['edit_option'] ?? 'SaveUpdate';
			if ($edit_option === '1')
				$edit_option = 'Amendment';
			if ($edit_option === '2')
				$edit_option = 'Addendum';

			if ($edit_option === 'Amendment' || $edit_option === 'Addendum') {
				$original_q = "SELECT * FROM contracts WHERE id = '" . $post['Rid'] . "'";
				$original_data = (array) $callConfig->getRow($original_q);
				$final_data = $original_data;
				foreach ($insert_array as $key => $val) {
					if ($val !== "" && $val !== null) {
						$final_data[$key] = $val;
					}
				}

				unset($final_data['id']);

				$final_data['company_id'] = $get['company_id'];
				$final_data['is_active'] = 0;
				$final_data['transaction_no'] = $this->toGetApplicationSerialNumber('contracts', $post);
				$final_data['created_by'] = $get['user_id'];
				$final_data['originator'] = $get['user_id'];
				$final_data['created_date'] = date('Y-m-d H:i:s');
				$final_data['parent_contract_id'] = $post['Rid'];
				$final_data['parent_contract'] = $post['Rid'];
				$final_data['contract_type'] = $edit_option;

				foreach ($final_data as $key => $value) {
					if ($value === "" || $value === null) {
						if (in_array($key, ['renewal_frequency', 'parent_contract_id', 'party_id', 'payment_term_id', 'current_status'])) {
							$final_data[$key] = 0;
						} else if (in_array($key, ['contract_amount'])) {
							$final_data[$key] = 0.00;
						} else if (in_array($key, ['start_date', 'end_date'])) {
							$final_data[$key] = date('Y-m-d');
						} else {
							$final_data[$key] = $value ?? '';
						}
					}
				}

				$insert_data = $callConfig->insertRecord('contracts', $final_data);
				$contract_id = $insert_data['LASTID'];
				$return['contract_result'] = $insert_data;
				$return['contract_result11'] = $insert_data;
				
				if($insert_data['status'] === 'sucess' || $insert_data['status'] === 'success') {
					$seluser = "SELECT id FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND JSON_SEARCH(role_id, 'one', 'CONTRACT_ADMIN') IS NOT NULL";
					$return['seluser'] = $seluser;
					$seluserres = $callConfig->getAllRows($seluser);
					$notification_users[] = $final_data['originator'];
					foreach($seluserres as $seluser){
						$notification_users[] = $seluser->id;
					}
					$msg = "Contract ".$final_data['contract_name']." - ".$final_data['transaction_no']." has been created successfully.";
					$notarry = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$contract_id,'application_table_name'=>'contracts','type'=>'INFORMATION', 'notification_users'=>$notification_users,'option'=>$option,'assigner_id'=>$get['user_id']);
					$sendnotification = $this->tosendnotifiations($notarry);
					$return['tosendnotifiations'] = $notarry;
				}

				if ($edit_option === 'Amendment' && ($insert_data['status'] === 'sucess' || $insert_data['status'] === 'success')) {
					$update_original = [
						'is_active' => null,
						'contract_status' => 'Expired',
						'updated_by' => $get['user_id'],
						'updated_date' => date('Y-m-d H:i:s')
					];
					$callConfig->updateRecord('contracts', $update_original, 'id', $post['Rid']);
				}

				if ($insert_data['status'] === 'sucess' || $insert_data['status'] === 'success') {
					$related_tables = [
						'contract_documents',
						'contract_permissions',
						'contract_recharge_details',
						'workflows_contract'
					];

					foreach ($related_tables as $table) {
						$q = "SELECT * FROM {$table} WHERE contract_id = '" . $post['Rid'] . "'";
						$rows = $callConfig->getAllRows($q);
						if (!empty($rows)) {
							foreach ($rows as $row) {
								$row = (array) $row;
								unset($row['id']);
								$row['contract_id'] = $contract_id;
								$row['created_date'] = date('Y-m-d H:i:s');
								$row['created_by'] = $get['user_id'];
								$callConfig->insertRecord($table, $row);
							}
						}
					}
				}
			} else {
				$insert_array['updated_by'] = $get['user_id'];
				$insert_array['updated_date'] = date('Y-m-d H:i:s');

				$curr_q = "SELECT contract_type FROM contracts WHERE id = '" . $post['Rid'] . "'";
				$curr_row = $callConfig->getRow($curr_q);
				if ($curr_row) {
					$insert_array['contract_type'] = $curr_row->contract_type;
				}

				if (trim((string) ($insert_array['payment_term_id'] ?? '')) == '') {
					unset($insert_array['payment_term_id']);
				}

				$res = $callConfig->updateRecord('contracts', $insert_array, 'id', $post['Rid']);
				$contract_id = $post['Rid'];
				$return['contract_result'] = $res;
			}
		} else {
			$insert_array['company_id'] = $get['company_id'];
			$insert_array['is_active'] = 0;
			$insert_array['transaction_no'] = $this->toGetApplicationSerialNumber('contracts', $post);
			$insert_array['created_by'] = $get['user_id'];
			$insert_array['originator'] = $get['user_id'];
			$insert_array['created_date'] = date('Y-m-d H:i:s');
			$insert_data = $callConfig->insertRecord('contracts', $insert_array);
			$contract_id = $insert_data['LASTID'];
			$return['contract_result'] = $insert_data;

			if($insert_data['status'] === 'sucess' || $insert_data['status'] === 'success') {
				$selected_location_ids = array();
				if(isset($post['amc_loc']) && count((array)$post['amc_loc']) > 0){
					foreach($post['amc_loc'] as $locRow){
						$locationId = (int)($locRow['location_id'] ?? 0);
						if($locationId > 0){
							$selected_location_ids[] = $locationId;
						}
					}
				}
				$selected_location_ids = array_values(array_unique($selected_location_ids));
				$admin_cond = "1=0";
				if(count((array)$selected_location_ids) > 0){
					$admin_cond = "(JSON_SEARCH(role_id, 'one', 'CONTRACT_ADMIN') IS NOT NULL AND location_id IN (".implode(',', $selected_location_ids)."))";
				}
				if($insert_array['originator'] !=""){
					$conditins = " AND (".$admin_cond." OR id = '".$insert_array['originator']."')";
				}else{
					$conditins = " AND ".$admin_cond;
				}
					$seluser = "SELECT id, company_id, user_email FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0".$conditins;
					$return['seluser'] = $seluser;
					$seluserres = $callConfig->getAllRows($seluser);
					//$notification_users[] = $insert_array['originator'];
					foreach($seluserres as $seluser){
						if($seluser->id == $insert_array['originator']){
								$originatordetailary = $seluser;
						} 
					}
					foreach($seluserres as $seluser){
						$notification_users[] = $seluser->id;

					//email part
						$emailarray  = $post;
						$emailarray['user_email_id'] = $seluser->user_email;
						$emailarray['user_display_name'] = $seluser->display_name;
						$emailarray['to_company_id'] = $seluser->company_id;
						$emailarray['session_company_id'] = $seluser->company_id;
						$emailarray['default_module_id'] = 40;
						$emailarray['created_by'] = $get['user_id'];

						$emailarray['contract_creation_or_updation'] = "Inserted";		
						$emailarray['contract_vendor_name'] = "";
						$emailarray['contract_name'] = $insert_array['contract_name'];
						$emailarray['contract_number'] = $insert_array['transaction_no'];
						$emailarray['contract_type'] = "";
						$emailarray['contract_user_name'] = $originatordetailary->display_name;;
						$emailarray['contract_update_datetime'] = date('Y-m-d H:i:s');

						$sendnotification =$this->sendNotificationAndEmails($notarry,$emailarray,6);
						//email part

					}
					$msg = "Contract ".$insert_array['contract_name']." - ".$insert_array['transaction_no']." has been created successfully.";
					$notarry = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$contract_id,'application_table_name'=>'contracts','type'=>'INFORMATION', 'notification_users'=>$notification_users,'option'=>$option,'assigner_id'=>$get['user_id']);
					$sendnotification = $this->tosendnotifiations($notarry);
					$return['tosendnotifiations'] = $notarry;
			}

		}

		$control_array = array(
			'permissions' => isset($post['permission_data']) ? json_encode($post['permission_data']) : json_encode([]),
			'workflow' => isset($post['workflow_data']) ? json_encode($post['workflow_data']) : json_encode([]),
			'documents' => isset($post['document_data']) ? json_encode($post['document_data']) : json_encode([]),
			'contract_details' => json_encode($insert_array),
			'version_id' => $post['version_id'] ?? 0,
			'company_id' => $get['company_id'] ?? 0,
			'contract_id' => $contract_id,
			'is_active' => 0,
			'created_by' => $get['user_id'] ?? 0,
			'created_date' => date('Y-m-d H:i:s')
		);
		$control_insert = $callConfig->insertRecord('contract_version', $control_array);
		$return['control_version_result'] = $control_insert;

				// ---- Contract Activity ----
		$endDate = !empty($post['end_date']) ? $post['end_date'] : 'N/A';
		if (empty($post['Rid'])) {
			$activity_text = "Contract Created (Valid until {$endDate})";
		} else {
			$edit_option = $post['edit_option'] ?? 'SaveUpdate';
			if ($edit_option === '1')
				$edit_option = 'Amendment';
			if ($edit_option === '2')
				$edit_option = 'Addendum';

			if ($edit_option === 'Amendment') {
				$activity_text = "Contract Amended (New Version Created, Valid until {$endDate})";
			} else if ($edit_option === 'Addendum') {
				$activity_text = "Contract Addendum Added (New Version Created, Valid until {$endDate})";
			} else {
				$activity_text = "Contract Updated (Standard Save, Valid until {$endDate})";
			}
		}
		$activity_array = array('company_id' => $get['company_id'] ?? 0, 'activity' => $activity_text, 'contract_id' => $contract_id, 'activity_date' => date('Y-m-d H:i:s'), 'is_active' => 0, 'created_by' => $get['user_id'] ?? 0, 'created_date' => date('Y-m-d H:i:s'));
		$activity_insert = $callConfig->insertRecord('contract_activity', $activity_array);
		$return['activity_result'] = $activity_insert;

				// Log on Parent Contract if it's an Amendment or Addendum
		if (!empty($post['Rid']) && ($edit_option === 'Amendment' || $edit_option === 'Addendum')) {
			$parent_msg = ($edit_option === 'Amendment') ? "Contract Amended (New Version Created, Valid until {$endDate})" : "Contract Addendum Added (New Version Created, Valid until {$endDate})";
			$parent_activity = array('company_id' => $get['company_id'] ?? 0, 'activity' => $parent_msg, 'contract_id' => $post['Rid'], 'activity_date' => date('Y-m-d H:i:s'), 'is_active' => 0, 'created_by' => $get['user_id'] ?? 0, 'created_date' => date('Y-m-d H:i:s'));
			$callConfig->insertRecord('contract_activity', $parent_activity);
		}

		if (isset($post['milestones']) && is_array($post['milestones']) && count($post['milestones']) > 0) {

			if (!empty($post['Rid'])) {
				$updateQuery = "UPDATE contract_milestones SET is_active = NULL, updated_date = '" . date('Y-m-d H:i:s') . "',  updated_by = '" . addslashes($get['user_id']) . "' WHERE transaction_id = '" . addslashes($contract_id) . "'";
				$callConfig->executeQuery($updateQuery);
			}

			foreach ($post['milestones'] as $milestone) {
				if (empty(trim($milestone['milestone_name'] ?? '')))
					continue;
				$milestoneData = ['company_id' => $get['company_id'], 'transaction_id' => $contract_id, 'transaction_date' => date('Y-m-d H:i:s'), 'milestone' => trim($milestone['milestone_name']), 'milestone_date' => !empty($milestone['milestone_date']) ? $this->dateconvertdbformat($milestone['milestone_date']) : null, 'milestone_price' => !empty($milestone['milestone_price']) ? (float) $milestone['milestone_price'] : 0, 'is_active' => 0, 'current_status' => 0, 'status_date' => date('Y-m-d H:i:s'), 'created_date' => date('Y-m-d H:i:s'), 'created_by' => $get['user_id'], 'updated_by' => $get['user_id'], 'updated_date' => date('Y-m-d H:i:s'),];

				$milestoneInsert = $callConfig->insertRecord('contract_milestones', $milestoneData);
				$return['milestone_inserts'][] = $milestoneInsert;
			}

		} else {
			$return['milestone_inserts'] = 'No milestones found in POST.';
		}

		if ($insert_array['is_amc_contract'] == 'Y') {
			$return['amc_contract_result'] = $this->saveAmcContractData($contract_id, $get, $post);
		} else {
			$return['commercial_details_result'] = $this->saveContractCommercialDetails($contract_id, $get, $post);
		}
		

		return $return;
	}

		function saveAmcContractData($contract_id, $get, $post)
	{
		global $callConfig;
		$return = array();
		$companyId = $get['company_id'] ?? 0;
		$userId = $get['user_id'] ?? 0;
		$now = date('Y-m-d H:i:s');
		if (empty($contract_id)) {
			return array('status' => 'error', 'message' => 'Missing contract id');
		}

		$deactivate = function ($table) use ($callConfig, $contract_id, $userId, $now) {
			$sql = "UPDATE {$table} SET is_active = NULL, updated_by = '" . addslashes($userId) . "', updated_date = '{$now}' WHERE contract_id = '" . addslashes($contract_id) . "' AND is_active = 0";
			$callConfig->executeQuery($sql);
		};

		$deactivate('contract_amc_details');
		$deactivate('contract_amc_locations');
		$deactivate('contract_amc_assets');

		$contractStartYmd = $this->amcParseDateYmd($post['start_date'] ?? '');
		$contractEndYmd = $this->amcParseDateYmd($post['end_date'] ?? '');

		$reminderDays = array();
		if (isset($post['reminder_days']) && is_array($post['reminder_days'])) {
			$reminderDays = array_values($post['reminder_days']);
		}

		$completionRules = array();
		if (isset($post['completion_req']) && is_array($post['completion_req'])) {
			$completionRules = array_values($post['completion_req']);
		}

		$billingCycle = $post['amc_billing_cycle'] ?? null;
		if ($billingCycle == 'halfyearly') {
			$billingCycle = 'half_yearly';
		}

		$detailsRow = array(
			'company_id' => $companyId,
			'contract_id' => $contract_id,
			'payment_mode' => $post['amc_payment_mode'] ?? 'billing_cycle',
			'billing_cycle' => $billingCycle,
			'billing_day' => !empty($post['amc_billing_day']) ? (int) $post['amc_billing_day'] : null,
			'is_draft' => 0,
			'service_mode' => ((($post['amc_service_mode'] ?? '') == 'inhouse') || (($post['party_type'] ?? '') == 'inhouse')) ? 'inhouse' : 'vendor',
			'department_id' => (((($post['amc_service_mode'] ?? '') == 'inhouse') || (($post['party_type'] ?? '') == 'inhouse')) && !empty($post['department_id'])) ? (int) $post['department_id'] : null,
			'default_employee_id' => !empty($post['amc_default_employee_id']) ? (int) $post['amc_default_employee_id'] : null,
			'customer_connect_id' => ((($post['party_type'] ?? '') == 'vendor') && !empty($post['customer_connect_id'])) ? (int) $post['customer_connect_id'] : null,
			'gate_entry' => $post['gate_entry'] ?? null,
			'reminder_days' => json_encode($reminderDays),
			'completion_rules' => json_encode($completionRules),
			'escalation_config' => json_encode($post['amc_escalation_config'] ?? array()),
			'is_active' => 0,
			'created_by' => $userId,
			'created_date' => $now,
			'updated_by' => $userId,
			'updated_date' => $now,
		);
		$return['details'] = $callConfig->insertRecord('contract_amc_details', $detailsRow);

		$amcServiceMode = $detailsRow['service_mode'];
		$contractDefaultEmployeeId = (int) ($detailsRow['default_employee_id'] ?? 0);
		$locationIdsForContract = array();
		$insertedAssetsForSchedule = array();
		$amcLocations = $post['amc_loc'] ?? array();
		if (is_array($amcLocations)) {
			foreach ($amcLocations as $locIdx => $locRow) {
				$locationId = (int) ($locRow['location_id'] ?? 0);
				if ($locationId <= 0) {
					continue;
				}
				$locStartYmd = $this->amcParseDateYmd($locRow['service_start_date'] ?? '');
				$locEndYmd = $this->amcParseDateYmd($locRow['service_end_date'] ?? '');
				if (($locStartYmd == '' || $locStartYmd == null) && ($contractStartYmd != '' && $contractStartYmd != null)) {
					$locStartYmd = $contractStartYmd;
				}
				if (($locEndYmd == '' || $locEndYmd == null) && ($contractEndYmd != '' && $contractEndYmd != null)) {
					$locEndYmd = $contractEndYmd;
				}
				$locInsert = array(
					'company_id' => $companyId,
					'contract_id' => $contract_id,
					'location_id' => $locationId,
					'service_start_date' => ($locStartYmd != '' && $locStartYmd != null) ? $locStartYmd : null,
					'service_end_date' => ($locEndYmd != '' && $locEndYmd != null) ? $locEndYmd : null,
					'is_active' => 0,
					'created_by' => $userId,
					'created_date' => $now,
					'updated_by' => $userId,
					'updated_date' => $now,
				);
				$locRes = $callConfig->insertRecord('contract_amc_locations', $locInsert);
				$return['locations'][] = $locRes;
				$amcLocationId = (int) ($locRes['LASTID'] ?? 0);
				if ($amcLocationId <= 0 && in_array($locRes['status'] ?? '', array('sucess', 'success'))) {
					$locLookupSql = "SELECT id FROM contract_amc_locations WHERE company_id = " . intval($companyId)
						. " AND contract_id = '" . addslashes($contract_id) . "' AND location_id = '" . addslashes($locationId) . "'"
						. " AND is_active = 0 ORDER BY id DESC LIMIT 1";
					$locLookupRow = $callConfig->getRow($locLookupSql);
					if ($locLookupRow) {
						$locLookupArr = is_array($locLookupRow) ? $locLookupRow : (array) $locLookupRow;
						$amcLocationId = (int) ($locLookupArr['id'] ?? 0);
					}
				}
				$locationIdsForContract[] = $locationId;

				$amcAssets = $post['amc_asset'][$locIdx] ?? array();
				if (!is_array($amcAssets)) {
					continue;
				}
				foreach ($amcAssets as $assetRow) {
					$assetId = (int) ($assetRow['asset_id'] ?? 0);
					if ($assetId <= 0) {
						continue;
					}
					$slots = $assetRow['service_slots'] ?? array();
					if (!is_array($slots)) {
						$slots = array();
					}
					$assetStartYmd = $this->amcParseDateYmd($assetRow['service_start_date'] ?? '');
					$assetEndYmd = $this->amcParseDateYmd($assetRow['service_end_date'] ?? '');
					if (($assetStartYmd == '' || $assetStartYmd == null) && ($locStartYmd != '' && $locStartYmd != null)) {
						$assetStartYmd = $locStartYmd;
					}
					if (($assetEndYmd == '' || $assetEndYmd == null) && ($locEndYmd != '' && $locEndYmd != null)) {
						$assetEndYmd = $locEndYmd;
					}
					if (($assetStartYmd == '' || $assetStartYmd == null) && ($contractStartYmd != '' && $contractStartYmd != null)) {
						$assetStartYmd = $contractStartYmd;
					}
					if (($assetEndYmd == '' || $assetEndYmd == null) && ($contractEndYmd != '' && $contractEndYmd != null)) {
						$assetEndYmd = $contractEndYmd;
					}
					$scheduleStartYmd = ($assetStartYmd != '' && $assetStartYmd != null) ? $assetStartYmd : null;
					$scheduleEndYmd = ($assetEndYmd != '' && $assetEndYmd != null) ? $assetEndYmd : null;
					$assetInsert = array(
						'company_id' => $companyId,
						'contract_id' => $contract_id,
						'contract_amc_location_id' => $amcLocationId,
						'asset_id' => $assetId,
						'asset_name' => $assetRow['asset_name'] ?? '',
						'service_frequency' => $assetRow['service_frequency'] ?? '',
						'visits_per_interval' => !empty($assetRow['visits_per_interval']) ? (int) $assetRow['visits_per_interval'] : 1,
						'service_start_date' => $scheduleStartYmd,
						'service_end_date' => $scheduleEndYmd,
						'service_slots' => json_encode(array_values($slots)),
						'is_active' => 0,
						'created_by' => $userId,
						'created_date' => $now,
						'updated_by' => $userId,
						'updated_date' => $now,
					);
					if ($amcServiceMode == 'inhouse' && $contractDefaultEmployeeId > 0) {
						$assetInsert['default_employee_id'] = $contractDefaultEmployeeId;
					}
					$assetRes = $callConfig->insertRecord('contract_amc_assets', $assetInsert);
					$return['assets'][] = $assetRes;
					$amcAssetRowId = (int) ($assetRes['LASTID'] ?? 0);
					if ($amcAssetRowId <= 0 && in_array($assetRes['status'] ?? '', array('sucess', 'success'))) {
						$lookupSql = "SELECT id FROM contract_amc_assets WHERE company_id = " . intval($companyId)
							. " AND contract_id = '" . addslashes($contract_id) . "' AND asset_id = '" . addslashes($assetId) . "'"
							. " AND is_active = 0 ORDER BY id DESC LIMIT 1";
						$lookupRow = $callConfig->getRow($lookupSql);
						if ($lookupRow) {
							$lookupArr = is_array($lookupRow) ? $lookupRow : (array) $lookupRow;
							$amcAssetRowId = (int) ($lookupArr['id'] ?? 0);
						}
					}
					if ($amcAssetRowId > 0) {
						$insertedAssetsForSchedule[] = array(
							'contract_amc_asset_id' => $amcAssetRowId,
							'contract_amc_location_id' => $amcLocationId,
							'asset_id' => $assetId,
							'service_frequency' => $assetInsert['service_frequency'],
							'visits_per_interval' => $assetInsert['visits_per_interval'],
							'service_start_date' => $scheduleStartYmd,
							'service_end_date' => $scheduleEndYmd,
							'service_slots' => $slots,
							'service_mode' => $amcServiceMode,
							'default_employee_id' => $assetInsert['default_employee_id'] ?? null,
						);
					}
				}
			}
		}

		if (count($locationIdsForContract) > 0) {
			$callConfig->updateRecord('contracts', array('locations' => json_encode(array_values(array_unique($locationIdsForContract)))), 'id', $contract_id);
		}

		$return['schedule_inserts'] = array();
		$return['schedule_errors'] = array();
		if (count($insertedAssetsForSchedule) > 0) {
			$deactivate('contract_amc_service_schedules');
			foreach ($insertedAssetsForSchedule as $assetRow) {
				$assetScheduleRows = $this->generateContractAmcSchedulesForAsset($contract_id, $companyId, $userId, $assetRow, $now);
				if (count((array)$assetScheduleRows) > 0) {
					foreach ($assetScheduleRows as $scheduleRow) {
						if (($scheduleRow['status'] ?? '') == 'fail') {
							$return['schedule_errors'][] = $scheduleRow['message'] ?? 'Schedule insert failed';
						}
					}
				}
				$return['schedule_inserts'] = array_merge(
					$return['schedule_inserts'],
					$assetScheduleRows
				);
			}
		}
		$return['schedule_count'] = 0;
		if (count((array)$return['schedule_inserts']) > 0) {
			foreach ($return['schedule_inserts'] as $scheduleRow) {
				if (in_array($scheduleRow['status'] ?? '', array('sucess', 'success'))) {
					$return['schedule_count']++;
				}
			}
		}

		return $return;
	}

	function saveContractCommercialDetails($contract_id, $get, $post)
	{
		global $callConfig;
		$return = array();
		$companyId = $get['company_id'] ?? 0;
		$userId = $get['user_id'] ?? 0;
		$now = date('Y-m-d H:i:s');
		if (empty($contract_id)) {
			return array('status' => 'error', 'message' => 'Missing contract id');
		}

		$sql = "UPDATE contract_amc_details SET is_active = NULL, updated_by = '" . addslashes($userId) . "', updated_date = '{$now}' WHERE contract_id = '" . addslashes($contract_id) . "' AND is_active = 0";
		$callConfig->executeQuery($sql);

		$billingCycle = $post['amc_billing_cycle'] ?? null;
		if ($billingCycle == 'halfyearly') {
			$billingCycle = 'half_yearly';
		}

		$detailsRow = array(
			'company_id' => $companyId,
			'contract_id' => $contract_id,
			'payment_mode' => $post['amc_payment_mode'] ?? 'billing_cycle',
			'billing_cycle' => $billingCycle,
			'billing_day' => !empty($post['amc_billing_day']) ? (int) $post['amc_billing_day'] : null,
			'is_draft' => 0,
			'is_active' => 0,
			'created_by' => $userId,
			'created_date' => $now,
			'updated_by' => $userId,
			'updated_date' => $now,
		);
		$return['details'] = $callConfig->insertRecord('contract_amc_details', $detailsRow);
		return $return;
	}
	function generateContractAmcSchedulesForAsset($contract_id, $companyId, $userId, $assetRow, $now)
	{
		global $callConfig;
		$inserted = array();
		$slots = $assetRow['service_slots'] ?? array();
		if (is_string($slots)) {
			$slots = json_decode($slots, true) ?: array();
		}
		if (!is_array($slots)) {
			$slots = array();
		}
		$visits = $this->buildAmcPlannedVisits(
			$assetRow['service_frequency'] ?? '',
			$assetRow['service_start_date'] ?? '',
			$assetRow['service_end_date'] ?? '',
			$assetRow['visits_per_interval'] ?? 1,
			$slots
		);
		foreach ($visits as $visit) {
			$plannedDt = $this->amcVisitToPlannedDatetime($visit);
			if (!$plannedDt) {
				continue;
			}
			$row = array(
				'company_id' => $companyId,
				'contract_id' => $contract_id,
				'contract_amc_asset_id' => $assetRow['contract_amc_asset_id'],
				'contract_amc_location_id' => $assetRow['contract_amc_location_id'] ?? null,
				'asset_id' => $assetRow['asset_id'],
				'planned_service_datetime' => $plannedDt,
				'actual_start_datetime' => null,
				'actual_end_datetime' => null,
				'current_status' => 0,
				'is_active' => 0,
				'created_by' => $userId,
				'created_date' => $now,
				'updated_by' => $userId,
				'updated_date' => $now,
			);
			if (($assetRow['service_mode'] ?? '') == 'inhouse') {
				$row['employee_id'] = !empty($assetRow['default_employee_id']) ? (int) $assetRow['default_employee_id'] : null;
				$row['employee_is_override'] = 0;
			} else {
				$row['technician_id'] = !empty($assetRow['default_technician_id']) ? (int) $assetRow['default_technician_id'] : null;
				$row['technician_is_override'] = 0;
			}
			$inserted[] = $callConfig->insertRecord('contract_amc_service_schedules', $row);
		}
		return $inserted;
	}
		private function buildAmcPlannedVisits($frequency, $serviceStart, $serviceEnd, $visitsPerInterval, $serviceSlots)
	{
		$freq = $this->normalizeAmcServiceOn($frequency);
		if ($freq == '') {
			return array();
		}
		$startYmd = $this->amcParseDateYmd($serviceStart);
		$endYmd = $this->amcParseDateYmd($serviceEnd);
		if (!is_array($serviceSlots)) {
			$serviceSlots = array();
		}

		if ($freq == 'custom') {
			$visits = array();
			if (count((array)$serviceSlots) > 0) {
				foreach ($serviceSlots as $slot) {
					$d = $this->amcParseDateYmd($slot) ?: trim((string) $slot);
					if ($d == '') {
						continue;
					}
					if ($startYmd && $endYmd && ($d < $startYmd || $d >= $endYmd)) {
						continue;
					}
					$visits[] = array('date' => $d, 'time' => '');
				}
			}
			usort($visits, function ($a, $b) {
				return strcmp($a['date'], $b['date']);
			});
			return $visits;
		}

		if (!$startYmd || !$endYmd || $startYmd >= $endYmd) {
			return array();
		}

		$interval = max(1, (int) $visitsPerInterval);
		if ($interval <= 1 && $freq != 'daily') {
			return array_map(function ($d) {
				return array('date' => $d, 'time' => '');
			}, $this->amcBuildPlannedVisitDatesSequential($frequency, $serviceStart, $serviceEnd));
		}
		if ($interval <= 1 && $freq == 'daily') {
			$dailyTime = !empty($serviceSlots[0])
				? $serviceSlots[0]
				: $this->amcGetDefaultSlotValue('daily', 0, 1, $startYmd);
			return array_map(function ($d) use ($dailyTime) {
				return array('date' => $d, 'time' => $dailyTime);
			}, $this->amcBuildPlannedVisitDatesSequential($frequency, $serviceStart, $serviceEnd));
		}

		$slots = count((array)$serviceSlots) ? $serviceSlots : array();
		if (count((array)$slots) == 0) {
			for ($i = 0; $i < $interval; $i++) {
				$slots[] = $this->amcGetDefaultSlotValue($freq, $i, $interval, $startYmd);
			}
		}

		$visits = array();
		$periodAnchor = $startYmd;
		while ($periodAnchor < $endYmd) {
			for ($i = 0; $i < $interval; $i++) {
				$slotVal = isset($slots[$i]) ? $slots[$i] : '';
				$visit = $this->amcComputeSlotVisit($periodAnchor, $frequency, $slotVal, $i, $interval, $startYmd);
				if ($visit['date'] >= $startYmd && $visit['date'] < $endYmd) {
					$visits[] = $visit;
				}
			}
			$nextAnchor = $this->amcGetNextPeriodAnchor($periodAnchor, $freq);
			if (!$nextAnchor || $nextAnchor == $periodAnchor) {
				break;
			}
			$periodAnchor = $nextAnchor;
			if (count((array)$visits) > 10000) {
				break;
			}
		}
		return $visits;
	}

	private function amcParseDateYmd($dateStr)
	{
		$dateStr = trim((string) $dateStr);
		if ($dateStr == '' || $dateStr == '1970-01-01') {
			return null;
		}
		if (preg_match('/^\d{4}-\d{2}-\d{2}/', $dateStr)) {
			return substr($dateStr, 0, 10);
		}
		$normalized = str_replace('/', '-', $dateStr);
		foreach (array('d-m-Y', 'm-d-Y') as $fmt) {
			$d = DateTime::createFromFormat('!' . $fmt, $normalized);
			if ($d instanceof DateTime) {
				$errors = DateTime::getLastErrors();
				if (empty($errors['warning_count']) && empty($errors['error_count'])) {
					return $d->format('Y-m-d');
				}
			}
		}
		$ts = strtotime($normalized);
		return $ts ? date('Y-m-d', $ts) : null;
	}


	private function amcVisitToPlannedDatetime(array $visit)
	{
		$date = $visit['date'] ?? '';
		$time = trim((string) ($visit['time'] ?? ''));
		if ($date == '') {
			return null;
		}
		if ($time != '') {
			if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
				$time .= ':00';
			}
			return $date . ' ' . $time;
		}
		return $date . ' 00:00:00';
	}
	private function normalizeAmcServiceOn($serviceOn)
	{
		$serviceOn = strtolower(trim((string) $serviceOn));
		$legacy = array(
			'0' => 'custom',
			'1' => 'daily',
			'2' => 'weekly',
			'3' => 'monthly',
			'4' => 'yearly',
		);
		if (isset($legacy[$serviceOn])) {
			return $legacy[$serviceOn];
		}
		return $serviceOn;
	}
		private function amcBuildPlannedVisitDatesSequential($frequency, $serviceStart, $serviceEnd)
	{
		$freq = $this->normalizeAmcServiceOn($frequency);
		if ($freq == 'custom' || $freq == '') {
			return array();
		}
		$startYmd = $this->amcParseDateYmd($serviceStart);
		$endYmd = $this->amcParseDateYmd($serviceEnd);
		if (!$startYmd || !$endYmd || $startYmd >= $endYmd) {
			return array();
		}
		$dates = array();
		$current = $startYmd;
		while ($current < $endYmd) {
			$dates[] = $current;
			$next = $this->amcAdvanceVisitDateYmd($current, $freq);
			if (!$next || $next == $current) {
				break;
			}
			$current = $next;
			if (count((array)$dates) > 10000) {
				break;
			}
		}
		return $dates;
	}
		private function amcGetDefaultSlotValue($freq, $slotIndex, $interval, $startYmd)
	{
		$f = $this->normalizeAmcServiceOn($freq);
		if ($f == 'daily') {
			$hour = min(9 + $slotIndex * 3, 21);
			return str_pad((string) $hour, 2, '0', STR_PAD_LEFT) . ':00';
		}
		if ($f == 'weekly') {
			$startDay = 1;
			if ($startYmd) {
				$d = DateTime::createFromFormat('Y-m-d', $startYmd);
				$startDay = $d ? (int) $d->format('w') : 1;
			}
			return (string) (($startDay + $slotIndex) % 7);
		}
		$startDay = 1;
		if ($startYmd) {
			$parts = explode('-', $startYmd);
			$startDay = isset($parts[2]) ? (int) $parts[2] : 1;
		}
		if ($f == 'monthly_twice') {
			if ($slotIndex == 0) {
				return (string) $startDay;
			}
			return (string) min(28, $startDay + 15);
		}
		if ($interval <= 1) {
			return (string) $startDay;
		}
		$step = max(1, (int) floor(28 / $interval));
		return (string) min(28, $startDay + $slotIndex * $step);
	}
	private function amcComputeSlotVisit($periodAnchor, $frequency, $slotValue, $slotIndex, $interval, $contractStartYmd)
	{
		$freq = $this->normalizeAmcServiceOn($frequency);
		$slot = trim((string) $slotValue);
		if ($freq == 'daily') {
			$time = $slot != '' ? $slot : $this->amcGetDefaultSlotValue($freq, $slotIndex, $interval, $contractStartYmd);
			return array('date' => $periodAnchor, 'time' => $time);
		}
		if ($freq == 'weekly') {
			$weekStart = DateTime::createFromFormat('Y-m-d', $periodAnchor);
			$startDow = (int) $weekStart->format('w');
			$targetDow = is_numeric($slot) ? (int) $slot : (($startDow + $slotIndex) % 7);
			$delta = $targetDow - $startDow;
			if ($delta < 0) {
				$delta += 7;
			}
			$visitDate = clone $weekStart;
			$visitDate->modify('+' . $delta . ' days');
			return array('date' => $this->amcFormatDateYmd($visitDate), 'time' => '');
		}
		$day = is_numeric($slot) ? (int) $slot : (int) $this->amcGetDefaultSlotValue($freq, $slotIndex, $interval, $contractStartYmd);
		$day = min(31, max(1, $day));
		$parts = explode('-', $periodAnchor);
		$y = (int) $parts[0];
		$m = (int) $parts[1];
		$dim = (int) date('t', mktime(0, 0, 0, $m, 1, $y));
		$day = min($day, $dim);
		return array(
			'date' => sprintf('%04d-%02d-%02d', $y, $m, $day),
			'time' => '',
		);
	}
	private function amcGetNextPeriodAnchor($currentYmd, $frequency)
	{
		return $this->amcAdvanceVisitDateYmd($currentYmd, $frequency);
	}
	private function amcAdvanceVisitDateYmd($currentYmd, $frequency)
	{
		$freq = $this->normalizeAmcServiceOn($frequency);
		$base = DateTime::createFromFormat('Y-m-d', $currentYmd);
		if (!$base) {
			return null;
		}
		switch ($freq) {
			case 'daily':
				$base->modify('+1 day');
				return $this->amcFormatDateYmd($base);
			case 'weekly':
				$base->modify('+7 days');
				return $this->amcFormatDateYmd($base);
			case 'monthly':
				return $this->amcAddMonthsSafeYmd($currentYmd, 1);
			case 'monthly_twice':
				$base->modify('+15 days');
				return $this->amcFormatDateYmd($base);
			case 'quarterly':
				return $this->amcAddMonthsSafeYmd($currentYmd, 3);
			case 'halfyearly':
				return $this->amcAddMonthsSafeYmd($currentYmd, 6);
			case 'yearly':
				$base->modify('+1 year');
				return $this->amcFormatDateYmd($base);
			default:
				return null;
		}
	}
	private function amcFormatDateYmd(DateTime $d)
	{
		return $d->format('Y-m-d');
	}
	private function amcAddMonthsSafeYmd($ymd, $months)
	{
		$d = DateTime::createFromFormat('Y-m-d', $ymd);
		if (!$d) {
			return null;
		}
		$day = (int) $d->format('d');
		$d->modify('+' . (int) $months . ' month');
		if ((int) $d->format('d') < $day) {
			$d->modify('last day of previous month');
		}
		return $this->amcFormatDateYmd($d);
	}
	function getAmcContractAssetSchedules($get)
	{
		global $callConfig;
		$return = array('schedules' => array(), 'asset' => array());
		$companyId = (int) ($get['company_id'] ?? 0);
		$contractId = (int) ($get['contract_id'] ?? 0);
		$assetRowId = (int) ($get['contract_amc_asset_id'] ?? 0);
		if ($companyId <= 0 || $contractId <= 0 || $assetRowId <= 0) {
			return $return;
		}

		$assetSql = "SELECT ca.*, a.asset_code FROM contract_amc_assets ca
			LEFT JOIN assets a ON a.id = ca.asset_id AND a.company_id = ca.company_id AND a.is_active = 0
			WHERE ca.company_id = {$companyId} AND ca.contract_id = '" . addslashes($contractId) . "'
			AND ca.id = '" . addslashes($assetRowId) . "' AND ca.is_active = 0 LIMIT 1";
		$assetRow = $callConfig->getRow($assetSql);
		$return['asset_sql'] = $assetSql;
		if ($assetRow) {
			$return['asset'] = is_array($assetRow) ? $assetRow : (array) $assetRow;
		}

		$sql = "SELECT * FROM contract_amc_service_schedules
			WHERE company_id = {$companyId} AND contract_id = '" . addslashes($contractId) . "'
			AND contract_amc_asset_id = '" . addslashes($assetRowId) . "' AND is_active = 0
			ORDER BY planned_service_datetime ASC";
		$rows = $callConfig->getAllRows($sql);
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$r = is_array($row) ? $row : (array) $row;
				$r['planned_display'] = $this->formatAmcScheduleDateTime($r['planned_service_datetime'] ?? '');
				$r['actual_start_display'] = !empty($r['actual_start_datetime']) ? $this->formatAmcScheduleDateTime($r['actual_start_datetime']) : '—';
				$r['actual_end_display'] = !empty($r['actual_end_datetime']) ? $this->formatAmcScheduleDateTime($r['actual_end_datetime']) : '—';
				$return['schedules'][] = $r;
			}
		}
		return $return;
	}
	private function formatAmcScheduleDateTime($datetime)
	{
		$datetime = trim((string) $datetime);
		if ($datetime == '') {
			return '—';
		}
		$ts = strtotime($datetime);
		if ($ts == false) {
			return $datetime;
		}
		$sessionFormat = $_SESSION['date_formate'] ?? '';
		$dateFormat = ($sessionFormat != '' && $sessionFormat != '0') ? $sessionFormat : 'd-m-Y';
		$datePart = date($dateFormat, $ts);
		if ($datePart == '' || $datePart == false) {
			$datePart = date('d-m-Y', $ts);
		}
		$hasTime = date('H:i', $ts) != '00:00';
		if ($hasTime) {
			return $datePart . ' ' . date('h:i A', $ts);
		}
		return $datePart;
	}
	function getAmcContractAssetCalendarEvents($get)
	{
		global $callConfig;
		$return = array('events' => array());
		$data = $this->getAmcContractAssetSchedules($get);
		$freq = '';
		if (!empty($data['asset']['service_frequency'])) {
			$freq = $this->normalizeAmcServiceOn($data['asset']['service_frequency']);
		}
		foreach ($data['schedules'] as $row) {
			$planned = $row['planned_service_datetime'] ?? '';
			if ($planned === '') {
				continue;
			}
			$allDay = $this->amcPlannedDatetimeIsAllDay($planned);
			$event = array(
				'title' => ($freq === 'daily' && !$allDay) ? 'Scheduled Visit' : 'Scheduled Visit',
				'start' => $allDay ? substr($planned, 0, 10) : str_replace(' ', 'T', $planned),
				'allDay' => $allDay,
				'color' => '#17a2b8',
			);
			if ($freq === 'daily' && !$allDay) {
				$event['title'] = 'Scheduled Visit';
			}
			$return['events'][] = $event;
		}
		return $return;
	}
	private function amcPlannedDatetimeIsAllDay($datetime)
	{
		return preg_match('/\s00:00:00$/', (string) $datetime) == 1;
	}
	function getContractAmcDashboardData($get)
	{
		global $callConfig;
		$return = array();
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($companyId <= 0) {
			return $return;
		}
		$where = $this->buildContractAmcDashboardWhere($get);
		$join = $this->buildContractAmcDashboardJoin($get);

		$today = date('Y-m-d');
		$next7 = date('Y-m-d', strtotime('+7 days'));

		$categories = array(
			'today_visits' => array(
				'filter' => "DATE(s.planned_service_datetime) = '{$today}'",
				'list_key' => 'today_list',
				'order' => 's.planned_service_datetime ASC',
			),
			'next_7_days' => array(
				'filter' => "s.current_status = 0 AND DATE(s.planned_service_datetime) BETWEEN '{$today}' AND '{$next7}'",
				'list_key' => 'next_7_days_list',
				'order' => 's.planned_service_datetime ASC',
			),
			'overdue' => array(
				'filter' => "s.current_status = 0 AND s.planned_service_datetime < NOW()",
				'list_key' => 'overdue_list',
				'order' => 's.planned_service_datetime ASC',
			),
			'in_progress' => array(
				'filter' => "s.actual_start_datetime IS NOT NULL AND s.current_status < 199",
				'list_key' => 'in_progress_list',
				'order' => 's.actual_start_datetime DESC',
			),
			'completed_today' => array(
				'filter' => "s.current_status = 199 AND DATE(COALESCE(s.actual_end_datetime, s.planned_service_datetime)) = '{$today}'",
				'list_key' => 'completed_today_list',
				'order' => 'COALESCE(s.actual_end_datetime, s.planned_service_datetime) DESC',
			),
		);

		$listSelect = "SELECT s.id, s.planned_service_datetime, s.current_status, s.actual_start_datetime, s.actual_end_datetime,
			c.id AS contract_id, c.contract_name, c.transaction_no
			FROM contract_amc_service_schedules s {$join}
			WHERE {$where} AND ";

		foreach ($categories as $countKey => $cfg) {
			$countSql = "SELECT COUNT(*) AS c FROM contract_amc_service_schedules s {$join} WHERE {$where} AND {$cfg['filter']}";
			$row = $callConfig->getRow($countSql);
			$return[$countKey] = (int) ($row->c ?? 0);
			$listSql = $listSelect . $cfg['filter'] . " ORDER BY {$cfg['order']} LIMIT 50";
			$return[$cfg['list_key']] = $callConfig->getAllRows($listSql);
		}

		return $return;
	}
	function buildContractAmcFmDashboardJoin()
	{
		return "INNER JOIN contracts c ON c.id = s.contract_id AND c.company_id = s.company_id AND c.is_active = 0
			AND c.is_amc_contract = 'Y' AND c.party_type = 'vendor' AND c.current_status = '" . ST_APPROVED . "'
			INNER JOIN contract_amc_details cad ON cad.contract_id = c.id AND cad.company_id = c.company_id AND cad.is_active = 0
			INNER JOIN vendors v ON v.id = c.party_id AND v.company_id = c.company_id AND v.is_active = 0
			LEFT JOIN contract_amc_assets ca ON ca.id = s.contract_amc_asset_id AND ca.company_id = s.company_id AND ca.is_active = 0
			LEFT JOIN assets a ON a.id = s.asset_id AND a.company_id = s.company_id AND a.is_active = 0";
	}

	function buildContractAmcFmDashboardWhere($get)
	{
		$companyId = (int) ($get['company_id'] ?? 0);
		$userId = (int) ($get['user_id'] ?? 0);
		$where = "s.company_id = {$companyId} AND s.is_active = 0 AND cad.customer_connect_id IS NOT NULL AND cad.customer_connect_id > 0";
		if ($userId <= 0) {
			$where .= " AND 1=0";
			return $where;
		}
		$where .= " AND cad.customer_connect_id = {$userId}";
		return $where;
	}
	function getPartyContractsList($get, $party_type, $party_id)
	{
		global $callConfig;
		$return = array();
		$return['active_contracts'] = array();
		$return['inactive_contracts'] = array();
		$return['active_count'] = 0;
		$return['inactive_count'] = 0;

		if ($party_id == '') {
			return $return;
		}

		$today = date('Y-m-d');
		$contractssql = "SELECT id, contract_name, transaction_no, start_date, end_date, current_status, cancel_status, is_amc_contract
			FROM contracts
			WHERE company_id = '" . $get['company_id'] . "'
			AND party_type = '" . addslashes($party_type) . "'
			AND party_id = '" . addslashes($party_id) . "'
			AND is_active = 0
			ORDER BY id DESC";
		$contractsres = $callConfig->getAllRows($contractssql);
		$contractIds = array();
		if (count((array)$contractsres) > 0) {
			foreach ($contractsres as $contractrow) {
				$row = (array) $contractrow;
				$contractIds[] = $row['id'];
			}
		}
		$rollups = $this->getContractAmcScheduleRollups($get['company_id'], $contractIds);

		if (count((array)$contractsres) > 0) {
			foreach ($contractsres as $contractrow) {
				$row = (array) $contractrow;
				$cancelled = ($row['cancel_status'] ?? '') == 'Y';
				$end_date = $row['end_date'] ?? '';
				$is_active_contract = !$cancelled && ($end_date == '' || $end_date >= $today);
				$merged = $this->mergeContractRollupsIntoRows(array($row), $rollups);
				$row = $merged[0];

				if ($is_active_contract) {
					$return['active_contracts'][] = $row;
				} else {
					$return['inactive_contracts'][] = $row;
				}
			}
		}

		$return['active_count'] = count((array)$return['active_contracts']);
		$return['inactive_count'] = count((array)$return['inactive_contracts']);

		$status = strtolower($get['status'] ?? '');
		if ($status == 'active') {
			$return['contracts'] = $return['active_contracts'];
		} elseif ($status == 'inactive') {
			$return['contracts'] = $return['inactive_contracts'];
		}

		return $return;
	}
	function getVendorContracts($get)
	{
		return $this->getPartyContractsList($get, 'vendor', $get['vendor_id'] ?? '');
	}
	function getContractAmcScheduleRollups($company_id, $contract_ids)
	{
		global $callConfig;
		$rollups = array();
		$ids = array();
		if (count((array)$contract_ids) > 0) {
			foreach ($contract_ids as $cid) {
				$cid = (int) $cid;
				if ($cid > 0) {
					$ids[] = $cid;
				}
			}
		}
		if (count((array)$ids) == 0) {
			return $rollups;
		}
		$idList = implode(',', $ids);
		$sql = "SELECT contract_id,
			MIN(CASE WHEN current_status = 0 AND planned_service_datetime >= CURDATE()
				THEN planned_service_datetime END) AS next_visit,
			SUM(CASE WHEN current_status = 0 AND planned_service_datetime < NOW()
				THEN 1 ELSE 0 END) AS overdue_count,
			MAX(CASE WHEN current_status = 199
				THEN COALESCE(actual_end_datetime, planned_service_datetime) END) AS last_completed,
			SUM(CASE WHEN current_status = 0 THEN 1 ELSE 0 END) AS pending_count
			FROM contract_amc_service_schedules
			WHERE company_id = " . (int) $company_id . " AND is_active = 0
			AND contract_id IN ({$idList})
			GROUP BY contract_id";
		$rows = $callConfig->getAllRows($sql);
		if (count((array)$rows) > 0) {
			foreach ($rows as $row) {
				$r = is_array($row) ? $row : (array) $row;
				$rollups[$r['contract_id']] = $r;
			}
		}
		return $rollups;
	}
	function mergeContractRollupsIntoRows($rows, $rollups)
	{
		if (count((array)$rows) == 0) {
			return $rows;
		}
		$merged = array();
		foreach ($rows as $contractrow) {
			$row = is_array($contractrow) ? $contractrow : (array) $contractrow;
			$cid = $row['id'] ?? '';
			if (($row['is_amc_contract'] ?? '') == 'Y' && isset($rollups[$cid])) {
				$ru = $rollups[$cid];
				$row['next_visit'] = $ru['next_visit'] ?? '';
				$row['overdue_count'] = (int) ($ru['overdue_count'] ?? 0);
				$row['last_completed'] = $ru['last_completed'] ?? '';
				$row['pending_count'] = (int) ($ru['pending_count'] ?? 0);
			} else {
				$row['next_visit'] = '';
				$row['overdue_count'] = 0;
				$row['last_completed'] = '';
				$row['pending_count'] = 0;
			}
			$merged[] = $row;
		}
		return $merged;
	}

	function insertContracts($get,$post){
		global $callConfig;
    	$return =array();
    	$return['post'] = $post;
	   $insert_array = array('contract_name'=>$post['contract_name'],'transaction_no'=>$post['transaction_no'],'party_id'=> $post['party_id'],'start_date'=>$post['start_date'],'end_date'=>$post['end_date'],'description'=>$post['description'],'additional_notes'=>$post['additional_notes'],'payment_term_id'=>$post['payment_term_id'],'termination_condition'=>$post['termination_condition'],'contract_status'=>$post['contract_status'],'rt_number'=>$post['rt_number']);
	    if($post['Rid']!=""){ 
    		$insert_array['updated_by']=$get['user_id'];
    		$insert_array['updated_date']=date('Y-m-d H:i:s');
    		$res=$callConfig->updateRecord('contracts',$insert_array,'id',$post['Rid']);
    		$return['result'] = $res;
    	}else{
    		$insert_array['company_id']=$get['company_id'];
    		$insert_array['is_active']=0;
    		$insert_array['created_by']=$get['user_id'];
    		$insert_array['created_date']=date('Y-m-d H:i:s');
    		$insert_data=$callConfig->insertRecord('contracts',$insert_array);
    		$return['result'] = $insert_data;
		}
		return $return;
	}
	private function mergeAmcServiceFrequencyOptions($dbRows)
	{
		$canonical = array(
			'daily'      => 'Daily',
			'weekly'     => 'Weekly',
			'monthly'    => 'Monthly',
			'quarterly'  => 'Quarterly',
			'halfyearly' => 'Halfyearly',
			'yearly'     => 'Yearly',
			'custom'     => 'Custom',
		);
		$legacyToSlug = array(
			'0' => 'custom',
			'1' => 'daily',
			'2' => 'weekly',
			'3' => 'monthly',
			'4' => 'yearly',
		);
		$merged = array();
		if (count((array)$dbRows) > 0) {
			foreach ($dbRows as $row) {
				$row = is_array($row) ? $row : json_decode(json_encode($row), true);
				$code = strtolower(trim((string)($row['genc_detail_code'] ?? '')));
				if ($code == '') {
					continue;
				}
				if (isset($legacyToSlug[$code])) {
					$code = $legacyToSlug[$code];
				}
				$name = $row['genc_detail_name'] ?? ($canonical[$code] ?? $code);
				$merged[$code] = array(
					'genc_detail_code' => $code,
					'genc_detail_name' => $name,
				);
			}
		}
		foreach ($canonical as $code => $name) {
			if (!isset($merged[$code])) {
				$merged[$code] = array(
					'genc_detail_code' => $code,
					'genc_detail_name' => $name,
				);
			}
		}
		$list = array_values($merged);
		if (count((array)$list) > 0) {
			usort($list, function ($a, $b) {
				$nameA = strtolower($a['genc_detail_name'] ?? '');
				$nameB = strtolower($b['genc_detail_name'] ?? '');
				if ($nameA == $nameB) {
					return 0;
				}
				return ($nameA < $nameB) ? -1 : 1;
			});
		}
		return $list;
	}

	private function vendorAmcTrackerFiscalMonthOrder()
	{
		return array('04', '05', '06', '07', '08', '09', '10', '11', '12', '01', '02', '03');
	}

	private function vendorAmcTrackerPeriodLabel($fiscalStartYear)
	{
		$fiscalStartYear = (int) $fiscalStartYear;
		if ($fiscalStartYear <= 0) {
			return '';
		}
		return 'April - ' . $fiscalStartYear . ' To March - ' . ($fiscalStartYear + 1);
	}

	function getCustomerContracts($get)
	{
		return $this->getPartyContractsList($get, 'customer', $get['customer_id'] ?? '');
	}
	private function sanitizeRowsForJson($rows)
	{
		if (empty($rows) || !is_array($rows)) {
			return array();
		}
		$clean = array();
		foreach ($rows as $row) {
			$arr = is_array($row) ? $row : (array) $row;
			foreach ($arr as $key => $value) {
				if (is_string($value)) {
					$arr[$key] = iconv('UTF-8', 'UTF-8//IGNORE', $value);
				}
			}
			$clean[] = $arr;
		}
		return $clean;
	}

	function getAmcScheduleStatusLabel($status)
	{
		$status = (int) $status;
		if ($status > 0 && $status < 199) {
			return array('label' => 'In Progress', 'class' => 'bg-warning');
		}
		if ($status >= 199) {
			return array('label' => 'Completed', 'class' => 'bg-success');
		}
		return array('label' => 'Pending', 'class' => 'bg-primary');
	}

	function getContractAmcScheduleDrilldown($get)
	{
		global $callConfig;
		$return = array('contract' => array(), 'locations' => array());
		$companyId = (int) ($get['company_id'] ?? 0);
		$contractId = (int) ($get['contract_id'] ?? 0);
		if ($companyId <= 0 || $contractId <= 0) {
			return $return;
		}

		$contractSql = "SELECT c.*,
			cad.service_mode, cad.department_id, cad.default_employee_id,
			CASE WHEN c.party_type = 'customer' THEN cu.display_name WHEN c.party_type = 'vendor' THEN v.display_name ELSE '' END AS party_name
			FROM contracts c
			LEFT JOIN contract_amc_details cad ON cad.contract_id = c.id AND cad.company_id = c.company_id AND cad.is_active = 0
			LEFT JOIN customers cu ON cu.id = c.party_id AND c.party_type = 'customer' AND cu.company_id = c.company_id AND cu.is_active = 0
			LEFT JOIN vendors v ON v.id = c.party_id AND c.party_type = 'vendor' AND v.company_id = c.company_id AND v.is_active = 0
			WHERE c.company_id = {$companyId} AND c.id = '" . addslashes($contractId) . "' AND c.is_active = 0 LIMIT 1";
		$contractRow = $callConfig->getRow($contractSql);
		if ($contractRow) {
			$return['contract'] = is_array($contractRow) ? $contractRow : (array) $contractRow;
		}

		$locSql = "SELECT cal.*, l.location_name
			FROM contract_amc_locations cal
			LEFT JOIN locations l ON l.id = cal.location_id AND l.company_id = cal.company_id AND l.is_active = 0
			WHERE cal.company_id = {$companyId} AND cal.contract_id = '" . addslashes($contractId) . "' AND cal.is_active = 0
			ORDER BY cal.id ASC";
		$locRows = $callConfig->getAllRows($locSql);

		$assetSql = "SELECT ca.*, a.asset_code, ac.category_description AS category_name
			FROM contract_amc_assets ca
			LEFT JOIN assets a ON a.id = ca.asset_id AND a.company_id = ca.company_id AND a.is_active = 0
			LEFT JOIN asset_categories ac ON ac.id = a.category_id AND ac.company_id = ca.company_id AND ac.is_active = 0
			WHERE ca.company_id = {$companyId} AND ca.contract_id = '" . addslashes($contractId) . "' AND ca.is_active = 0
			ORDER BY ca.id ASC";
		$assetRows = $callConfig->getAllRows($assetSql);
		$assetsByLoc = array();
		$assetDefaults = array();
		$assetEmployeeDefaults = array();
		if (count((array)$assetRows) > 0) {
			foreach ($assetRows as $ar) {
				$a = is_array($ar) ? $ar : (array) $ar;
				$locKey = $a['contract_amc_location_id'] ?? 0;
				if (!isset($assetsByLoc[$locKey])) {
					$assetsByLoc[$locKey] = array();
				}
				$assetsByLoc[$locKey][] = $a;
				$assetDefaults[$a['id'] ?? 0] = (int) ($a['default_technician_id'] ?? 0);
				$assetEmployeeDefaults[$a['id'] ?? 0] = (int) ($a['default_employee_id'] ?? 0);
			}
		}

		$serviceMode = $this->resolveContractAmcServiceMode($return['contract']);
		$techNameMap = $this->getContractAmcTechnicians($companyId);
		$empNameMap = $this->getContractAmcEmployees($companyId, (int) ($return['contract']['department_id'] ?? 0));

		$schedSql = "SELECT s.*
			FROM contract_amc_service_schedules s
			WHERE s.company_id = {$companyId} AND s.contract_id = '" . addslashes($contractId) . "' AND s.is_active = 0
			ORDER BY s.planned_service_datetime ASC";
		$schedRows = $callConfig->getAllRows($schedSql);
		$schedByAsset = array();
		if (count((array)$schedRows) > 0) {
			foreach ($schedRows as $sr) {
				$s = is_array($sr) ? $sr : (array) $sr;
				$assetKey = $s['contract_amc_asset_id'] ?? 0;
				if (!isset($schedByAsset[$assetKey])) {
					$schedByAsset[$assetKey] = array();
				}
				$s['planned_display'] = $this->formatAmcScheduleDateTime($s['planned_service_datetime'] ?? '');
				$s['actual_start_display'] = !empty($s['actual_start_datetime']) ? $this->formatAmcScheduleDateTime($s['actual_start_datetime']) : '—';
				$s['actual_end_display'] = !empty($s['actual_end_datetime']) ? $this->formatAmcScheduleDateTime($s['actual_end_datetime']) : '—';
				$st = $this->getAmcScheduleStatusLabel($s['current_status'] ?? 0);
				$s['status_label'] = $st['label'];
				$s['status_class'] = $st['class'];
				if ($serviceMode == 'inhouse') {
					$assetDefaultEmpId = $assetEmployeeDefaults[$assetKey] ?? 0;
					$effective = $this->resolveEffectiveAmcEmployee($s, $assetDefaultEmpId, $empNameMap);
					$s['effective_employee_id'] = $effective['employee_id'];
					$s['effective_employee_name'] = $effective['employee_name'];
					$s['effective_assignee_name'] = $effective['employee_name'];
					$s['employee_is_override'] = $effective['is_override'];
				} else {
					$assetDefaultId = $assetDefaults[$assetKey] ?? 0;
					$effective = $this->resolveEffectiveAmcTechnician($s, $assetDefaultId, $techNameMap);
					$s['effective_technician_id'] = $effective['technician_id'];
					$s['effective_technician_name'] = $effective['technician_name'];
					$s['effective_assignee_name'] = $effective['technician_name'];
					$s['technician_name'] = $effective['technician_name'];
					$s['technician_is_override'] = $effective['is_override'];
				}
				$repairBundle = $this->getRepairOrderBundleForSchedule($companyId, (int) ($s['id'] ?? 0), $s);
				$s['repair_eligibility'] = $repairBundle['repair_eligibility'];
				$s['has_repair_order'] = !empty($repairBundle['repair_order']['id']);
				$s['repair_order_id'] = (int) ($repairBundle['repair_order']['id'] ?? 0);
				$addedAmount = 0;
				$repairLines = $repairBundle['repair_order_lines'] ?? array();
				if (count((array)$repairLines) > 0) {
					foreach ($repairLines as $repairLine) {
						$repairLine = is_array($repairLine) ? $repairLine : (array) $repairLine;
						$addedAmount += (float) ($repairLine['amount'] ?? 0);
					}
				}
				$s['added_amount'] = $addedAmount;
				$schedByAsset[$assetKey][] = $s;
			}
		}

		$assetIds = array();
		if (count((array)$assetRows) > 0) {
			foreach ($assetRows as $ar) {
				$a = is_array($ar) ? $ar : (array) $ar;
				$assetIds[] = $a['id'];
			}
		}
		$assetRollups = array();
		if (count((array)$assetIds) > 0) {
			$idList = implode(',', array_map('intval', $assetIds));
			$rollupSql = "SELECT contract_amc_asset_id,
				MIN(CASE WHEN current_status = 0 AND planned_service_datetime >= CURDATE() THEN planned_service_datetime END) AS next_visit,
				SUM(CASE WHEN current_status = 0 AND planned_service_datetime < NOW() THEN 1 ELSE 0 END) AS overdue_count,
				MAX(CASE WHEN current_status = 199 THEN COALESCE(actual_end_datetime, planned_service_datetime) END) AS last_completed,
				SUM(CASE WHEN current_status = 0 THEN 1 ELSE 0 END) AS pending_count
				FROM contract_amc_service_schedules
				WHERE company_id = {$companyId} AND is_active = 0 AND contract_amc_asset_id IN ({$idList})
				GROUP BY contract_amc_asset_id";
			$rollupRows = $callConfig->getAllRows($rollupSql);
			if (count((array)$rollupRows) > 0) {
				foreach ($rollupRows as $rr) {
					$r = is_array($rr) ? $rr : (array) $rr;
					$assetRollups[$r['contract_amc_asset_id']] = $r;
				}
			}
		}

		if (count((array)$locRows) > 0) {
			foreach ($locRows as $lr) {
				$loc = is_array($lr) ? $lr : (array) $lr;
				$locId = $loc['id'] ?? 0;
				$loc['assets'] = array();
				$locAssets = $assetsByLoc[$locId] ?? array();
				if (count((array)$locAssets) > 0) {
					foreach ($locAssets as $asset) {
						$aid = $asset['id'] ?? 0;
						$asset['schedules'] = $schedByAsset[$aid] ?? array();
						$asset['rollup'] = $assetRollups[$aid] ?? array();
						$loc['assets'][] = $asset;
					}
				}
				$return['locations'][] = $loc;
			}
		}

		$return['can_execute'] = $this->canExecuteContractAmcService($get, $return['contract']);
		$return['contract_is_approved'] = $this->isContractAmcApprovedForExecution($get, $return['contract']);
		$return['service_mode'] = $this->resolveContractAmcServiceMode($return['contract']);
		return $return;
	}

	function resolveContractAmcCurrentStatus($get, $contract)
	{
		$contract = (array) $contract;
		$currentStatus = $contract['current_status'] ?? null;
		if ($currentStatus !== null && $currentStatus !== '') {
			return $currentStatus;
		}

		$contractId = (int) ($contract['contract_id'] ?? $contract['id'] ?? 0);
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($contractId <= 0 || $companyId <= 0) {
			return '';
		}

		global $callConfig;
		$row = $callConfig->getRow(
			"SELECT current_status FROM contracts WHERE id = {$contractId} AND company_id = '"
			. addslashes((string) $companyId) . "' AND is_active = 0 LIMIT 1"
		);
		if (!$row) {
			return '';
		}
		return is_array($row) ? ($row['current_status'] ?? '') : ($row->current_status ?? '');
	}

	function isContractAmcApprovedForExecution($get, $contract)
	{
		return $this->resolveContractAmcCurrentStatus($get, $contract) == ST_APPROVED;
	}

	function canExecuteContractAmcService($get, $contract)
	{
		if (!$this->isContractAmcApprovedForExecution($get, $contract)) {
			return false;
		}

		$role = strtoupper($get['working_role_short_code'] ?? '');
		if (($get['admin_type'] ?? '') == '1' || $role == 'CONTRACT_ADMIN') {
			return true;
		}
		$contract = (array) $contract;
		$contractId = (int) ($contract['id'] ?? $contract['contract_id'] ?? 0);
		$serviceMode = $contract['service_mode'] ?? '';
		if ($serviceMode == '' && $contractId > 0) {
			$serviceMode = $this->getContractAmcServiceMode($get['company_id'] ?? 0, $contractId);
		}
		if ($serviceMode == 'inhouse') {
			return true;
		}
		if (($contract['party_type'] ?? '') != 'vendor') {
			return false;
		}
		$partyId = (int) ($contract['party_id'] ?? 0);
		if ($partyId <= 0) {
			return false;
		}
		global $callConfig;
		$vendorSql = "SELECT user_id FROM vendors WHERE id = {$partyId} AND company_id = '" . addslashes($get['company_id']) . "' AND is_active = 0 LIMIT 1";
		$vendorRow = $callConfig->getRow($vendorSql);
		if ($vendorRow && (int) ($vendorRow->user_id ?? 0) == (int) ($get['user_id'] ?? 0)) {
			return true;
		}
		return false;
	}

	function resolveContractAmcServiceMode($contractRow)
	{
		$row = is_object($contractRow) ? (array) $contractRow : (array) $contractRow;
		if (($row['service_mode'] ?? '') == 'inhouse') {
			return 'inhouse';
		}
		if (($row['party_type'] ?? '') == 'inhouse') {
			return 'inhouse';
		}
		return 'vendor';
	}

	function getContractAmcServiceMode($companyId, $contractId)
	{
		global $callConfig;
		$companyId = (int) $companyId;
		$contractId = (int) $contractId;
		if ($companyId <= 0 || $contractId <= 0) {
			return 'vendor';
		}
		$sql = "SELECT cad.service_mode, c.party_type
			FROM contracts c
			LEFT JOIN contract_amc_details cad ON cad.contract_id = c.id AND cad.company_id = c.company_id AND cad.is_active = 0
			WHERE c.company_id = {$companyId} AND c.id = '" . addslashes($contractId) . "' AND c.is_active = 0
			ORDER BY cad.id DESC LIMIT 1";
		$row = $callConfig->getRow($sql);
		if (!$row) {
			return 'vendor';
		}
		$r = is_array($row) ? $row : (array) $row;
		return $this->resolveContractAmcServiceMode($r);
	}

	function getContractAmcEmployees($companyId, $departmentId = 0)
	{
		global $callConfig;
		$list = array();
		$companyId = (int) $companyId;
		$departmentId = (int) $departmentId;
		if ($companyId <= 0) {
			return $list;
		}
		$sql = "SELECT id, display_name, emp_code FROM employees
			WHERE company_id = '" . addslashes($companyId) . "' AND is_active = 0";
		if ($departmentId > 0) {
			$sql .= " AND department_id = {$departmentId}";
		}
		$sql .= " ORDER BY display_name ASC";
		$rows = $callConfig->getAllRows($sql);
		if (count((array)$rows) > 0) {
			foreach ($rows as $row) {
				$r = is_array($row) ? $row : (array) $row;
				$label = trim(($r['display_name'] ?? '') . (!empty($r['emp_code']) ? ' (' . $r['emp_code'] . ')' : ''));
				$list[$r['id']] = $label != '' ? $label : ('Employee #' . $r['id']);
			}
		}
		return $list;
	}

	function getAmcAssetsByLocationCategory($get)
	{
		global $callConfig;
		$return = array('assets' => array());
		$companyId = (int) ($get['company_id'] ?? 0);
		$locationId = (int) ($get['location_id'] ?? 0);
		$categoryId = (int) ($get['category_id'] ?? 0);
		if ($companyId <= 0 || $locationId <= 0 || $categoryId <= 0) {
			return $return;
		}
		$sql = "SELECT a.id, a.asset_code, a.asset_description, a.category_id, a.asset_location,
			ac.category_description AS category_name
			FROM assets a
			LEFT JOIN asset_categories ac ON ac.id = a.category_id AND ac.company_id = a.company_id AND ac.is_active = 0
			WHERE a.company_id = {$companyId} AND a.is_active = 0
			AND a.asset_location = '" . addslashes($locationId) . "'
			AND a.category_id = '" . addslashes($categoryId) . "'
			ORDER BY a.asset_code ASC";
		$rows = $callConfig->getAllRows($sql);
		if (count((array)$rows) > 0) {
			foreach ($rows as $row) {
				$return['assets'][] = is_array($row) ? $row : (array) $row;
			}
		}
		return $return;
	}

	function getContractAmcTechnicians($company_id)
	{
		global $callConfig;
		$list = array();
		$sql = "SELECT id, account_name FROM amc_account_details WHERE company_id = '" . addslashes($company_id) . "' AND is_active = 0 ORDER BY account_name ASC";
		$rows = $callConfig->getAllRows($sql);
		if (count((array)$rows) > 0) {
			foreach ($rows as $row) {
				$r = is_array($row) ? $row : (array) $row;
				$list[$r['id']] = $r['account_name'];
			}
		}
		return $list;
	}

	function getContractAmcAssetDefaultTechnicianId($companyId, $contractAmcAssetId)
	{
		global $callConfig;
		$contractAmcAssetId = (int) $contractAmcAssetId;
		$companyId = (int) $companyId;
		if ($contractAmcAssetId <= 0 || $companyId <= 0) {
			return 0;
		}
		$sql = "SELECT default_technician_id FROM contract_amc_assets
			WHERE id = {$contractAmcAssetId} AND company_id = {$companyId} AND is_active = 0 LIMIT 1";
		$row = $callConfig->getRow($sql);
		if (!$row) {
			return 0;
		}
		$r = is_array($row) ? $row : (array) $row;
		return (int) ($r['default_technician_id'] ?? 0);
	}

	function resolveEffectiveAmcTechnician($scheduleRow, $assetDefaultId, $techNameMap = array())
	{
		$schedule = (array) $scheduleRow;
		$isOverride = (int) ($schedule['technician_is_override'] ?? 0) == 1;
		$scheduleTechId = (int) ($schedule['technician_id'] ?? 0);
		$assetDefaultId = (int) $assetDefaultId;
		$effectiveId = 0;

		if ($isOverride) {
			$effectiveId = $scheduleTechId;
		} elseif ($scheduleTechId > 0) {
			$effectiveId = $scheduleTechId;
		} else {
			$effectiveId = $assetDefaultId;
		}

		$name = '';
		if ($effectiveId > 0) {
			if (count((array)$techNameMap) > 0 && isset($techNameMap[$effectiveId])) {
				$name = $techNameMap[$effectiveId];
			} elseif (!empty($schedule['technician_name']) && !$isOverride && $scheduleTechId == $effectiveId) {
				$name = $schedule['technician_name'];
			} elseif (!empty($schedule['default_technician_name']) && $effectiveId == $assetDefaultId) {
				$name = $schedule['default_technician_name'];
			}
		}

		return array(
			'technician_id' => $effectiveId,
			'technician_name' => $name,
			'is_override' => $isOverride ? 1 : 0,
		);
	}

	function resolveAmcScheduleTechnicianOverrideFields($technicianId, $assetDefaultId, $resetToDefault = false)
	{
		$technicianId = (int) $technicianId;
		$assetDefaultId = (int) $assetDefaultId;
		if ($resetToDefault) {
			return array(
				'technician_id' => $assetDefaultId > 0 ? $assetDefaultId : null,
				'technician_is_override' => 0,
			);
		}
		if ($technicianId == $assetDefaultId || ($technicianId <= 0 && $assetDefaultId <= 0)) {
			return array(
				'technician_id' => $technicianId > 0 ? $technicianId : null,
				'technician_is_override' => 0,
			);
		}
		return array(
			'technician_id' => $technicianId > 0 ? $technicianId : null,
			'technician_is_override' => 1,
		);
	}

	function getContractAmcAssetDefaultEmployeeId($companyId, $contractAmcAssetId)
	{
		global $callConfig;
		$contractAmcAssetId = (int) $contractAmcAssetId;
		$companyId = (int) $companyId;
		if ($contractAmcAssetId <= 0 || $companyId <= 0) {
			return 0;
		}
		$sql = "SELECT default_employee_id FROM contract_amc_assets
			WHERE id = {$contractAmcAssetId} AND company_id = {$companyId} AND is_active = 0 LIMIT 1";
		$row = $callConfig->getRow($sql);
		if (!$row) {
			return 0;
		}
		$r = is_array($row) ? $row : (array) $row;
		return (int) ($r['default_employee_id'] ?? 0);
	}

	function resolveEffectiveAmcEmployee($scheduleRow, $assetDefaultId, $empNameMap = array())
	{
		$schedule = (array) $scheduleRow;
		$isOverride = (int) ($schedule['employee_is_override'] ?? 0) == 1;
		$scheduleEmpId = (int) ($schedule['employee_id'] ?? 0);
		$assetDefaultId = (int) $assetDefaultId;
		$effectiveId = 0;

		if ($isOverride) {
			$effectiveId = $scheduleEmpId;
		} elseif ($scheduleEmpId > 0) {
			$effectiveId = $scheduleEmpId;
		} else {
			$effectiveId = $assetDefaultId;
		}

		$name = '';
		if ($effectiveId > 0) {
			if (count((array)$empNameMap) > 0 && isset($empNameMap[$effectiveId])) {
				$name = $empNameMap[$effectiveId];
			} elseif (!empty($schedule['employee_name']) && !$isOverride && $scheduleEmpId == $effectiveId) {
				$name = $schedule['employee_name'];
			} elseif (!empty($schedule['default_employee_name']) && $effectiveId == $assetDefaultId) {
				$name = $schedule['default_employee_name'];
			}
		}

		return array(
			'employee_id' => $effectiveId,
			'employee_name' => $name,
			'is_override' => $isOverride ? 1 : 0,
		);
	}

	function resolveAmcScheduleEmployeeOverrideFields($employeeId, $assetDefaultId, $resetToDefault = false)
	{
		$employeeId = (int) $employeeId;
		$assetDefaultId = (int) $assetDefaultId;
		if ($resetToDefault) {
			return array(
				'employee_id' => $assetDefaultId > 0 ? $assetDefaultId : null,
				'employee_is_override' => 0,
			);
		}
		if ($employeeId == $assetDefaultId || ($employeeId <= 0 && $assetDefaultId <= 0)) {
			return array(
				'employee_id' => $employeeId > 0 ? $employeeId : null,
				'employee_is_override' => 0,
			);
		}
		return array(
			'employee_id' => $employeeId > 0 ? $employeeId : null,
			'employee_is_override' => 1,
		);
	}

	function propagateAssetDefaultEmployeeToPendingSchedules($companyId, $contractAmcAssetId, $employeeId, $userId = 0)
	{
		global $callConfig;
		$companyId = (int) $companyId;
		$contractAmcAssetId = (int) $contractAmcAssetId;
		$employeeId = (int) $employeeId;
		if ($companyId <= 0 || $contractAmcAssetId <= 0) {
			return 0;
		}
		$empVal = $employeeId > 0 ? $employeeId : 'NULL';
		$now = date('Y-m-d H:i:s');
		$sql = "UPDATE contract_amc_service_schedules SET
			employee_id = {$empVal},
			employee_is_override = 0,
			updated_by = '" . addslashes($userId) . "',
			updated_date = '{$now}'
			WHERE company_id = {$companyId}
			AND contract_amc_asset_id = {$contractAmcAssetId}
			AND is_active = 0
			AND current_status = 0
			AND employee_is_override = 0";
		$callConfig->executeQuery($sql);
		return 1;
	}

	function propagateAssetDefaultToPendingSchedules($companyId, $contractAmcAssetId, $technicianId, $userId = 0)
	{
		global $callConfig;
		$companyId = (int) $companyId;
		$contractAmcAssetId = (int) $contractAmcAssetId;
		$technicianId = (int) $technicianId;
		if ($companyId <= 0 || $contractAmcAssetId <= 0) {
			return 0;
		}
		$techVal = $technicianId > 0 ? $technicianId : 'NULL';
		$now = date('Y-m-d H:i:s');
		$sql = "UPDATE contract_amc_service_schedules SET
			technician_id = {$techVal},
			technician_is_override = 0,
			updated_by = '" . addslashes($userId) . "',
			updated_date = '{$now}'
			WHERE company_id = {$companyId}
			AND contract_amc_asset_id = {$contractAmcAssetId}
			AND is_active = 0
			AND current_status = 0
			AND technician_is_override = 0";
		$callConfig->executeQuery($sql);
		return 1;
	}

	function getContractAmcTechnicianAssignments($get)
	{
		global $callConfig;
		$return = array('contract' => array(), 'locations' => array(), 'technicians' => array(), 'employees' => array(), 'service_mode' => 'vendor');
		$companyId = (int) ($get['company_id'] ?? 0);
		$contractId = (int) ($get['contract_id'] ?? 0);
		if ($companyId <= 0 || $contractId <= 0) {
			return $return;
		}

		$contractSql = "SELECT c.*,
			cad.service_mode, cad.department_id, cad.default_employee_id,
			CASE WHEN c.party_type = 'customer' THEN cu.display_name WHEN c.party_type = 'vendor' THEN v.display_name ELSE '' END AS party_name
			FROM contracts c
			LEFT JOIN contract_amc_details cad ON cad.contract_id = c.id AND cad.company_id = c.company_id AND cad.is_active = 0
			LEFT JOIN customers cu ON cu.id = c.party_id AND c.party_type = 'customer' AND cu.company_id = c.company_id AND cu.is_active = 0
			LEFT JOIN vendors v ON v.id = c.party_id AND c.party_type = 'vendor' AND v.company_id = c.company_id AND v.is_active = 0
			WHERE c.company_id = {$companyId} AND c.id = '" . addslashes($contractId) . "' AND c.is_active = 0 LIMIT 1";
		$contractRow = $callConfig->getRow($contractSql);
		if (!$contractRow) {
			return $return;
		}
		$return['contract'] = is_array($contractRow) ? $contractRow : (array) $contractRow;
		$return['service_mode'] = $this->resolveContractAmcServiceMode($return['contract']);
		$return['technicians'] = $this->getContractAmcTechnicians($companyId);
		$return['employees'] = $this->getContractAmcEmployees($companyId, (int) ($return['contract']['department_id'] ?? 0));

		$locSql = "SELECT cal.*, l.location_name
			FROM contract_amc_locations cal
			LEFT JOIN locations l ON l.id = cal.location_id AND l.company_id = cal.company_id AND l.is_active = 0
			WHERE cal.company_id = {$companyId} AND cal.contract_id = '" . addslashes($contractId) . "' AND cal.is_active = 0
			ORDER BY cal.id ASC";
		$locRows = $callConfig->getAllRows($locSql);

		$assetSql = "SELECT ca.*, a.asset_code, ac.category_description AS category_name,
			t.account_name AS default_technician_name, e.display_name AS default_employee_name
			FROM contract_amc_assets ca
			LEFT JOIN assets a ON a.id = ca.asset_id AND a.company_id = ca.company_id AND a.is_active = 0
			LEFT JOIN asset_categories ac ON ac.id = a.category_id AND ac.company_id = ca.company_id AND ac.is_active = 0
			LEFT JOIN amc_account_details t ON t.id = ca.default_technician_id AND t.company_id = ca.company_id AND t.is_active = 0
			LEFT JOIN employees e ON e.id = ca.default_employee_id AND e.company_id = ca.company_id AND e.is_active = 0
			WHERE ca.company_id = {$companyId} AND ca.contract_id = '" . addslashes($contractId) . "' AND ca.is_active = 0
			ORDER BY ca.id ASC";
		$assetRows = $callConfig->getAllRows($assetSql);
		$assetsByLoc = array();
		$assetIds = array();
		if (count((array)$assetRows) > 0) {
			foreach ($assetRows as $ar) {
				$a = is_array($ar) ? $ar : (array) $ar;
				$locKey = $a['contract_amc_location_id'] ?? 0;
				if (!isset($assetsByLoc[$locKey])) {
					$assetsByLoc[$locKey] = array();
				}
				$assetsByLoc[$locKey][] = $a;
				$assetIds[] = (int) ($a['id'] ?? 0);
			}
		}

		$assetRollups = array();
		if (count((array)$assetIds) > 0) {
			$idList = implode(',', array_map('intval', $assetIds));
			$overrideCol = ($return['service_mode'] == 'inhouse') ? 'employee_is_override' : 'technician_is_override';
			$rollupSql = "SELECT contract_amc_asset_id,
				SUM(CASE WHEN current_status = 0 THEN 1 ELSE 0 END) AS pending_count,
				SUM(CASE WHEN current_status = 0 AND {$overrideCol} = 0 THEN 1 ELSE 0 END) AS pending_non_override_count
				FROM contract_amc_service_schedules
				WHERE company_id = {$companyId} AND is_active = 0 AND contract_amc_asset_id IN ({$idList})
				GROUP BY contract_amc_asset_id";
			$rollupRows = $callConfig->getAllRows($rollupSql);
			if (count((array)$rollupRows) > 0) {
				foreach ($rollupRows as $rr) {
					$r = is_array($rr) ? $rr : (array) $rr;
					$assetRollups[$r['contract_amc_asset_id']] = $r;
				}
			}
		}

		if (count((array)$locRows) > 0) {
			foreach ($locRows as $lr) {
				$loc = is_array($lr) ? $lr : (array) $lr;
				$locId = $loc['id'] ?? 0;
				$loc['assets'] = array();
				$locAssets = $assetsByLoc[$locId] ?? array();
				if (count((array)$locAssets) > 0) {
					foreach ($locAssets as $asset) {
						$aid = $asset['id'] ?? 0;
						$rollup = $assetRollups[$aid] ?? array();
						$asset['pending_count'] = (int) ($rollup['pending_count'] ?? 0);
						$asset['pending_non_override_count'] = (int) ($rollup['pending_non_override_count'] ?? 0);
						if ($return['service_mode'] == 'inhouse') {
							$asset['is_assigned'] = !empty($asset['default_employee_id']) ? 1 : 0;
						} else {
							$asset['is_assigned'] = !empty($asset['default_technician_id']) ? 1 : 0;
						}
						$loc['assets'][] = $asset;
					}
				}
				$return['locations'][] = $loc;
			}
		}

		$return['can_execute'] = $this->canExecuteContractAmcService($get, $return['contract']);
		$return['contract_is_approved'] = $this->isContractAmcApprovedForExecution($get, $return['contract']);
		return $return;
	}

	function getContractAmcChecklist($get)
	{
		global $callConfig;
		$return = array(
			'contract' => array(),
			'checklist_items' => array(),
			'checklist_is_default' => true,
			'can_execute' => false,
			'contract_is_approved' => false,
		);
		$companyId = (int) ($get['company_id'] ?? 0);
		$contractId = (int) ($get['contract_id'] ?? 0);
		if ($companyId <= 0 || $contractId <= 0) {
			return $return;
		}

		$contractSql = "SELECT c.*,
			cad.service_mode, cad.department_id,
			CASE WHEN c.party_type = 'customer' THEN cu.display_name WHEN c.party_type = 'vendor' THEN v.display_name ELSE '' END AS party_name
			FROM contracts c
			LEFT JOIN contract_amc_details cad ON cad.contract_id = c.id AND cad.company_id = c.company_id AND cad.is_active = 0
			LEFT JOIN customers cu ON cu.id = c.party_id AND c.party_type = 'customer' AND cu.company_id = c.company_id AND cu.is_active = 0
			LEFT JOIN vendors v ON v.id = c.party_id AND c.party_type = 'vendor' AND v.company_id = c.company_id AND v.is_active = 0
			WHERE c.company_id = {$companyId} AND c.id = '" . addslashes($contractId) . "' AND c.is_active = 0 LIMIT 1";
		$contractRow = $callConfig->getRow($contractSql);
		if (!$contractRow) {
			return $return;
		}
		$return['contract'] = is_array($contractRow) ? $contractRow : (array) $contractRow;

		$checkSql = "SELECT COUNT(id) AS cnt FROM application_check_lists
			WHERE company_id = '" . addslashes($companyId) . "' AND transaction_id = '" . addslashes($contractId) . "'
			AND application_tablename = 'contracts' AND is_active = 0";
		$checkRow = $callConfig->getRow($checkSql);
		$cnt = 0;
		if ($checkRow) {
			$cr = is_array($checkRow) ? $checkRow : (array) $checkRow;
			$cnt = (int) ($cr['cnt'] ?? 0);
		}
		$return['checklist_is_default'] = ($cnt == 0);
		$return['checklist_items'] = $this->getDefaultContractAmcChecklistItems($companyId, $contractId);
		$return['can_execute'] = $this->canExecuteContractAmcService($get, $return['contract']);
		$return['contract_is_approved'] = $this->isContractAmcApprovedForExecution($get, $return['contract']);
		return $return;
	}

	function saveContractAmcChecklist($get, $post)
	{
		global $callConfig;
		$return = array('status' => 'error', 'message' => 'Unable to save checklist');
		$contractId = (int) ($post['contract_id'] ?? 0);
		$companyId = (int) ($get['company_id'] ?? 0);
		$userId = (int) ($get['user_id'] ?? 0);

		if ($contractId <= 0 || $companyId <= 0) {
			$return['message'] = 'Invalid contract';
			return $return;
		}

		$contractSql = "SELECT c.*, cad.service_mode FROM contracts c
			LEFT JOIN contract_amc_details cad ON cad.contract_id = c.id AND cad.company_id = c.company_id AND cad.is_active = 0
			WHERE c.company_id = {$companyId} AND c.id = '" . addslashes($contractId) . "' AND c.is_active = 0 LIMIT 1";
		$contractRow = $callConfig->getRow($contractSql);
		if (!$contractRow) {
			$return['message'] = 'Contract not found';
			return $return;
		}
		$contract = is_array($contractRow) ? $contractRow : (array) $contractRow;
		if (!$this->isContractAmcApprovedForExecution($get, $contract)) {
			$return['message'] = 'Contract must be approved before checklist can be saved';
			return $return;
		}
		if (!$this->canExecuteContractAmcService($get, $contract)) {
			$return['message'] = 'Not authorized';
			return $return;
		}

		$checklistItems = $post['checklist_items'] ?? array();
		if (!is_array($checklistItems) || count((array)$checklistItems) == 0) {
			$return['message'] = 'At least one checklist item is required';
			return $return;
		}
		if (count((array)$checklistItems) > 0) {
			foreach ($checklistItems as $fieldValue) {
				if (trim((string) $fieldValue) == '') {
					$return['message'] = 'Checklist item name cannot be empty';
					return $return;
				}
			}
		}

		$now = date('Y-m-d H:i:s');
		$query = "UPDATE application_check_lists SET is_active = NULL, updated_by={$userId}, updated_date = '{$now}'
			WHERE transaction_id = '" . addslashes($contractId) . "' AND application_tablename='contracts'
			AND company_id={$companyId} AND is_active = 0";
		$callConfig->executeQuery($query);

		$existingIds = $post['existing_checklist_ids'] ?? array();
		if (!is_array($existingIds)) {
			$existingIds = array();
		}

		$saved = 0;
		if (count((array)$checklistItems) > 0) {
			foreach ($checklistItems as $i => $fieldValue) {
				$fieldValue = trim((string) $fieldValue);
				$checkId = $existingIds[$i] ?? '';
				if ($checkId != '' && strpos((string) $checkId, 'def_') !== 0 && is_numeric($checkId)) {
					$update_check_array = array(
						'check_name' => $fieldValue,
						'is_active' => '0',
						'updated_by' => $userId,
						'updated_date' => $now,
					);
					$res = $callConfig->updateRecord('application_check_lists', $update_check_array, 'id', $checkId);
					if (isset($res['status']) && $res['status'] == 'sucess') {
						$saved++;
					}
				} else {
					$check_array = array(
						'company_id' => $companyId,
						'transaction_id' => $contractId,
						'application_tablename' => 'contracts',
						'check_name' => $fieldValue,
						'current_status' => '0',
						'is_active' => '0',
						'created_by' => $userId,
						'created_date' => $now,
					);
					$res = $callConfig->insertRecord('application_check_lists', $check_array);
					if (isset($res['status']) && $res['status'] == 'sucess') {
						$saved++;
					}
				}
			}
		}

		if ($saved > 0) {
			$return['status'] = 'success';
			$return['message'] = 'Checklist saved';
			$return['saved_count'] = $saved;
		} else {
			$return['message'] = 'No checklist items were saved';
		}
		return $return;
	}

	function saveContractAmcAssetTechnicians($get, $post)
	{
		global $callConfig;
		$return = array('status' => 'error', 'message' => 'Unable to save assignments');
		$contractId = (int) ($post['contract_id'] ?? 0);
		$companyId = (int) ($get['company_id'] ?? 0);
		$userId = (int) ($get['user_id'] ?? 0);
		$propagatePending = !isset($post['propagate_pending']) || $post['propagate_pending'] == '1' || $post['propagate_pending'] == 1;

		if ($contractId <= 0 || $companyId <= 0) {
			$return['message'] = 'Invalid contract';
			return $return;
		}

		$contractSql = "SELECT c.*, cad.service_mode FROM contracts c
			LEFT JOIN contract_amc_details cad ON cad.contract_id = c.id AND cad.company_id = c.company_id AND cad.is_active = 0
			WHERE c.company_id = {$companyId} AND c.id = '" . addslashes($contractId) . "' AND c.is_active = 0 LIMIT 1";
		$contractRow = $callConfig->getRow($contractSql);
		if (!$contractRow) {
			$return['message'] = 'Contract not found';
			return $return;
		}
		$contract = is_array($contractRow) ? $contractRow : (array) $contractRow;
		$serviceMode = $this->resolveContractAmcServiceMode($contract);
		if (!$this->isContractAmcApprovedForExecution($get, $contract)) {
			$return['message'] = 'Contract must be approved before assignments can be saved';
			return $return;
		}
		if (!$this->canExecuteContractAmcService($get, $contract)) {
			$return['message'] = 'Not authorized';
			return $return;
		}

		$assetAssignments = ($serviceMode == 'inhouse')
			? ($post['asset_employees'] ?? array())
			: ($post['asset_technicians'] ?? array());
		if (!is_array($assetAssignments) || count((array)$assetAssignments) == 0) {
			$return['message'] = 'No asset assignments provided';
			return $return;
		}

		$now = date('Y-m-d H:i:s');
		$updated = 0;
		foreach ($assetAssignments as $assetId => $assigneeId) {
			$assetId = (int) $assetId;
			$assigneeId = (int) $assigneeId;
			if ($assetId <= 0) {
				continue;
			}
			$checkSql = "SELECT id FROM contract_amc_assets
				WHERE id = {$assetId} AND contract_id = '" . addslashes($contractId) . "'
				AND company_id = {$companyId} AND is_active = 0 LIMIT 1";
			$checkRow = $callConfig->getRow($checkSql);
			if (!$checkRow) {
				continue;
			}
			if ($serviceMode == 'inhouse') {
				$update = array(
					'default_employee_id' => $assigneeId > 0 ? $assigneeId : null,
					'updated_by' => $userId,
					'updated_date' => $now,
				);
			} else {
				$update = array(
					'default_technician_id' => $assigneeId > 0 ? $assigneeId : null,
					'updated_by' => $userId,
					'updated_date' => $now,
				);
			}
			$res = $callConfig->updateRecord('contract_amc_assets', $update, 'id', $assetId);
			if (isset($res['status']) && $res['status'] == 'sucess') {
				$updated++;
				if ($propagatePending) {
					if ($serviceMode == 'inhouse') {
						$this->propagateAssetDefaultEmployeeToPendingSchedules($companyId, $assetId, $assigneeId, $userId);
					} else {
						$this->propagateAssetDefaultToPendingSchedules($companyId, $assetId, $assigneeId, $userId);
					}
				}
			}
		}

		if ($updated > 0) {
			$return['status'] = 'success';
			$return['message'] = ($serviceMode == 'inhouse') ? 'Employee assignments saved' : 'Technician assignments saved';
			$return['updated_count'] = $updated;
		} else {
			$return['message'] = 'No assignments were updated';
		}
		return $return;
	}

	function assignContractAmcTechnician($get, $post)
	{
		global $callConfig;
		$return = array('status' => 'error', 'message' => 'Unable to assign');
		$scheduleId = (int) ($post['schedule_id'] ?? 0);
		if ($scheduleId <= 0) {
			$return['message'] = 'Invalid schedule';
			return $return;
		}

		$schedSql = "SELECT s.*, s.current_status AS visit_status, c.party_type, c.party_id, c.current_status, c.cancel_status,
			cad.service_mode, cad.department_id
			FROM contract_amc_service_schedules s
			INNER JOIN contracts c ON c.id = s.contract_id AND c.company_id = s.company_id AND c.is_active = 0
			LEFT JOIN contract_amc_details cad ON cad.contract_id = c.id AND cad.company_id = c.company_id AND cad.is_active = 0
			WHERE s.id = {$scheduleId} AND s.company_id = '" . addslashes($get['company_id']) . "' AND s.is_active = 0 LIMIT 1";
		$schedRow = $callConfig->getRow($schedSql);
		if (!$schedRow) {
			$return['message'] = 'Schedule not found';
			return $return;
		}
		$sched = is_array($schedRow) ? $schedRow : (array) $schedRow;
		if (!$this->isContractAmcApprovedForExecution($get, $sched)) {
			$return['message'] = 'Contract must be approved before service visits can be started or updated';
			return $return;
		}
		if (!$this->canExecuteContractAmcService($get, $sched)) {
			$return['message'] = 'Not authorized';
			return $return;
		}

		$serviceMode = $this->resolveContractAmcServiceMode($sched);
		$resetToDefault = !empty($post['reset_to_default']) && ($post['reset_to_default'] == '1' || $post['reset_to_default'] == 1);

		if ($serviceMode == 'inhouse') {
			$employeeId = (int) ($post['employee_id'] ?? 0);
			$assetDefaultId = $this->getContractAmcAssetDefaultEmployeeId($get['company_id'], $sched['contract_amc_asset_id'] ?? 0);
			if ($resetToDefault) {
				$employeeId = $assetDefaultId;
			}
			$overrideFields = $this->resolveAmcScheduleEmployeeOverrideFields($employeeId, $assetDefaultId, $resetToDefault);
			$update = array(
				'employee_id' => $overrideFields['employee_id'],
				'employee_is_override' => $overrideFields['employee_is_override'],
				'updated_by' => $get['user_id'],
				'updated_date' => date('Y-m-d H:i:s'),
			);
			$successMsg = $resetToDefault ? 'Reset to asset default' : 'Employee assigned';
		} else {
			$technicianId = (int) ($post['technician_id'] ?? 0);
			$assetDefaultId = $this->getContractAmcAssetDefaultTechnicianId($get['company_id'], $sched['contract_amc_asset_id'] ?? 0);
			if ($resetToDefault) {
				$technicianId = $assetDefaultId;
			}
			$overrideFields = $this->resolveAmcScheduleTechnicianOverrideFields($technicianId, $assetDefaultId, $resetToDefault);
			$update = array(
				'technician_id' => $overrideFields['technician_id'],
				'technician_is_override' => $overrideFields['technician_is_override'],
				'updated_by' => $get['user_id'],
				'updated_date' => date('Y-m-d H:i:s'),
			);
			$successMsg = $resetToDefault ? 'Reset to asset default' : 'Technician assigned';
		}

		$res = $callConfig->updateRecord('contract_amc_service_schedules', $update, 'id', $scheduleId);
		$return['res'] = $res;
		$return['status'] = (isset($res['status']) && $res['status'] == 'sucess') ? 'success' : 'error';
		$return['message'] = $return['status'] == 'success' ? $successMsg : 'Update failed';
		return $return;
	}

	function getDefaultContractAmcChecklistItems($company_id, $contract_id)
	{
		global $callConfig;
		$items = array();
		$sql = "SELECT id, check_name FROM application_check_lists
			WHERE company_id = '" . addslashes($company_id) . "' AND transaction_id = '" . addslashes($contract_id) . "'
			AND application_tablename = 'contracts' AND is_active = 0";
		$rows = $callConfig->getAllRows($sql);
		if (count((array)$rows) > 0) {
			foreach ($rows as $row) {
				$r = is_array($row) ? $row : (array) $row;
				$items[$r['id']] = $r['check_name'];
			}
		}
		if (count((array)$items) == 0) {
			$defaults = array('Visual Inspection', 'Functional Test', 'Safety Check');
			$idx = 1;
			foreach ($defaults as $name) {
				$items['def_' . $idx] = $name;
				$idx++;
			}
		}
		return $items;
	}

	function getContractAmcScheduleDetails($get)
	{
		global $callConfig;
		$return = array();
		$scheduleId = (int) ($get['id'] ?? $get['schedule_id'] ?? 0);
		if ($scheduleId <= 0) {
			return $return;
		}

		$sql = "SELECT s.*, s.current_status AS visit_status, c.party_type, c.party_id, c.contract_name,
			CASE WHEN c.party_type = 'vendor' THEN v.display_name WHEN c.party_type = 'customer' THEN cu.display_name ELSE '' END AS party_name,
			c.current_status, c.cancel_status, c.cancel_reason, c.cancel_date,
			cad.service_mode, cad.department_id,
			t.account_name AS technician_name, e.display_name AS employee_name,
			ca.default_technician_id AS asset_default_technician_id, td.account_name AS default_technician_name,
			ca.default_employee_id AS asset_default_employee_id, ed.display_name AS default_employee_name
			FROM contract_amc_service_schedules s
			INNER JOIN contracts c ON c.id = s.contract_id AND c.company_id = s.company_id AND c.is_active = 0
			LEFT JOIN vendors v ON v.id = c.party_id AND c.party_type = 'vendor' AND v.company_id = c.company_id AND v.is_active = 0
			LEFT JOIN customers cu ON cu.id = c.party_id AND c.party_type = 'customer' AND cu.company_id = c.company_id AND cu.is_active = 0
			LEFT JOIN contract_amc_details cad ON cad.contract_id = c.id AND cad.company_id = c.company_id AND cad.is_active = 0
			LEFT JOIN contract_amc_assets ca ON ca.id = s.contract_amc_asset_id AND ca.company_id = s.company_id AND ca.is_active = 0
			LEFT JOIN amc_account_details t ON t.id = s.technician_id AND t.company_id = s.company_id AND t.is_active = 0
			LEFT JOIN amc_account_details td ON td.id = ca.default_technician_id AND td.company_id = ca.company_id AND td.is_active = 0
			LEFT JOIN employees e ON e.id = s.employee_id AND e.company_id = s.company_id AND e.is_active = 0
			LEFT JOIN employees ed ON ed.id = ca.default_employee_id AND ed.company_id = ca.company_id AND ed.is_active = 0
			WHERE s.id = {$scheduleId} AND s.company_id = '" . addslashes($get['company_id']) . "' AND s.is_active = 0 LIMIT 1";
		$row = $callConfig->getRow($sql);
		if (!$row) {
			return $return;
		}
		$schedule = is_array($row) ? $row : (array) $row;
		$schedule['planned_display'] = $this->formatAmcScheduleDateTime($schedule['planned_service_datetime'] ?? '');
		$st = $this->getAmcScheduleStatusLabel($schedule['visit_status'] ?? 0);
		$schedule['status_label'] = $st['label'];
		$schedule['status_class'] = $st['class'];
		$serviceMode = $this->resolveContractAmcServiceMode($schedule);
		if ($serviceMode == 'inhouse') {
			$empNameMap = $this->getContractAmcEmployees($get['company_id'], (int) ($schedule['department_id'] ?? 0));
			$effective = $this->resolveEffectiveAmcEmployee($schedule, (int) ($schedule['asset_default_employee_id'] ?? 0), $empNameMap);
			$schedule['effective_employee_id'] = $effective['employee_id'];
			$schedule['effective_employee_name'] = $effective['employee_name'];
			$schedule['employee_is_override'] = $effective['is_override'];
		} else {
			$techNameMap = $this->getContractAmcTechnicians($get['company_id']);
			$effective = $this->resolveEffectiveAmcTechnician($schedule, (int) ($schedule['asset_default_technician_id'] ?? 0), $techNameMap);
			$schedule['effective_technician_id'] = $effective['technician_id'];
			$schedule['effective_technician_name'] = $effective['technician_name'];
			$schedule['technician_is_override'] = $effective['is_override'];
		}
		$return['schedule'] = $schedule;
		$return['service_mode'] = $serviceMode;
		$return['technicians'] = $this->getContractAmcTechnicians($get['company_id']);
		$return['employees'] = $this->getContractAmcEmployees($get['company_id'], (int) ($schedule['department_id'] ?? 0));
		$return['contract_is_approved'] = $this->isContractAmcApprovedForExecution($get, $schedule);
		$return['can_execute'] = $this->canExecuteContractAmcService($get, $schedule);
		$return['checklist_items'] = $this->getDefaultContractAmcChecklistItems($get['company_id'], $schedule['contract_id']);

		$checkSql = "SELECT * FROM contract_amc_check_list
			WHERE contract_amc_service_schedule_id = {$scheduleId} AND company_id = '" . addslashes($get['company_id']) . "' AND is_active = 0";
		$checkRows = $callConfig->getAllRows($checkSql);
		$check_list_status = array();
		$dbByAppId = array();
		$dbByName = array();
		if (count((array)$checkRows) > 0) {
			foreach ($checkRows as $cr) {
				$c = is_array($cr) ? $cr : (array) $cr;
				if ($c['application_check_list_id'] != '') {
					$dbByAppId[$c['application_check_list_id']] = $c;
				}
				$dbByName[$c['check_name']] = $c;
			}
		}
		if (count((array)$return['checklist_items']) > 0) {
			foreach ($return['checklist_items'] as $itemKey => $itemName) {
				if (isset($dbByAppId[$itemKey])) {
					$check_list_status[$itemKey] = $dbByAppId[$itemKey];
				} elseif (isset($dbByName[$itemName])) {
					$check_list_status[$itemKey] = $dbByName[$itemName];
				}
			}
		}
		$return['check_list_status'] = $check_list_status;

		$repairBundle = $this->getRepairOrderBundleForSchedule($get['company_id'], $scheduleId, $schedule);
		$return['repair_eligibility'] = $repairBundle['repair_eligibility'];
		$return['can_manage_repair'] = ($repairBundle['repair_eligibility'] == 'editable');
		$return['repair_order'] = $repairBundle['repair_order'];
		$return['repair_order_lines'] = $repairBundle['repair_order_lines'];
		$return['repair_contract_assets'] = $this->getAmcRepairOrderContractAssetsData($get['company_id'], (int) ($schedule['contract_id'] ?? 0));
		$return['repair_items'] = $this->getAmcRepairOrderItemsData($get['company_id']);

		return $return;
	}

	function isContractAmcChecklistComplete($company_id, $schedule_id, $contract_id)
	{
		global $callConfig;
		$items = $this->getDefaultContractAmcChecklistItems($company_id, $contract_id);
		if (count((array)$items) == 0) {
			return false;
		}
		$checkSql = "SELECT application_check_list_id, check_name, check_status FROM contract_amc_check_list
			WHERE contract_amc_service_schedule_id = '" . addslashes($schedule_id) . "'
			AND company_id = '" . addslashes($company_id) . "' AND is_active = 0";
		$checkRows = $callConfig->getAllRows($checkSql);
		$saved = array();
		if (count((array)$checkRows) > 0) {
			foreach ($checkRows as $cr) {
				$c = is_array($cr) ? $cr : (array) $cr;
				$key = $c['application_check_list_id'] != '' ? $c['application_check_list_id'] : $c['check_name'];
				$saved[$key] = $c['check_status'];
			}
		}
		foreach ($items as $key => $name) {
			$status = $saved[$key] ?? $saved[$name] ?? '0';
			if ($status != '1' && $status != 'Y') {
				return false;
			}
		}
		return true;
	}

	function updateContractAmcScheduleDetails($get, $post)
	{
		global $callConfig;
		$return = array('status' => 'error', 'message' => 'Update failed');
		$scheduleId = (int) ($post['schedule_id'] ?? $post['record_id'] ?? 0);
		$action = strtolower($post['action'] ?? '');
		if ($scheduleId <= 0) {
			$return['message'] = 'Invalid schedule';
			return $return;
		}

		$detail = $this->getContractAmcScheduleDetails(array_merge($get, array('schedule_id' => $scheduleId)));
		$schedule = $detail['schedule'] ?? array();
		if (count((array)$schedule) == 0) {
			$return['message'] = 'Schedule not found';
			return $return;
		}
		if (!$this->isContractAmcApprovedForExecution($get, $schedule)) {
			$return['message'] = 'Contract must be approved before service visits can be started or updated';
			return $return;
		}
		if (empty($detail['can_execute'])) {
			$return['message'] = 'Not authorized';
			return $return;
		}

		$now = date('Y-m-d H:i:s');
		$update = array(
			'updated_by' => $get['user_id'],
			'updated_date' => $now,
		);

		if ($action == 'start') {
			$update['actual_start_datetime'] = $now;
			$update['current_status'] = 1;
		} elseif ($action == 'finish') {
			if (!$this->isContractAmcChecklistComplete($get['company_id'], $scheduleId, $schedule['contract_id'])) {
				$return['message'] = 'Complete all checklist items before finishing';
				return $return;
			}
			$update['actual_end_datetime'] = $now;
			$update['current_status'] = 199;
		}

		if (isset($post['technician_id'])) {
			$assetDefaultId = $this->getContractAmcAssetDefaultTechnicianId($get['company_id'], $schedule['contract_amc_asset_id'] ?? 0);
			$postedTechId = (int) $post['technician_id'];
			$overrideFields = $this->resolveAmcScheduleTechnicianOverrideFields($postedTechId, $assetDefaultId, false);
			$update['technician_id'] = $overrideFields['technician_id'];
			$update['technician_is_override'] = $overrideFields['technician_is_override'];
		}

		if (isset($post['employee_id'])) {
			$assetDefaultEmpId = $this->getContractAmcAssetDefaultEmployeeId($get['company_id'], $schedule['contract_amc_asset_id'] ?? 0);
			$postedEmpId = (int) $post['employee_id'];
			$overrideFields = $this->resolveAmcScheduleEmployeeOverrideFields($postedEmpId, $assetDefaultEmpId, false);
			$update['employee_id'] = $overrideFields['employee_id'];
			$update['employee_is_override'] = $overrideFields['employee_is_override'];
		}

		$res = $callConfig->updateRecord('contract_amc_service_schedules', $update, 'id', $scheduleId);
		$return['res'] = $res;

		if (count((array)$post['checklist_items']) > 0) {
			foreach ($post['checklist_items'] as $i => $fieldValue) {
				$check_id = $post['existing_checklist_ids'][$i] ?? ($post['checklist_keys'][$i] ?? '');
				$check_status = $post['checklist_status'][$check_id] ?? ($post['checklist_status'][$i] ?? '0');
				$check_remarks = $post['checklist_remarks'][$i] ?? '';
				$existingId = $post['saved_checklist_row_ids'][$check_id] ?? '';

				if ($existingId != '') {
					$check_update_arr = array(
						'check_status' => $check_status,
						'check_remarks' => $check_remarks,
						'updated_by' => $get['user_id'],
						'updated_date' => $now,
					);
					$callConfig->updateRecord('contract_amc_check_list', $check_update_arr, 'id', $existingId);
				} else {
					$appCheckId = (strpos((string) $check_id, 'def_') === 0) ? null : $check_id;
					$check_array = array(
						'company_id' => $get['company_id'],
						'contract_id' => $schedule['contract_id'],
						'contract_amc_service_schedule_id' => $scheduleId,
						'application_check_list_id' => $appCheckId,
						'check_status' => $check_status,
						'check_remarks' => $check_remarks,
						'check_name' => $fieldValue,
						'current_status' => '0',
						'is_active' => '0',
						'created_by' => $get['user_id'],
						'created_date' => $now,
					);
					$callConfig->insertRecord('contract_amc_check_list', $check_array);
				}
			}
		}

		$return['status'] = (isset($res['status']) && $res['status'] == 'sucess') ? 'success' : 'error';
		$return['message'] = $return['status'] == 'success' ? 'Saved successfully' : 'Update failed';
		return $return;
	}

	function buildContractAmcDashboardWhere($get)
	{
		global $callConfig;
		$companyId = (int) ($get['company_id'] ?? 0);
		$where = "s.company_id = {$companyId} AND s.is_active = 0";
		$partyType = $get['party_type'] ?? '';
		$partyId = (int) ($get['party_id'] ?? 0);
		if ($partyType != '' && $partyId > 0) {
			$where .= " AND c.party_type = '" . addslashes($partyType) . "' AND c.party_id = {$partyId}";
		}
		$technicianId = (int) ($get['technician_id'] ?? 0);
		if ($technicianId > 0) {
			$where .= " AND COALESCE(NULLIF(s.technician_id, 0), ca.default_technician_id) = {$technicianId}";
		}
		return $where;
	}

	function buildContractAmcDashboardJoin($get)
	{
		return "INNER JOIN contracts c ON c.id = s.contract_id AND c.company_id = s.company_id AND c.is_active = 0 AND c.is_amc_contract = 'Y' AND c.current_status = '" . ST_APPROVED . "'
			LEFT JOIN contract_amc_assets ca ON ca.id = s.contract_amc_asset_id AND ca.company_id = s.company_id AND ca.is_active = 0";
	}

	function resolveContractAmcFmVisitStatus($row)
	{
		$row = is_object($row) ? (array) $row : (array) $row;
		$fmConfirmed = trim((string) ($row['fm_confirmed_datetime'] ?? ''));
		if ($fmConfirmed != '') {
			return array('code' => 'closed', 'label' => 'Closed', 'class' => 'bg-success');
		}
		$currentStatus = (int) ($row['current_status'] ?? $row['visit_status'] ?? 0);
		$actualEnd = trim((string) ($row['actual_end_datetime'] ?? ''));
		if ($currentStatus >= 199 && $actualEnd != '') {
			return array('code' => 'pending_fm', 'label' => 'Pending FM', 'class' => 'bg-info text-dark');
		}
		$planned = $row['planned_service_datetime'] ?? '';
		$plannedDate = $planned != '' ? date('Y-m-d', strtotime($planned)) : '';
		$today = date('Y-m-d');
		if ($plannedDate != '' && $plannedDate < $today && $currentStatus < 199) {
			return array('code' => 'delayed', 'label' => 'Delayed', 'class' => 'bg-danger');
		}
		return array('code' => 'due', 'label' => 'Due', 'class' => 'bg-primary');
	}

	function enrichContractAmcFmVisitRow($row)
	{
		global $callConfig;
		$r = is_object($row) ? (array) $row : (array) $row;
		$assetCode = $r['asset_code'] ?? '';
		$assetName = $r['asset_name'] ?? $r['ca_asset_name'] ?? '—';
		if ($assetCode != '') {
			$r['asset_label'] = $assetCode . ' — ' . $assetName;
		} else {
			$r['asset_label'] = $assetName;
		}
		$r['vendor_name'] = $r['vendor_name'] ?? '—';
		$r['planned_display'] = $this->formatAmcScheduleDateTime($r['planned_service_datetime'] ?? '');
		$fmStatus = $this->resolveContractAmcFmVisitStatus($r);
		$r['fm_status'] = $fmStatus['code'];
		$r['fm_status_label'] = $fmStatus['label'];
		$r['fm_status_class'] = $fmStatus['class'];
		if (!empty($r['fm_confirmed_datetime'])) {
			$r['completed_display'] = $this->formatAmcScheduleDateTime($r['fm_confirmed_datetime']);
		} elseif (!empty($r['actual_end_datetime'])) {
			$r['completed_display'] = $this->formatAmcScheduleDateTime($r['actual_end_datetime']);
		} else {
			$r['completed_display'] = '—';
		}
		$r['can_confirm'] = ($fmStatus['code'] == 'pending_fm');
		return $r;
	}

	function hasContractAmcFmDashboardAccess($get)
	{
		global $callConfig;
		$userId = (int) ($get['user_id'] ?? 0);
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($userId <= 0 || $companyId <= 0) {
			return false;
		}
		$sql = "SELECT COUNT(c.id) AS c FROM contracts c
			INNER JOIN contract_amc_details cad ON cad.contract_id = c.id AND cad.company_id = c.company_id AND cad.is_active = 0
			WHERE c.company_id = {$companyId} AND c.is_active = 0 AND c.is_amc_contract = 'Y'
			AND c.party_type = 'vendor' AND c.current_status = '" . ST_APPROVED . "'
			AND cad.customer_connect_id = {$userId} LIMIT 1";
		$row = $callConfig->getRow($sql);
		return $row && (int) ($row->c ?? 0) > 0;
	}

	function getContractAmcFmDashboardSectionFilters()
	{
		$monthStart = date('Y-m-01');
		$monthEnd = date('Y-m-t');
		return array(
			'previously' => "DATE(s.planned_service_datetime) < '{$monthStart}'",
			'this_month' => "DATE(s.planned_service_datetime) BETWEEN '{$monthStart}' AND '{$monthEnd}'",
			'next_coming' => "DATE(s.planned_service_datetime) > '{$monthEnd}'",
		);
	}

	function buildContractAmcFmDashboardSearchWhere($search)
	{
		global $callConfig;
		$search = trim((string) $search);
		if ($search == '') {
			return '';
		}
		$esc = mysqli_real_escape_string($callConfig->con, $search);
		$like = "'%" . $esc . "%'";
		return " AND (v.display_name LIKE {$like} OR a.asset_code LIKE {$like} OR ca.asset_name LIKE {$like} OR c.contract_name LIKE {$like}"
			. " OR s.planned_service_datetime LIKE {$like}"
			. " OR DATE_FORMAT(s.planned_service_datetime, '%d-%m-%Y') LIKE {$like}"
			. " OR DATE_FORMAT(s.planned_service_datetime, '%Y-%m-%d') LIKE {$like}"
			. " OR DATE_FORMAT(s.planned_service_datetime, '%d/%m/%Y') LIKE {$like})";
	}

	function getContractAmcFmDashboardSection($get, $sectionKey, $page = 1, $search = '')
	{
		global $callConfig;
		$empty = array(
			'rows' => array(),
			'total' => 0,
			'page' => 1,
			'limit' => 10,
			'total_pages' => 0,
		);
		$sectionFilters = $this->getContractAmcFmDashboardSectionFilters();
		if (!isset($sectionFilters[$sectionKey])) {
			return $empty;
		}

		$page = max(1, (int) $page);
		$limit = 10;
		$offset = ($page - 1) * $limit;
		$join = $this->buildContractAmcFmDashboardJoin();
		$where = $this->buildContractAmcFmDashboardWhere($get);
		$sectionFilter = $sectionFilters[$sectionKey];
		$searchWhere = $this->buildContractAmcFmDashboardSearchWhere($search);
		$baseFrom = "FROM contract_amc_service_schedules s {$join} WHERE {$where} AND {$sectionFilter}{$searchWhere}";

		$countSql = "SELECT COUNT(s.id) AS c {$baseFrom}";
		$countRow = $callConfig->getRow($countSql);
		$total = (int) ($countRow->c ?? 0);
		$totalPages = $total > 0 ? (int) ceil($total / $limit) : 0;
		if ($totalPages > 0 && $page > $totalPages) {
			$page = $totalPages;
			$offset = ($page - 1) * $limit;
		}

		$select = "SELECT s.id, s.planned_service_datetime, s.actual_end_datetime, s.fm_confirmed_datetime,
			s.current_status, s.fm_confirmation_remarks, s.fm_confirmation_file,
			c.id AS contract_id, c.contract_name, v.display_name AS vendor_name,
			a.asset_code, ca.asset_name AS ca_asset_name
			{$baseFrom} ORDER BY s.planned_service_datetime DESC LIMIT {$limit} OFFSET {$offset}";
		$rows = $callConfig->getAllRows($select);
		$list = array();
		if (count((array)$rows) > 0) {
			foreach ($rows as $row) {
				$list[] = $this->enrichContractAmcFmVisitRow($row);
			}
		}

		return array(
			'rows' => $list,
			'total' => $total,
			'page' => $page,
			'limit' => $limit,
			'total_pages' => $totalPages,
		);
	}

	function getContractAmcFmDashboardData($get)
	{
		$return = array(
			'has_access' => false,
			'previously' => array('rows' => array(), 'total' => 0, 'page' => 1, 'limit' => 10, 'total_pages' => 0),
			'this_month' => array('rows' => array(), 'total' => 0, 'page' => 1, 'limit' => 10, 'total_pages' => 0),
			'next_coming' => array('rows' => array(), 'total' => 0, 'page' => 1, 'limit' => 10, 'total_pages' => 0),
		);
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($companyId <= 0) {
			return $return;
		}
		$return['has_access'] = $this->hasContractAmcFmDashboardAccess($get);
		if (!$return['has_access']) {
			return $return;
		}

		$sectionKeys = array('previously', 'this_month', 'next_coming');
		$singleSection = trim((string) ($get['section'] ?? ''));
		if ($singleSection != '' && in_array($singleSection, $sectionKeys)) {
			$page = (int) ($get['page'] ?? $get[$singleSection . '_page'] ?? 1);
			$search = trim((string) ($get['search'] ?? $get[$singleSection . '_search'] ?? ''));
			$return = array(
				'has_access' => true,
				'section' => $singleSection,
				$singleSection => $this->getContractAmcFmDashboardSection($get, $singleSection, $page, $search),
			);
			return $return;
		}

		foreach ($sectionKeys as $key) {
			$page = (int) ($get[$key . '_page'] ?? 1);
			$search = trim((string) ($get[$key . '_search'] ?? ''));
			$return[$key] = $this->getContractAmcFmDashboardSection($get, $key, $page, $search);
		}
		return $return;
	}

	function getContractAmcFmScheduleRow($get, $scheduleId)
	{
		global $callConfig;
		$scheduleId = (int) $scheduleId;
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($scheduleId <= 0 || $companyId <= 0) {
			return array();
		}
		$join = $this->buildContractAmcFmDashboardJoin();
		$sql = "SELECT s.*, s.current_status AS visit_status, cad.customer_connect_id,
			c.contract_name, v.display_name AS vendor_name,
			a.asset_code, ca.asset_name AS ca_asset_name
			FROM contract_amc_service_schedules s {$join}
			WHERE s.id = {$scheduleId} AND s.company_id = {$companyId} AND s.is_active = 0 LIMIT 1";
		$row = $callConfig->getRow($sql);
		if (!$row) {
			return array();
		}
		return $this->enrichContractAmcFmVisitRow($row);
	}

	function getContractAmcFmConfirmDetails($get)
	{
		$scheduleId = (int) ($get['schedule_id'] ?? $get['id'] ?? 0);
		$schedule = $this->getContractAmcFmScheduleRow($get, $scheduleId);
		if (count((array)$schedule) == 0) {
			return array('schedule' => array(), 'can_confirm' => false);
		}
		return array(
			'schedule' => $schedule,
			'can_confirm' => $this->canConfirmContractAmcFmVisit($get, $schedule) && !empty($schedule['can_confirm']),
		);
	}

	function canConfirmContractAmcFmVisit($get, $schedule)
	{
		if (($get['admin_type'] ?? '') == '1' || strtoupper($get['working_role_short_code'] ?? '') == 'CONTRACT_ADMIN') {
			return true;
		}
		$userId = (int) ($get['user_id'] ?? 0);
		$connectId = (int) ($schedule['customer_connect_id'] ?? 0);
		if ($userId <= 0 || $connectId <= 0) {
			return false;
		}
		return $connectId == $userId;
	}

	function confirmContractAmcFmVisit($get, $post)
	{
		global $callConfig;
		$return = array('status' => 'error', 'message' => 'Confirmation failed');
		$scheduleId = (int) ($post['schedule_id'] ?? 0);
		if ($scheduleId <= 0) {
			$return['message'] = 'Invalid schedule';
			return $return;
		}

		$schedule = $this->getContractAmcFmScheduleRow($get, $scheduleId);
		if (count((array)$schedule) == 0) {
			$return['message'] = 'Visit not found';
			return $return;
		}
		if (!$this->canConfirmContractAmcFmVisit($get, $schedule)) {
			$return['message'] = 'Not authorized';
			return $return;
		}
		if (empty($schedule['can_confirm'])) {
			$return['message'] = 'Visit is not ready for facility manager confirmation';
			return $return;
		}
		if (!empty($schedule['fm_confirmed_datetime'])) {
			$return['message'] = 'Visit already confirmed';
			return $return;
		}

		$remarks = trim((string) ($post['fm_confirmation_remarks'] ?? ''));
		if ($remarks == '') {
			$return['message'] = 'Remarks are required';
			return $return;
		}
		$fileName = trim((string) ($post['fm_confirmation_file'] ?? ''));
		if ($fileName == '') {
			$return['message'] = 'Job done document is required';
			return $return;
		}

		$now = date('Y-m-d H:i:s');
		$userId = (int) ($get['user_id'] ?? 0);
		$update = array(
			'fm_confirmed_datetime' => $now,
			'fm_confirmed_by' => $userId,
			'fm_confirmation_remarks' => $remarks,
			'fm_confirmation_file' => $fileName,
			'updated_by' => $userId,
			'updated_date' => $now,
		);
		$res = $callConfig->updateRecord('contract_amc_service_schedules', $update, 'id', $scheduleId);
		if (isset($res['status']) && $res['status'] == 'sucess') {
			$attach = array(
				'company_id' => $get['company_id'],
				'application_tablename' => 'contract_amc_service_schedules',
				'transaction_id' => $scheduleId,
				'file_name' => $fileName,
				'is_active' => '0',
				'created_by' => $userId,
				'created_date' => $now,
			);
			$callConfig->insertRecord('application_attachments', $attach);
			$return['status'] = 'success';
			$return['message'] = 'Visit confirmed successfully';
		} else {
			$return['message'] = 'Unable to save confirmation';
		}
		return $return;
	}

	function resolvePortalPartyIds($get)
	{
		global $callConfig;
		$userId = (int) ($get['user_id'] ?? 0);
		$companyId = (int) ($get['company_id'] ?? 0);
		$return = array('customer_ids' => array(), 'vendor_ids' => array());
		if ($userId <= 0 || $companyId <= 0) {
			return $return;
		}
		$cSql = "SELECT id FROM customers WHERE company_id = {$companyId} AND user_id = {$userId} AND is_active = 0";
		$cRows = $callConfig->getAllRows($cSql);
		if (count((array)$cRows) > 0) {
			foreach ($cRows as $row) {
				$r = is_array($row) ? $row : (array) $row;
				$return['customer_ids'][] = $r['id'];
			}
		}
		$vSql = "SELECT id FROM vendors WHERE company_id = {$companyId} AND user_id = {$userId} AND is_active = 0";
		$vRows = $callConfig->getAllRows($vSql);
		if (count((array)$vRows) > 0) {
			foreach ($vRows as $row) {
				$r = is_array($row) ? $row : (array) $row;
				$return['vendor_ids'][] = $r['id'];
			}
		}
		return $return;
	}

	function getAmcPortalHomeData($get)
	{
		$parties = $this->resolvePortalPartyIds($get);
		$return = array(
			'receive' => array('active_contracts' => array(), 'inactive_contracts' => array(), 'active_count' => 0, 'inactive_count' => 0),
			'provide' => array('active_contracts' => array(), 'inactive_contracts' => array(), 'active_count' => 0, 'inactive_count' => 0),
			'dashboard' => array(),
		);

		if (count((array)$parties['customer_ids']) > 0) {
			$allActive = array();
			$allInactive = array();
			foreach ($parties['customer_ids'] as $cid) {
				$res = $this->getPartyContractsList($get, 'customer', $cid);
				$allActive = array_merge($allActive, $res['active_contracts'] ?? array());
				$allInactive = array_merge($allInactive, $res['inactive_contracts'] ?? array());
			}
			$return['receive']['active_contracts'] = $allActive;
			$return['receive']['inactive_contracts'] = $allInactive;
			$return['receive']['active_count'] = count((array)$allActive);
			$return['receive']['inactive_count'] = count((array)$allInactive);
		}

		if (count((array)$parties['vendor_ids']) > 0) {
			$allActive = array();
			$allInactive = array();
			foreach ($parties['vendor_ids'] as $vid) {
				$res = $this->getPartyContractsList($get, 'vendor', $vid);
				$allActive = array_merge($allActive, $res['active_contracts'] ?? array());
				$allInactive = array_merge($allInactive, $res['inactive_contracts'] ?? array());
			}
			$return['provide']['active_contracts'] = $allActive;
			$return['provide']['inactive_contracts'] = $allInactive;
			$return['provide']['active_count'] = count((array)$allActive);
			$return['provide']['inactive_count'] = count((array)$allInactive);
		}

		$return['dashboard'] = $this->getContractAmcDashboardData($get);
		$return['parties'] = $parties;
		$return['fm_has_access'] = $this->hasContractAmcFmDashboardAccess($get);
		return $return;
	}

	function sendContractAmcNotification($get, $schedule, $type, $message, $reminderDay = null)
	{
		$schedule = (array) $schedule;
		$contractId = (int) ($schedule['contract_id'] ?? 0);
		$scheduleId = (int) ($schedule['id'] ?? 0);
		$notarry = array(
			array(
				'company_id' => $get['company_id'],
				'user_id' => $get['user_id'] ?? 1,
				'notification' => $message,
				'ref_table' => 'contract_amc_service_schedules',
				'ref_id' => $scheduleId,
				'current_status' => 0,
				'is_active' => 0,
				'created_by' => $get['user_id'] ?? 1,
				'created_date' => date('Y-m-d H:i:s'),
			),
		);
		$emailarray = array(
			'subject' => 'AMC Visit ' . ucfirst($type),
			'message' => $message,
		);
		$this->sendNotificationAndEmails($notarry, $emailarray, 900);

		global $callConfig;
		$log = array(
			'company_id' => $get['company_id'],
			'contract_amc_service_schedule_id' => $scheduleId,
			'contract_id' => $contractId,
			'notification_type' => $type,
			'reminder_day' => $reminderDay,
			'channel' => 'in_app_email',
			'sent_at' => date('Y-m-d H:i:s'),
			'is_active' => 0,
			'created_date' => date('Y-m-d H:i:s'),
		);
		$callConfig->insertRecord('contract_amc_notification_log', $log);
	}

	// Daily cron: send reminders before upcoming AMC visits based on reminder_days config.
	function runContractAmcReminderCron($get)
	{
		global $callConfig;
		$return = array('sent' => 0, 'skipped' => 0);

		// company_id comes from cron URL / CLI args (e.g. company_id=1).
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($companyId <= 0) {
			return $return;
		}

		$today = date('Y-m-d');

		// Find pending AMC schedules on approved, non-cancelled contracts planned today or in the future.
		$sql = "SELECT s.*, c.contract_name, d.reminder_days
			FROM contract_amc_service_schedules s
			INNER JOIN contracts c ON c.id = s.contract_id AND c.company_id = s.company_id AND c.is_active = 0 AND c.is_amc_contract = 'Y'
				AND c.current_status = '" . ST_APPROVED . "'
				AND (c.cancel_status IS NULL OR c.cancel_status != 'Y')
			INNER JOIN contract_amc_details d ON d.contract_id = s.contract_id AND d.company_id = s.company_id AND d.is_active = 0
			WHERE s.company_id = {$companyId} AND s.is_active = 0 AND s.current_status = 0
			AND DATE(s.planned_service_datetime) >= '{$today}'";
		$rows = $callConfig->getAllRows($sql);
		if (count((array)$rows) > 0) {
			foreach ($rows as $row) {
				$s = is_array($row) ? $row : (array) $row;

				// Read reminder offsets from contract AMC settings (e.g. [7, 3, 1] = 7/3/1 days before visit).
				$days = json_decode($s['reminder_days'] ?? '[]', true);
				if (!is_array($days) || count((array)$days) == 0) {
					$return['skipped']++;
					continue;
				}

				// Calculate how many days remain until the planned visit.
				$plannedDate = substr($s['planned_service_datetime'], 0, 10);
				$diff = (int) ((strtotime($plannedDate) - strtotime($today)) / 86400);

				// Skip if today is not one of the configured reminder days.
				if (!in_array($diff, $days)) {
					$return['skipped']++;
					continue;
				}

				// Send each reminder only once per schedule and reminder_day (check notification log).
				$logSql = "SELECT id FROM contract_amc_notification_log
					WHERE contract_amc_service_schedule_id = '" . addslashes($s['id']) . "'
					AND notification_type = 'reminder' AND reminder_day = {$diff} AND is_active = 0 LIMIT 1";
				$exists = $callConfig->getRow($logSql);
				if ($exists) {
					$return['skipped']++;
					continue;
				}

				// Notify user (in-app + email) and log the reminder.
				$msg = 'AMC visit reminder: ' . ($s['contract_name'] ?? '') . ' planned on ' . $plannedDate;
				$this->sendContractAmcNotification($get, $s, 'reminder', $msg, $diff);
				$return['sent']++;
			}
		}
		return $return;
	}

	function parseContractAmcEscalationConfigRules($escalationConfigJson)
	{
		$rules = json_decode($escalationConfigJson ?? '[]', true);
		if (!is_array($rules)) {
			$rules = array();
		}
		if (count((array)$rules) > 0 && !isset($rules[0]) && (isset($rules['hours']) || isset($rules['not_started_hours']))) {
			$rules = array(array(
				'condition' => 'not_started',
				'hours' => (int) ($rules['hours'] ?? $rules['not_started_hours'] ?? 4),
				'notify_role' => 'service_manager',
			));
		}
		return $rules;
	}

	function resolveContractAmcEscalationNotifyUserIds($get, $companyId, $scheduleRow, $notifyRole, &$roleUserCache)
	{
		global $callConfig;
		$scheduleRow = (array) $scheduleRow;
		// TODO: replace with SQL from new notify-role table when available.
		$amcEscalationNotifyRoles = array(
			'service_manager' => 'service_manager',
			'regional_manager' => 'regional_manager',
			'contract_owner' => 'contract_owner',
		);
		if (!isset($amcEscalationNotifyRoles[$notifyRole])) {
			$notifyRole = 'service_manager';
		}
		$notifyUserIds = array();
		if ($notifyRole == 'contract_owner' && (int) ($scheduleRow['owner_user_id'] ?? 0) > 0) {
			$notifyUserIds[] = (int) $scheduleRow['owner_user_id'];
		} else {
			$roleCode = strtoupper($amcEscalationNotifyRoles[$notifyRole]);
			$cacheKey = $companyId . '_' . $roleCode;
			if (isset($roleUserCache[$cacheKey])) {
				$notifyUserIds = $roleUserCache[$cacheKey];
			} else {
				$roleSql = "SELECT DISTINCT user_id FROM application_user_module_roles
					WHERE company_id = {$companyId} AND role_short_code = '" . addslashes($roleCode) . "' AND is_active = 0";
				$roleRows = $callConfig->getAllRows($roleSql);
				if (count((array)$roleRows) > 0) {
					foreach ($roleRows as $roleRow) {
						$uid = (int) (is_array($roleRow) ? ($roleRow['user_id'] ?? 0) : ($roleRow->user_id ?? 0));
						if ($uid > 0) {
							$notifyUserIds[] = $uid;
						}
					}
				}
				$roleUserCache[$cacheKey] = $notifyUserIds;
			}
		}
		if (count((array)$notifyUserIds) == 0) {
			$notifyUserIds[] = (int) ($get['user_id'] ?? 1);
		}
		return $notifyUserIds;
	}

	// Block A: multiple not_started escalation tiers per schedule.
	function processContractAmcEscalationNotStarted($get, $companyId, &$roleUserCache)
	{
		global $callConfig;
		$return = array('sent' => 0, 'skipped' => 0);

		$contractJoin = "INNER JOIN contracts c ON c.id = s.contract_id AND c.company_id = s.company_id AND c.is_active = 0 AND c.is_amc_contract = 'Y'
				AND c.current_status = '" . ST_APPROVED . "'
				AND (c.cancel_status IS NULL OR c.cancel_status != 'Y')
			INNER JOIN contract_amc_details d ON d.contract_id = s.contract_id AND d.company_id = s.company_id AND d.is_active = 0";

		$sql = "SELECT s.*, c.contract_name, c.owner_user_id, d.escalation_config
			FROM contract_amc_service_schedules s
			{$contractJoin}
			WHERE s.company_id = {$companyId} AND s.is_active = 0 AND s.current_status = 0
			AND s.planned_service_datetime < NOW() AND (s.actual_start_datetime IS NULL OR s.actual_start_datetime = '')";
		$rows = $callConfig->getAllRows($sql);
		if (count((array)$rows) > 0) {
			foreach ($rows as $row) {
				$s = is_array($row) ? $row : (array) $row;
				$rules = $this->parseContractAmcEscalationConfigRules($s['escalation_config'] ?? '[]');
				$hasNotStartedRule = false;
				if (count((array)$rules) > 0) {
					foreach ($rules as $rule) {
						if (!is_array($rule) || ($rule['condition'] ?? '') != 'not_started') {
							continue;
						}
						$hasNotStartedRule = true;
						$hours = (int) ($rule['hours'] ?? 0);
						if ($hours <= 0) {
							$return['skipped']++;
							continue;
						}
						$plannedTs = strtotime($s['planned_service_datetime']);
						if ($plannedTs === false || (time() - $plannedTs) < ($hours * 3600)) {
							$return['skipped']++;
							continue;
						}
						$logSql = "SELECT id FROM contract_amc_notification_log
							WHERE contract_amc_service_schedule_id = '" . addslashes($s['id']) . "'
							AND notification_type = 'escalation_not_started' AND reminder_day = {$hours} AND is_active = 0 LIMIT 1";
						$exists = $callConfig->getRow($logSql);
						if ($exists) {
							$return['skipped']++;
							continue;
						}
						$notifyRole = $rule['notify_role'] ?? 'service_manager';
						$notifyUserIds = $this->resolveContractAmcEscalationNotifyUserIds($get, $companyId, $s, $notifyRole, $roleUserCache);
						$msg = 'AMC visit escalation: ' . ($s['contract_name'] ?? '') . ' not started within ' . $hours . ' hours';
						if (count((array)$notifyUserIds) > 0) {
							foreach ($notifyUserIds as $uid) {
								$notifyGet = $get;
								$notifyGet['user_id'] = $uid;
								$this->sendContractAmcNotification($notifyGet, $s, 'escalation_not_started', $msg, $hours);
							}
						}
						$return['sent']++;
					}
				}
				if (!$hasNotStartedRule && count((array)$rules) > 0) {
					$return['skipped']++;
				}
			}
		}
		return $return;
	}

	// Block B: multiple not_completed escalation tiers per schedule.
	function processContractAmcEscalationNotCompleted($get, $companyId, &$roleUserCache)
	{
		global $callConfig;
		$return = array('sent' => 0, 'skipped' => 0);

		$contractJoin = "INNER JOIN contracts c ON c.id = s.contract_id AND c.company_id = s.company_id AND c.is_active = 0 AND c.is_amc_contract = 'Y'
				AND c.current_status = '" . ST_APPROVED . "'
				AND (c.cancel_status IS NULL OR c.cancel_status != 'Y')
			INNER JOIN contract_amc_details d ON d.contract_id = s.contract_id AND d.company_id = s.company_id AND d.is_active = 0";

		$sql = "SELECT s.*, c.contract_name, c.owner_user_id, d.escalation_config
			FROM contract_amc_service_schedules s
			{$contractJoin}
			WHERE s.company_id = {$companyId} AND s.is_active = 0 AND s.current_status < 199
			AND s.actual_start_datetime IS NOT NULL AND s.actual_start_datetime != ''
			AND (s.actual_end_datetime IS NULL OR s.actual_end_datetime = '')";
		$rows = $callConfig->getAllRows($sql);
		if (count((array)$rows) > 0) {
			foreach ($rows as $row) {
				$s = is_array($row) ? $row : (array) $row;
				$rules = $this->parseContractAmcEscalationConfigRules($s['escalation_config'] ?? '[]');
				$hasNotCompletedRule = false;
				if (count((array)$rules) > 0) {
					foreach ($rules as $rule) {
						if (!is_array($rule) || ($rule['condition'] ?? '') != 'not_completed') {
							continue;
						}
						$hasNotCompletedRule = true;
						$hours = (int) ($rule['hours'] ?? 0);
						if ($hours <= 0) {
							$return['skipped']++;
							continue;
						}
						$startedTs = strtotime($s['actual_start_datetime']);
						if ($startedTs === false || (time() - $startedTs) < ($hours * 3600)) {
							$return['skipped']++;
							continue;
						}
						$logSql = "SELECT id FROM contract_amc_notification_log
							WHERE contract_amc_service_schedule_id = '" . addslashes($s['id']) . "'
							AND notification_type = 'escalation_not_completed' AND reminder_day = {$hours} AND is_active = 0 LIMIT 1";
						$exists = $callConfig->getRow($logSql);
						if ($exists) {
							$return['skipped']++;
							continue;
						}
						$notifyRole = $rule['notify_role'] ?? 'service_manager';
						$notifyUserIds = $this->resolveContractAmcEscalationNotifyUserIds($get, $companyId, $s, $notifyRole, $roleUserCache);
						$msg = 'AMC visit escalation: ' . ($s['contract_name'] ?? '') . ' not completed within ' . $hours . ' hours';
						if (count((array)$notifyUserIds) > 0) {
							foreach ($notifyUserIds as $uid) {
								$notifyGet = $get;
								$notifyGet['user_id'] = $uid;
								$this->sendContractAmcNotification($notifyGet, $s, 'escalation_not_completed', $msg, $hours);
							}
						}
						$return['sent']++;
					}
				}
				if (!$hasNotCompletedRule && count((array)$rules) > 0) {
					$return['skipped']++;
				}
			}
		}
		return $return;
	}

	// Hourly cron: send escalation alerts when AMC visits are late (not started / not completed).
	function runContractAmcEscalationCron($get)
	{
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($companyId <= 0) {
			return array('sent' => 0, 'skipped' => 0);
		}
		$roleUserCache = array();
		$notStarted = $this->processContractAmcEscalationNotStarted($get, $companyId, $roleUserCache);
		$notCompleted = $this->processContractAmcEscalationNotCompleted($get, $companyId, $roleUserCache);
		return array(
			'sent' => (int) ($notStarted['sent'] ?? 0) + (int) ($notCompleted['sent'] ?? 0),
			'skipped' => (int) ($notStarted['skipped'] ?? 0) + (int) ($notCompleted['skipped'] ?? 0),
		);
	}

	function getPpmInhouseReportData($get)
	{
		global $callConfig;
		$return = array(
			'filters' => array('categories' => array(), 'locations' => array(), 'years' => array()),
			'rows' => array(),
			'year' => '',
		);
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($companyId <= 0) {
			return $return;
		}

		$inhouseJoin = "INNER JOIN contracts c ON c.id = s.contract_id AND c.company_id = s.company_id
			AND c.is_active = 0 AND c.is_amc_contract = 'Y' AND c.party_type = 'inhouse'
			AND c.current_status = '" . ST_APPROVED . "'
			AND (c.cancel_status IS NULL OR c.cancel_status != 'Y')
			LEFT JOIN contract_amc_details cad ON cad.contract_id = c.id AND cad.company_id = s.company_id
			AND cad.is_active = 0
			INNER JOIN contract_amc_assets ca ON ca.id = s.contract_amc_asset_id AND ca.company_id = s.company_id AND ca.is_active = 0
			INNER JOIN assets a ON a.id = s.asset_id AND a.company_id = s.company_id AND a.is_active = 0
			LEFT JOIN asset_categories ac ON ac.id = a.category_id AND ac.company_id = a.company_id AND ac.is_active = 0
			INNER JOIN contract_amc_locations cal ON cal.id = s.contract_amc_location_id AND cal.company_id = s.company_id AND cal.is_active = 0
			LEFT JOIN locations l ON l.id = cal.location_id AND l.company_id = cal.company_id AND l.is_active = 0";

		$catSql = "SELECT DISTINCT ac.id, ac.category_description AS name
			FROM contract_amc_service_schedules s
			{$inhouseJoin}
			WHERE s.company_id = {$companyId} AND s.is_active = 0
			AND ac.category_description IS NOT NULL AND ac.category_description != ''
			ORDER BY ac.category_description ASC";
		$catRows = $callConfig->getAllRows($catSql);
		if (count((array)$catRows) > 0) {
			foreach ($catRows as $catRow) {
				$r = is_array($catRow) ? $catRow : (array) $catRow;
				$return['filters']['categories'][] = array(
					'id' => (int) ($r['id'] ?? 0),
					'name' => $r['name'] ?? '',
				);
			}
		}

		$locSql = "SELECT DISTINCT l.id, l.location_name AS name
			FROM contract_amc_service_schedules s
			{$inhouseJoin}
			WHERE s.company_id = {$companyId} AND s.is_active = 0
			AND l.location_name IS NOT NULL AND l.location_name != ''
			ORDER BY l.location_name ASC";
		$locRows = $callConfig->getAllRows($locSql);
		if (count((array)$locRows) > 0) {
			foreach ($locRows as $locRow) {
				$r = is_array($locRow) ? $locRow : (array) $locRow;
				$return['filters']['locations'][] = array(
					'id' => (int) ($r['id'] ?? 0),
					'name' => $r['name'] ?? '',
				);
			}
		}

		$yearSql = "SELECT DISTINCT YEAR(s.planned_service_datetime) AS yr
			FROM contract_amc_service_schedules s
			{$inhouseJoin}
			WHERE s.company_id = {$companyId} AND s.is_active = 0
			AND s.planned_service_datetime IS NOT NULL AND s.planned_service_datetime != ''
			ORDER BY yr DESC";
		$yearRows = $callConfig->getAllRows($yearSql);
		$years = array();
		if (count((array)$yearRows) > 0) {
			foreach ($yearRows as $yearRow) {
				$r = is_array($yearRow) ? $yearRow : (array) $yearRow;
				if (!empty($r['yr'])) {
					$years[] = (int) $r['yr'];
				}
			}
		}
		if (count((array)$years) == 0) {
			$cur = (int) date('Y');
			for ($y = 2019; $y <= ($cur + 1); $y++) {
				$years[] = $y;
			}
			rsort($years);
		}
		$return['filters']['years'] = $years;

		$year = trim((string) ($get['year'] ?? ''));
		if ($year == '' || !is_numeric($year)) {
			return $return;
		}
		$return['year'] = $year;
		$categoryId = (int) ($get['category_id'] ?? 0);
		$locationId = (int) ($get['location_id'] ?? 0);

		$where = "s.company_id = {$companyId} AND s.is_active = 0
			AND YEAR(s.planned_service_datetime) = '" . addslashes($year) . "'";
		if ($categoryId > 0) {
			$where .= " AND ac.id = {$categoryId}";
		}
		if ($locationId > 0) {
			$where .= " AND l.id = {$locationId}";
		}

		$sql = "SELECT s.id, s.planned_service_datetime, s.actual_start_datetime,
			ca.id AS contract_amc_asset_id, ca.asset_name, ca.service_frequency,
			a.id AS asset_id, a.asset_code, a.old_ref_no_1 AS equipment_asset_no,
			ac.category_description AS category_name, l.location_name
			FROM contract_amc_service_schedules s
			{$inhouseJoin}
			WHERE {$where}
			ORDER BY l.location_name ASC, a.asset_code ASC, s.planned_service_datetime ASC";
		$scheduleRows = $callConfig->getAllRows($sql);

		$grouped = array();
		if (count((array)$scheduleRows) > 0) {
			foreach ($scheduleRows as $scheduleRow) {
				$row = is_array($scheduleRow) ? $scheduleRow : (array) $scheduleRow;
				$assetKey = (int) ($row['contract_amc_asset_id'] ?? 0);
				if ($assetKey <= 0) {
					$assetKey = (int) ($row['asset_id'] ?? 0);
				}
				if ($assetKey <= 0) {
					continue;
				}
				if (!isset($grouped[$assetKey])) {
					$emptyMonths = array();
					for ($m = 1; $m <= 12; $m++) {
						$mk = str_pad((string) $m, 2, '0', STR_PAD_LEFT);
						$emptyMonths[$mk] = array('planned' => '', 'actual_start' => '');
					}
					$grouped[$assetKey] = array(
						'equipment_asset_no' => $row['equipment_asset_no'] ?? '',
						'asset_code' => $row['asset_code'] ?? '',
						'equipment_name' => $row['asset_name'] ?? '',
						'category_name' => $row['category_name'] ?? '',
						'location_name' => $row['location_name'] ?? '',
						'frequency' => $row['service_frequency'] ?? '',
						'months' => $emptyMonths,
					);
				}
				$planned = trim((string) ($row['planned_service_datetime'] ?? ''));
				if ($planned == '') {
					continue;
				}
				$monthKey = date('m', strtotime($planned));
				if ($monthKey == '' || date('Y', strtotime($planned)) != $year) {
					continue;
				}
				// If multiple visits in the same month, keep the latest planned visit.
				$existingPlanned = $grouped[$assetKey]['months'][$monthKey]['planned'] ?? '';
				if ($existingPlanned != '' && strtotime($existingPlanned) >= strtotime($planned)) {
					continue;
				}
				$grouped[$assetKey]['months'][$monthKey] = array(
					'planned' => $planned,
					'actual_start' => trim((string) ($row['actual_start_datetime'] ?? '')),
				);
			}
		}

		$return['rows'] = array_values($grouped);
		return $return;
	}

	function getVendorAmcTrackerReportData($get)
	{
		global $callConfig;
		$return = array(
			'filters' => array('vendors' => array(), 'categories' => array(), 'years' => array()),
			'categories' => array(),
			'year' => '',
			'period_label' => '',
		);
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($companyId <= 0) {
			return $return;
		}

		$vendorJoin = "INNER JOIN contracts c ON c.id = s.contract_id AND c.company_id = s.company_id
			AND c.is_active = 0 AND c.is_amc_contract = 'Y' AND c.party_type = 'vendor'
			AND c.current_status = '" . ST_APPROVED . "'
			AND (c.cancel_status IS NULL OR c.cancel_status != 'Y')
			LEFT JOIN contract_amc_details cad ON cad.contract_id = c.id AND cad.company_id = s.company_id AND cad.is_active = 0
			LEFT JOIN vendors v ON v.id = c.party_id AND c.party_type = 'vendor' AND v.company_id = c.company_id AND v.is_active = 0
			INNER JOIN contract_amc_assets ca ON ca.id = s.contract_amc_asset_id AND ca.company_id = s.company_id AND ca.is_active = 0
			LEFT JOIN assets a ON a.id = s.asset_id AND a.company_id = s.company_id AND a.is_active = 0
			LEFT JOIN asset_categories ac ON ac.id = a.category_id AND ac.company_id = a.company_id AND ac.is_active = 0";

		$vendorSql = "SELECT DISTINCT v.id, v.display_name AS name
			FROM contract_amc_service_schedules s
			{$vendorJoin}
			WHERE s.company_id = {$companyId} AND s.is_active = 0
			AND v.display_name IS NOT NULL AND v.display_name != ''
			ORDER BY v.display_name ASC";
		$vendorRows = $callConfig->getAllRows($vendorSql);
		if (count((array)$vendorRows) > 0) {
			foreach ($vendorRows as $vendorRow) {
				$r = is_array($vendorRow) ? $vendorRow : (array) $vendorRow;
				$return['filters']['vendors'][] = array(
					'id' => (int) ($r['id'] ?? 0),
					'name' => $r['name'] ?? '',
				);
			}
		}

		$vendorIdForFilters = (int) ($get['vendor_id'] ?? 0);
		$vendorFilterSql = '';
		if ($vendorIdForFilters > 0) {
			$vendorFilterSql = " AND c.party_id = {$vendorIdForFilters} AND c.party_type = 'vendor'";
		}

		$catSql = "SELECT DISTINCT ac.id, ac.category_description AS name
			FROM contract_amc_service_schedules s
			{$vendorJoin}
			WHERE s.company_id = {$companyId} AND s.is_active = 0{$vendorFilterSql}
			AND ac.category_description IS NOT NULL AND ac.category_description != ''
			ORDER BY ac.category_description ASC";
		$catRows = $callConfig->getAllRows($catSql);
		if (count((array)$catRows) > 0) {
			foreach ($catRows as $catRow) {
				$r = is_array($catRow) ? $catRow : (array) $catRow;
				$return['filters']['categories'][] = array(
					'id' => (int) ($r['id'] ?? 0),
					'name' => $r['name'] ?? '',
				);
			}
		}

		$yearSql = "SELECT DISTINCT CASE
				WHEN MONTH(s.planned_service_datetime) >= 4 THEN YEAR(s.planned_service_datetime)
				ELSE YEAR(s.planned_service_datetime) - 1
			END AS fy
			FROM contract_amc_service_schedules s
			{$vendorJoin}
			WHERE s.company_id = {$companyId} AND s.is_active = 0{$vendorFilterSql}
			AND s.planned_service_datetime IS NOT NULL AND s.planned_service_datetime != ''
			ORDER BY fy DESC";
		$yearRows = $callConfig->getAllRows($yearSql);
		$years = array();
		if (count((array)$yearRows) > 0) {
			foreach ($yearRows as $yearRow) {
				$r = is_array($yearRow) ? $yearRow : (array) $yearRow;
				if (!empty($r['fy'])) {
					$years[] = (int) $r['fy'];
				}
			}
		}
		if (count((array)$years) == 0) {
			$cur = (int) date('Y');
			for ($y = 2019; $y <= ($cur + 1); $y++) {
				$years[] = $y;
			}
			rsort($years);
		}
		$return['filters']['years'] = $years;

		$freqLabelMap = $this->buildVendorAmcFrequencyLabelMap($companyId);

		$vendorId = (int) ($get['vendor_id'] ?? 0);
		if ($vendorId <= 0) {
			return $return;
		}

		$year = trim((string) ($get['year'] ?? ''));
		$hasYearFilter = ($year != '' && is_numeric($year));
		$fiscalStartYear = $hasYearFilter ? (int) $year : 0;
		if ($hasYearFilter) {
			$return['year'] = (string) $fiscalStartYear;
			$return['period_label'] = $this->vendorAmcTrackerPeriodLabel($fiscalStartYear);
		}

		$categoryId = (int) ($get['category_id'] ?? 0);

		$where = "s.company_id = {$companyId} AND s.is_active = 0
			AND c.party_id = {$vendorId} AND c.party_type = 'vendor'";
		if ($hasYearFilter) {
			$fyStart = $fiscalStartYear . '-04-01';
			$fyEnd = ($fiscalStartYear + 1) . '-03-31 23:59:59';
			$where .= " AND s.planned_service_datetime >= '" . addslashes($fyStart) . "'
				AND s.planned_service_datetime <= '" . addslashes($fyEnd) . "'";
		}
		if ($categoryId > 0) {
			$where .= " AND ac.id = {$categoryId}";
		}

		$sql = "SELECT s.id, s.planned_service_datetime, s.actual_start_datetime,
			c.id AS contract_id, c.contract_name, c.transaction_no, c.party_id AS vendor_id,
			v.display_name AS vendor_name,
			ca.id AS contract_amc_asset_id, ca.asset_name, ca.service_frequency,
			ac.id AS category_id, ac.category_description AS category_name
			FROM contract_amc_service_schedules s
			{$vendorJoin}
			WHERE {$where}
			ORDER BY ac.category_description ASC, v.display_name ASC, c.transaction_no ASC, ca.asset_name ASC, s.planned_service_datetime ASC";
		$scheduleRows = $callConfig->getAllRows($sql);

		$fiscalMonths = $this->vendorAmcTrackerFiscalMonthOrder();
		$emptyMonths = array();
		if (count((array)$fiscalMonths) > 0) {
			foreach ($fiscalMonths as $mk) {
				$emptyMonths[$mk] = array('planned' => '', 'actual_start' => '');
			}
		}

		$categoryMap = array();
		if (count((array)$scheduleRows) > 0) {
			foreach ($scheduleRows as $scheduleRow) {
				$row = is_array($scheduleRow) ? $scheduleRow : (array) $scheduleRow;
				$catId = (int) ($row['category_id'] ?? 0);
				$catName = trim((string) ($row['category_name'] ?? ''));
				if ($catName == '') {
					$catName = 'Uncategorized';
					$catId = 0;
				}
				$contractId = (int) ($row['contract_id'] ?? 0);
				$assetKey = (int) ($row['contract_amc_asset_id'] ?? 0);
				if ($contractId <= 0 || $assetKey <= 0) {
					continue;
				}

				if (!isset($categoryMap[$catId])) {
					$categoryMap[$catId] = array(
						'category_id' => $catId,
						'category_name' => $catName,
						'blocks' => array(),
					);
				}

				$planned = trim((string) ($row['planned_service_datetime'] ?? ''));
				if ($planned == '') {
					continue;
				}
				$visitFiscalYear = $hasYearFilter ? $fiscalStartYear : $this->vendorAmcFiscalStartYearFromDate($planned);
				if ($visitFiscalYear <= 0) {
					continue;
				}
				$blockKey = $contractId . '_' . $visitFiscalYear;
				if (!isset($categoryMap[$catId]['blocks'][$blockKey])) {
					$categoryMap[$catId]['blocks'][$blockKey] = array(
						'contract_id' => $contractId,
						'contract_number' => trim((string) (($row['transaction_no'] ?? '') != '' ? $row['transaction_no'] : ($row['contract_name'] ?? ''))),
						'vendor_id' => (int) ($row['vendor_id'] ?? 0),
						'vendor_name' => $row['vendor_name'] ?? '',
						'fiscal_start_year' => $visitFiscalYear,
						'period_label' => $this->vendorAmcTrackerPeriodLabel($visitFiscalYear),
						'rows' => array(),
					);
				}
				if (!isset($categoryMap[$catId]['blocks'][$blockKey]['rows'][$assetKey])) {
					$categoryMap[$catId]['blocks'][$blockKey]['rows'][$assetKey] = array(
						'equipment_name' => $row['asset_name'] ?? '',
						'frequency' => $row['service_frequency'] ?? '',
						'frequency_label' => $this->vendorAmcFrequencyLabel($row['service_frequency'] ?? '', $freqLabelMap),
						'months' => $emptyMonths,
					);
				}
				$monthKey = $this->vendorAmcFiscalMonthKey($planned, $visitFiscalYear);
				if ($monthKey == '') {
					continue;
				}
				$existingPlanned = $categoryMap[$catId]['blocks'][$blockKey]['rows'][$assetKey]['months'][$monthKey]['planned'] ?? '';
				if ($existingPlanned != '' && strtotime($existingPlanned) >= strtotime($planned)) {
					continue;
				}
				$categoryMap[$catId]['blocks'][$blockKey]['rows'][$assetKey]['months'][$monthKey] = array(
					'planned' => $planned,
					'actual_start' => trim((string) ($row['actual_start_datetime'] ?? '')),
				);
			}
		}

		$categories = array();
		if (count((array)$categoryMap) > 0) {
			foreach ($categoryMap as $catData) {
				$blocks = array();
				if (count((array)($catData['blocks'] ?? array())) > 0) {
					foreach ($catData['blocks'] as $blockData) {
						$assetRows = array_values($blockData['rows'] ?? array());
						if (count((array)$assetRows) > 0) {
							$blockData['rows'] = $assetRows;
							$blocks[] = $blockData;
						}
					}
				}
				if (count((array)$blocks) > 1) {
					usort($blocks, function ($a, $b) {
						$fyA = (int) ($a['fiscal_start_year'] ?? 0);
						$fyB = (int) ($b['fiscal_start_year'] ?? 0);
						if ($fyA == $fyB) {
							$cnA = (string) ($a['contract_number'] ?? '');
							$cnB = (string) ($b['contract_number'] ?? '');
							if ($cnA == $cnB) {
								return 0;
							}
							return ($cnA < $cnB) ? -1 : 1;
						}
						return ($fyA > $fyB) ? -1 : 1;
					});
				}
				if (count((array)$blocks) > 0) {
					$catData['blocks'] = $blocks;
					$categories[] = $catData;
				}
			}
		}
		$return['categories'] = $categories;
		return $return;
	}

	function buildVendorAmcFrequencyLabelMap($companyId)
	{
		global $callConfig;
		$map = array();
		$companyId = (int) $companyId;
		if ($companyId <= 0) {
			return $map;
		}
		$sql = "SELECT genc_detail_code, genc_detail_name FROM general_code_details
			WHERE company_id = {$companyId} AND is_active = 0 AND genc_code = 'AMCSERVICE'
			ORDER BY genc_detail_name ASC";
		$rows = $callConfig->getAllRows($sql);
		$merged = $this->mergeAmcServiceFrequencyOptions($rows);
		if (count((array)$merged) > 0) {
			foreach ($merged as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$code = $this->normalizeAmcServiceOn($row['genc_detail_code'] ?? '');
				if ($code != '') {
					$map[$code] = $row['genc_detail_name'] ?? $code;
				}
			}
		}
		return $map;
	}

	function vendorAmcFrequencyLabel($frequency, $labelMap = array())
	{
		$freq = $this->normalizeAmcServiceOn($frequency);
		if (count((array)$labelMap) > 0 && isset($labelMap[$freq]) && $labelMap[$freq] != '') {
			return $labelMap[$freq];
		}
		$canonical = $this->mergeAmcServiceFrequencyOptions(array());
		if (count((array)$canonical) > 0) {
			foreach ($canonical as $row) {
				$code = $this->normalizeAmcServiceOn($row['genc_detail_code'] ?? '');
				if ($code == $freq) {
					return $row['genc_detail_name'] ?? $freq;
				}
			}
		}
		$raw = trim((string) $frequency);
		return $raw != '' ? $raw : '—';
	}

	function vendorAmcFiscalMonthKey($plannedDatetime, $fiscalStartYear)
	{
		$plannedDatetime = trim((string) $plannedDatetime);
		if ($plannedDatetime == '') {
			return '';
		}
		$ts = strtotime($plannedDatetime);
		if ($ts === false) {
			return '';
		}
		$month = date('m', $ts);
		$calYear = (int) date('Y', $ts);
		$fiscalStartYear = (int) $fiscalStartYear;
		if ($month >= '04') {
			if ($calYear != $fiscalStartYear) {
				return '';
			}
		} else {
			if ($calYear != ($fiscalStartYear + 1)) {
				return '';
			}
		}
		return $month;
	}

	function vendorAmcFiscalStartYearFromDate($plannedDatetime)
	{
		$plannedDatetime = trim((string) $plannedDatetime);
		if ($plannedDatetime == '') {
			return 0;
		}
		$ts = strtotime($plannedDatetime);
		if ($ts === false) {
			return 0;
		}
		$month = (int) date('m', $ts);
		$calYear = (int) date('Y', $ts);
		if ($month >= 4) {
			return $calYear;
		}
		return $calYear - 1;
	}

	function vendorAmcScheduleFormatAssetLocation($assetName, $locationName, $assetCode = '', $assetDescription = '', $equipmentAssetNo = '', $categoryName = '')
	{
		$assetName = trim((string) $assetName);
		$locationName = trim((string) $locationName);
		$assetCode = trim((string) $assetCode);
		$assetDescription = trim((string) $assetDescription);
		$equipmentAssetNo = trim((string) $equipmentAssetNo);
		$categoryName = trim((string) $categoryName);

		$displayName = $assetName;
		$isCategoryLabel = ($categoryName != '' && strcasecmp($displayName, $categoryName) == 0);

		if ($displayName == '' || $isCategoryLabel) {
			if ($assetDescription != '' && ($categoryName == '' || strcasecmp($assetDescription, $categoryName) != 0)) {
				$displayName = $assetDescription;
			} elseif ($equipmentAssetNo != '') {
				$displayName = $equipmentAssetNo;
			} elseif ($assetCode != '') {
				$displayName = $assetCode;
			} elseif ($assetName != '') {
				$displayName = $assetName;
			}
		}

		if ($displayName == '') {
			return '—';
		}
		if ($locationName != '' && stripos($displayName, $locationName) === false) {
			return $displayName . ' (' . $locationName . ')';
		}
		return $displayName;
	}

	function vendorAmcScheduleReportTitle($categoryName, $fiscalStartYear)
	{
		$categoryName = trim((string) $categoryName);
		$fiscalStartYear = (int) $fiscalStartYear;
		if ($categoryName == '' || $fiscalStartYear <= 0) {
			return 'AMC Schedule';
		}
		return $categoryName . ' AMC Schedule-' . $fiscalStartYear . '-' . ($fiscalStartYear + 1);
	}

	function vendorAmcScheduleMonthLabel($monthKey)
	{
		$monthKey = trim((string) $monthKey);
		if ($monthKey == '') {
			return '';
		}
		$ts = strtotime($monthKey . '-01');
		if ($ts === false) {
			return $monthKey;
		}
		return date('F', $ts);
	}

	function vendorAmcScheduleBuildVisitColumnLayout($dateKeys)
	{
		$monthToDates = array();
		if (count((array)$dateKeys) > 0) {
			foreach ($dateKeys as $dateKey) {
				$dateKey = trim((string) $dateKey);
				if ($dateKey == '') {
					continue;
				}
				$ts = strtotime($dateKey);
				if ($ts === false) {
					continue;
				}
				$monthKey = date('Y-m', $ts);
				if (!isset($monthToDates[$monthKey])) {
					$monthToDates[$monthKey] = array();
				}
				$monthToDates[$monthKey][$dateKey] = $dateKey;
			}
		}

		$monthGroups = array();
		$visitColumns = array();
		if (count((array)$monthToDates) > 0) {
			ksort($monthToDates);
			foreach ($monthToDates as $monthKey => $datesMap) {
				$dates = array_values($datesMap);
				sort($dates);
				$monthLabel = $this->vendorAmcScheduleMonthLabel($monthKey);
				$visitCount = count((array)$dates);
				$monthGroups[] = array(
					'month_key' => $monthKey,
					'label' => $monthLabel,
					'colspan' => $visitCount * 2,
					'visit_count' => $visitCount,
				);
				$slot = 0;
				if (count((array)$dates) > 0) {
					foreach ($dates as $dateKey) {
						$slot++;
						$visitColumns[] = array(
							'key' => $dateKey,
							'month_key' => $monthKey,
							'month_label' => $monthLabel,
							'slot' => $slot,
						);
					}
				}
			}
		}

		return array(
			'month_groups' => $monthGroups,
			'visit_columns' => $visitColumns,
		);
	}

	function vendorAmcScheduleMergeVisitCell($existing, $planned, $actualStart)
	{
		$existing = is_array($existing) ? $existing : array('planned' => '', 'actual_start' => '');
		$planned = trim((string) $planned);
		$actualStart = trim((string) $actualStart);
		$existingPlanned = trim((string) ($existing['planned'] ?? ''));
		$existingActual = trim((string) ($existing['actual_start'] ?? ''));

		if ($existingPlanned == '') {
			return array('planned' => $planned, 'actual_start' => $actualStart);
		}
		if ($planned == '') {
			return $existing;
		}

		if ($actualStart != '' && $existingActual == '') {
			return array('planned' => $planned, 'actual_start' => $actualStart);
		}
		if ($existingActual != '' && $actualStart == '') {
			return $existing;
		}

		$existingTs = strtotime($existingPlanned);
		$newTs = strtotime($planned);
		if ($existingTs !== false && $newTs !== false && $newTs < $existingTs) {
			return array(
				'planned' => $planned,
				'actual_start' => ($actualStart != '') ? $actualStart : $existingActual,
			);
		}

		return array(
			'planned' => $existingPlanned,
			'actual_start' => ($existingActual != '') ? $existingActual : $actualStart,
		);
	}

	function getVendorAmcScheduleReportData($get)
	{
		global $callConfig;
		$return = array(
			'filters' => array('vendors' => array(), 'categories' => array(), 'years' => array()),
			'period_label' => '',
			'year' => '',
			'category_name' => '',
			'report_title' => '',
			'visit_columns' => array(),
			'month_groups' => array(),
			'rows' => array(),
		);
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($companyId <= 0) {
			return $return;
		}

		$vendorSql = "SELECT id, display_name AS name FROM vendors
			WHERE company_id = {$companyId} AND is_active = 0
			AND display_name IS NOT NULL AND display_name != ''
			ORDER BY display_name ASC";
		$vendorRows = $callConfig->getAllRows($vendorSql);
		if (count((array)$vendorRows) > 0) {
			foreach ($vendorRows as $vendorRow) {
				$r = is_array($vendorRow) ? $vendorRow : (array) $vendorRow;
				$return['filters']['vendors'][] = array(
					'id' => (int) ($r['id'] ?? 0),
					'name' => $r['name'] ?? '',
				);
			}
		}

		$catSql = "SELECT id, category_description AS name FROM asset_categories
			WHERE company_id = {$companyId} AND is_active = 0
			AND category_description IS NOT NULL AND category_description != ''
			ORDER BY category_description ASC";
		$catRows = $callConfig->getAllRows($catSql);
		if (count((array)$catRows) > 0) {
			foreach ($catRows as $catRow) {
				$r = is_array($catRow) ? $catRow : (array) $catRow;
				$return['filters']['categories'][] = array(
					'id' => (int) ($r['id'] ?? 0),
					'name' => $r['name'] ?? '',
				);
			}
		}

		$cur = (int) date('Y');
		$years = array();
		for ($y = 2019; $y <= ($cur + 1); $y++) {
			$years[] = $y;
		}
		rsort($years);
		$return['filters']['years'] = $years;

		$vendorJoin = "INNER JOIN contracts c ON c.id = s.contract_id AND c.company_id = s.company_id
			AND c.is_active = 0 AND c.is_amc_contract = 'Y' AND c.party_type = 'vendor'
			AND c.current_status = '" . ST_APPROVED . "'
			AND (c.cancel_status IS NULL OR c.cancel_status != 'Y')
			LEFT JOIN contract_amc_details cad ON cad.contract_id = c.id AND cad.company_id = s.company_id AND cad.is_active = 0
			LEFT JOIN vendors v ON v.id = c.party_id AND c.party_type = 'vendor' AND v.company_id = c.company_id AND v.is_active = 0
			INNER JOIN contract_amc_assets ca ON ca.id = s.contract_amc_asset_id AND ca.company_id = s.company_id AND ca.is_active = 0
			INNER JOIN contract_amc_locations cal ON cal.id = s.contract_amc_location_id AND cal.company_id = s.company_id AND cal.is_active = 0
			LEFT JOIN locations l ON l.id = cal.location_id AND l.company_id = cal.company_id AND l.is_active = 0
			LEFT JOIN assets caa ON caa.id = ca.asset_id AND caa.company_id = ca.company_id AND caa.is_active = 0
			LEFT JOIN asset_categories ac ON ac.id = caa.category_id AND ac.company_id = caa.company_id AND ac.is_active = 0";

		$vendorId = (int) ($get['vendor_id'] ?? 0);
		$categoryId = (int) ($get['category_id'] ?? 0);
		$year = trim((string) ($get['year'] ?? ''));
		if ($vendorId <= 0 || $categoryId <= 0 || $year == '' || !is_numeric($year)) {
			return $return;
		}

		$fiscalStartYear = (int) $year;
		$return['year'] = (string) $fiscalStartYear;
		$return['period_label'] = $this->vendorAmcTrackerPeriodLabel($fiscalStartYear);
		$freqLabelMap = $this->buildVendorAmcFrequencyLabelMap($companyId);

		$categoryName = '';
		if (count((array)($return['filters']['categories'] ?? array())) > 0) {
			foreach ($return['filters']['categories'] as $catFilter) {
				if ((int) ($catFilter['id'] ?? 0) == $categoryId) {
					$categoryName = trim((string) ($catFilter['name'] ?? ''));
					break;
				}
			}
		}

		$fyStart = $fiscalStartYear . '-04-01';
		$fyEnd = ($fiscalStartYear + 1) . '-03-31 23:59:59';
		$where = "s.company_id = {$companyId} AND s.is_active = 0
			AND c.party_id = {$vendorId} AND c.party_type = 'vendor'
			AND ac.id = {$categoryId}
			AND s.planned_service_datetime >= '" . addslashes($fyStart) . "'
			AND s.planned_service_datetime <= '" . addslashes($fyEnd) . "'";

		$sql = "SELECT s.id, s.planned_service_datetime, s.actual_start_datetime,
			v.display_name AS vendor_name,
			ca.id AS contract_amc_asset_id, ca.asset_name, ca.service_frequency,
			caa.asset_code, caa.asset_description, caa.old_ref_no_1 AS equipment_asset_no,
			ac.id AS category_id, ac.category_description AS category_name,
			l.location_name
			FROM contract_amc_service_schedules s
			{$vendorJoin}
			WHERE {$where}
			ORDER BY ca.asset_name ASC, l.location_name ASC, s.planned_service_datetime ASC";
		$scheduleRows = $callConfig->getAllRows($sql);

		$visitDateKeys = array();
		$grouped = array();
		if (count((array)$scheduleRows) > 0) {
			foreach ($scheduleRows as $scheduleRow) {
				$row = is_array($scheduleRow) ? $scheduleRow : (array) $scheduleRow;
				if ($categoryName == '') {
					$categoryName = trim((string) ($row['category_name'] ?? ''));
				}
				$assetKey = (int) ($row['contract_amc_asset_id'] ?? 0);
				if ($assetKey <= 0) {
					continue;
				}
				$planned = trim((string) ($row['planned_service_datetime'] ?? ''));
				if ($planned == '') {
					continue;
				}
				$plannedTs = strtotime($planned);
				if ($plannedTs === false) {
					continue;
				}
				$visitKey = date('Y-m-d', $plannedTs);
				if ($visitKey == '') {
					continue;
				}
				$visitDateKeys[$visitKey] = $visitKey;

				if (!isset($grouped[$assetKey])) {
					$grouped[$assetKey] = array(
						'vendor_name' => $row['vendor_name'] ?? '',
						'asset_location' => $this->vendorAmcScheduleFormatAssetLocation(
							$row['asset_name'] ?? '',
							$row['location_name'] ?? '',
							$row['asset_code'] ?? '',
							$row['asset_description'] ?? '',
							$row['equipment_asset_no'] ?? '',
							$row['category_name'] ?? ''
						),
						'frequency_label' => $this->vendorAmcFrequencyLabel($row['service_frequency'] ?? '', $freqLabelMap),
						'visits' => array(),
					);
				}
				$existingCell = $grouped[$assetKey]['visits'][$visitKey] ?? array('planned' => '', 'actual_start' => '');
				$grouped[$assetKey]['visits'][$visitKey] = $this->vendorAmcScheduleMergeVisitCell(
					$existingCell,
					$planned,
					trim((string) ($row['actual_start_datetime'] ?? ''))
				);
			}
		}

		$columnLayout = $this->vendorAmcScheduleBuildVisitColumnLayout(array_values($visitDateKeys));
		$monthGroups = $columnLayout['month_groups'] ?? array();
		$visitColumns = $columnLayout['visit_columns'] ?? array();

		$return['category_name'] = $categoryName;
		$return['report_title'] = $this->vendorAmcScheduleReportTitle($categoryName, $fiscalStartYear);
		$return['month_groups'] = $monthGroups;
		$return['visit_columns'] = $visitColumns;
		$return['rows'] = array_values($grouped);
		return $return;
	}

	function getScheduleVisitPhase($scheduleRow)
	{
		$row = is_array($scheduleRow) ? $scheduleRow : (array) $scheduleRow;
		$status = (int) ($row['visit_status'] ?? $row['current_status'] ?? 0);
		if ($status >= 199 || !empty($row['actual_end_datetime'])) {
			return 'closed';
		}
		if (!empty($row['actual_start_datetime']) || ($status > 0 && $status < 199)) {
			return 'in_progress';
		}
		return 'pending';
	}

	function getScheduleRepairEligibility($scheduleRow, $repairOrderRow)
	{
		$phase = $this->getScheduleVisitPhase($scheduleRow);
		$hasRepair = count((array)$repairOrderRow) > 0 && !empty($repairOrderRow['id']);
		if ($phase == 'pending') {
			return 'hidden';
		}
		if ($phase == 'in_progress') {
			return 'editable';
		}
		if ($hasRepair) {
			return 'readonly';
		}
		return 'hidden';
	}

	function canSaveRepairOrderForSchedule($companyId, $scheduleId, $repairOrderId)
	{
		global $callConfig;
		$companyId = (int) $companyId;
		$scheduleId = (int) $scheduleId;
		$repairOrderId = (int) $repairOrderId;
		if ($scheduleId <= 0) {
			return array('ok' => true, 'message' => '');
		}
		$sql = "SELECT s.*, s.current_status AS visit_status FROM contract_amc_service_schedules s
			WHERE s.id = {$scheduleId} AND s.company_id = {$companyId} AND s.is_active = 0 LIMIT 1";
		$row = $callConfig->getRow($sql);
		if (!$row) {
			return array('ok' => false, 'message' => 'Schedule visit not found');
		}
		$schedule = is_array($row) ? $row : (array) $row;
		if ($this->getScheduleVisitPhase($schedule) != 'in_progress') {
			return array('ok' => false, 'message' => 'Job is closed; repair order cannot be modified.');
		}
		$existing = $this->getRepairOrderByScheduleId($companyId, $scheduleId);
		if (count((array)$existing) > 0 && (int) ($existing['id'] ?? 0) != $repairOrderId) {
			return array('ok' => false, 'message' => 'A repair order already exists for this visit.');
		}
		return array('ok' => true, 'message' => '', 'schedule' => $schedule);
	}

	function resolveContractFromAssetLocation($companyId, $assetId, $locationId)
	{
		global $callConfig;
		$companyId = (int) $companyId;
		$assetId = (int) $assetId;
		$locationId = (int) $locationId;
		if ($companyId <= 0 || $assetId <= 0 || $locationId <= 0) {
			return array();
		}
		$approved = defined('ST_APPROVED') ? (int) ST_APPROVED : 199;
		$sql = "SELECT c.id AS contract_id, c.contract_name, c.party_type, c.party_id,
			cal.id AS contract_amc_location_id, cal.location_id,
			ca.id AS contract_amc_asset_id, ca.asset_id,
			CASE WHEN c.party_type = 'vendor' THEN v.display_name ELSE '' END AS vendor_name
			FROM contract_amc_assets ca
			INNER JOIN contract_amc_locations cal ON cal.id = ca.contract_amc_location_id AND cal.company_id = ca.company_id AND cal.is_active = 0
			INNER JOIN contracts c ON c.id = ca.contract_id AND c.company_id = ca.company_id AND c.is_active = 0
			LEFT JOIN vendors v ON v.id = c.party_id AND c.party_type = 'vendor' AND v.company_id = c.company_id AND v.is_active = 0
			WHERE ca.company_id = {$companyId} AND ca.asset_id = {$assetId} AND cal.location_id = {$locationId}
			AND ca.is_active = 0 AND c.is_amc_contract = 'Y' AND c.current_status = {$approved}
			AND (c.cancel_status IS NULL OR c.cancel_status != 'Y')
			ORDER BY c.end_date DESC, c.id DESC LIMIT 1";
		$row = $callConfig->getRow($sql);
		if (!$row) {
			return array();
		}
		return is_array($row) ? $row : (array) $row;
	}

	function resolveContractFromAssetLocationApi($get)
	{
		$assetId = (int) ($get['asset_id'] ?? 0);
		$locationId = (int) ($get['location_id'] ?? 0);
		$row = $this->resolveContractFromAssetLocation($get['company_id'], $assetId, $locationId);
		return array('contract' => $row, 'found' => count((array)$row) > 0);
	}

	function getAmcContractSchedulesForAsset($get)
	{
		global $callConfig;
		$companyId = (int) ($get['company_id'] ?? 0);
		$contractId = (int) ($get['contract_id'] ?? 0);
		$assetId = (int) ($get['asset_id'] ?? 0);
		$return = array('schedules' => array());
		if ($companyId <= 0 || $contractId <= 0 || $assetId <= 0) {
			return $return;
		}
		$sql = "SELECT s.id, s.planned_service_datetime, s.current_status, s.actual_start_datetime, s.actual_end_datetime,
			ca.asset_id
			FROM contract_amc_service_schedules s
			INNER JOIN contract_amc_assets ca ON ca.id = s.contract_amc_asset_id AND ca.company_id = s.company_id AND ca.is_active = 0
			WHERE s.company_id = {$companyId} AND s.contract_id = {$contractId} AND ca.asset_id = {$assetId} AND s.is_active = 0
			ORDER BY s.planned_service_datetime DESC LIMIT 50";
		$rows = $callConfig->getAllRows($sql);
		$list = array();
		if (count((array)$rows) > 0) {
			foreach ($rows as $r) {
				$row = is_array($r) ? $r : (array) $r;
				$row['planned_display'] = $this->formatAmcScheduleDateTime($row['planned_service_datetime'] ?? '');
				$st = $this->getAmcScheduleStatusLabel($row['current_status'] ?? 0);
				$row['status_label'] = $st['label'];
				$list[] = $row;
			}
		}
		$return['schedules'] = $list;
		return $return;
	}

	function getRepairOrderByScheduleId($companyId, $scheduleId)
	{
		global $callConfig;
		$companyId = (int) $companyId;
		$scheduleId = (int) $scheduleId;
		if ($companyId <= 0 || $scheduleId <= 0) {
			return array();
		}
		$sql = "SELECT * FROM contract_amc_repair_orders
			WHERE company_id = {$companyId} AND schedule_id = {$scheduleId} AND is_active = 0 LIMIT 1";
		$row = $callConfig->getRow($sql);
		if (!$row) {
			return array();
		}
		return is_array($row) ? $row : (array) $row;
	}

	function getRepairOrderLines($companyId, $repairOrderId)
	{
		global $callConfig;
		$companyId = (int) $companyId;
		$repairOrderId = (int) $repairOrderId;
		if ($companyId <= 0 || $repairOrderId <= 0) {
			return array();
		}
		$sql = "SELECT l.*, i.item_name, i.sku, i.item_description,
			a.asset_code, a.asset_description,
			CONCAT(loc.short_name, '-', loc.location_name) AS location_label
			FROM contract_amc_repair_order_lines l
			LEFT JOIN items i ON i.id = l.item_id AND i.company_id = l.company_id AND i.is_active = 0
			LEFT JOIN assets a ON a.id = l.asset_id AND a.company_id = l.company_id AND a.is_active = 0
			LEFT JOIN locations loc ON loc.id = l.location_id AND loc.company_id = l.company_id AND loc.is_active = 0
			WHERE l.company_id = {$companyId} AND l.repair_order_id = {$repairOrderId} AND l.is_active = 0
			ORDER BY l.line_order ASC, l.id ASC";
		$rows = $callConfig->getAllRows($sql);
		$out = array();
		if (count((array)$rows) > 0) {
			foreach ($rows as $r) {
				$out[] = is_array($r) ? $r : (array) $r;
			}
		}
		return $out;
	}

	function getRepairOrderBundleForSchedule($companyId, $scheduleId, $scheduleRow)
	{
		$repairOrder = $this->getRepairOrderByScheduleId($companyId, $scheduleId);
		$lines = array();
		if (count((array)$repairOrder) > 0) {
			$lines = $this->getRepairOrderLines($companyId, (int) $repairOrder['id']);
		}
		$eligibility = $this->getScheduleRepairEligibility($scheduleRow, $repairOrder);
		return array(
			'repair_eligibility' => $eligibility,
			'repair_order' => $repairOrder,
			'repair_order_lines' => $lines,
		);
	}

	function generateRepairOrderTransactionNo($companyId)
	{
		global $callConfig;
		$companyId = (int) $companyId;
		$year = date('Y');
		$row = $callConfig->getRow("SELECT COUNT(id) AS cc FROM contract_amc_repair_orders WHERE company_id = {$companyId} AND YEAR(created_date) = " . (int) $year);
		$seq = (int) (($row->cc ?? 0) + 1);
		return 'RO-' . $year . '-' . sprintf('%05d', $seq);
	}

	function createRepairOrderApprovalLog($get, $repairOrderId, $repairDate)
	{
		global $callConfig;
		$moduleId = (int) ($get['module_id'] ?? $_SESSION['module'] ?? 0);
		$insert_array = array(
			'company_id' => $get['company_id'],
			'transaction_table' => 'contract_amc_repair_orders',
			'transaction_id' => $repairOrderId,
			'previous_status' => 0,
			'request_date' => $repairDate != '' ? $repairDate : date('Y-m-d'),
			'assigner_user_id' => $get['user_id'],
			'user_id' => $get['user_id'],
			'module_setup_id' => $moduleId,
		);
		return $callConfig->insertRecord('transaction_approval_logs', $insert_array);
	}

	function validateRepairOrderLineAssets($companyId, $contractId, $lines)
	{
		$assets = $this->getAmcRepairOrderContractAssetsData($companyId, $contractId);
		$allowedAssetIds = array();
		if (count((array)($assets['assets'] ?? array())) > 0) {
			foreach ($assets['assets'] as $a) {
				$allowedAssetIds[(int) ($a['asset_id'] ?? 0)] = true;
			}
		}
		if (count((array)$lines) == 0) {
			return array('ok' => true, 'message' => '');
		}
		foreach ($lines as $line) {
			$aid = (int) ($line['asset_id'] ?? 0);
			if ($aid > 0 && empty($allowedAssetIds[$aid])) {
				return array('ok' => false, 'message' => 'Invalid asset for this contract.');
			}
		}
		return array('ok' => true, 'message' => '');
	}
	function validateRepairOrderLineItems($companyId, $lines)
	{
		global $callConfig;
		$companyId = (int) $companyId;
		$allowedItemIds = array();
		if ($companyId > 0) {
			$rows = $callConfig->getAllRows("SELECT id FROM items WHERE company_id = {$companyId} AND is_active = 0 AND item_name != '' ");
			if (count((array)$rows) > 0) {
				foreach ($rows as $r) {
					$row = is_array($r) ? $r : (array) $r;
					$allowedItemIds[(int) ($row['id'] ?? 0)] = true;
				}
			}
		}
		if (count((array)$lines) == 0) {
			return array('ok' => true, 'message' => '');
		}
		foreach ($lines as $line) {
			$iid = (int) ($line['item_id'] ?? 0);
			if ($iid > 0 && empty($allowedItemIds[$iid])) {
				return array('ok' => false, 'message' => 'Invalid item.');
			}
		}
		return array('ok' => true, 'message' => '');
	}
	function getAmcRepairOrderItemsData($companyId)
	{
		global $callConfig;
		$companyId = (int) $companyId;
		if ($companyId <= 0) {
			return array();
		}
		$sql = "SELECT id, sku, item_name, item_description
			FROM items
			WHERE company_id = {$companyId} AND is_active = 0  AND item_name != '' 
			ORDER BY item_name ASC";
		$rows = $callConfig->getAllRows($sql);
		$items = array();
		if (count((array)$rows) > 0) {
			foreach ($rows as $r) {
				$items[] = is_array($r) ? $r : (array) $r;
			}
		}
		return $items;
	}

	function getAmcRepairOrderContractAssetsData($companyId, $contractId)
	{
		global $callConfig;
		$companyId = (int) $companyId;
		$contractId = (int) $contractId;
		$return = array('assets' => array(), 'locations' => array());
		if ($companyId <= 0 || $contractId <= 0) {
			return $return;
		}
		$sql = "SELECT ca.asset_id, ca.asset_name, ca.contract_amc_location_id, cal.location_id,
			a.asset_code, a.asset_description,
			CONCAT(l.short_name, '-', l.location_name) AS location_label
			FROM contract_amc_assets ca
			INNER JOIN contract_amc_locations cal ON cal.id = ca.contract_amc_location_id AND cal.company_id = ca.company_id AND cal.is_active = 0
			LEFT JOIN assets a ON a.id = ca.asset_id AND a.company_id = ca.company_id AND a.is_active = 0
			LEFT JOIN locations l ON l.id = cal.location_id AND l.company_id = cal.company_id AND l.is_active = 0
			WHERE ca.company_id = {$companyId} AND ca.contract_id = {$contractId} AND ca.is_active = 0
			ORDER BY cal.id ASC, ca.id ASC";
		$rows = $callConfig->getAllRows($sql);
		$assets = array();
		$locations = array();
		if (count((array)$rows) > 0) {
			foreach ($rows as $r) {
				$row = is_array($r) ? $r : (array) $r;
				$assets[] = $row;
				$locId = (int) ($row['location_id'] ?? 0);
				if ($locId > 0 && !isset($locations[$locId])) {
					$locations[$locId] = array(
						'location_id' => $locId,
						'location_label' => $row['location_label'] ?? '',
						'contract_amc_location_id' => (int) ($row['contract_amc_location_id'] ?? 0),
					);
				}
			}
		}
		$return['assets'] = $assets;
		$return['locations'] = array_values($locations);
		return $return;
	}

	function getAmcRepairOrderContractAssets($get)
	{
		$contractId = (int) ($get['contract_id'] ?? 0);
		return $this->getAmcRepairOrderContractAssetsData($get['company_id'], $contractId);
	}

	function buildAmcRepairOrderScheduleSummary($get, $scheduleId, $schedule = array(), $detail = array())
	{
		global $callConfig;
		$scheduleId = (int) $scheduleId;
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($scheduleId <= 0 || $companyId <= 0) {
			return array();
		}
		if (count((array)$schedule) == 0) {
			$detail = $this->getAmcRepairOrderScheduleDetailsGet($get, $scheduleId);
			$schedule = $detail['schedule'] ?? array();
		}
		if (count((array)$schedule) == 0) {
			return array();
		}
		$locRow = $callConfig->getRow("SELECT cal.location_id, ca.asset_id, ca.asset_name,
			CONCAT(l.short_name, '-', l.location_name) AS location_label,
			CASE WHEN c.party_type = 'vendor' THEN v.display_name ELSE '' END AS vendor_name
			FROM contract_amc_service_schedules s
			LEFT JOIN contract_amc_locations cal ON cal.id = s.contract_amc_location_id AND cal.company_id = s.company_id
			LEFT JOIN contract_amc_assets ca ON ca.id = s.contract_amc_asset_id AND ca.company_id = s.company_id
			LEFT JOIN locations l ON l.id = cal.location_id AND l.company_id = s.company_id AND l.is_active = 0
			LEFT JOIN contracts c ON c.id = s.contract_id AND c.company_id = s.company_id
			LEFT JOIN vendors v ON v.id = c.party_id AND c.party_type = 'vendor' AND v.company_id = c.company_id AND v.is_active = 0
			WHERE s.id = {$scheduleId} AND s.company_id = {$companyId} LIMIT 1");
		$loc = is_array($locRow) ? $locRow : (array) $locRow;
		$planned = $schedule['planned_service_datetime'] ?? '';
		$actualStart = $schedule['actual_start_datetime'] ?? '';
		$actualEnd = $schedule['actual_end_datetime'] ?? '';
		return array(
			'id' => $scheduleId,
			'planned_service_datetime' => $planned,
			'planned_display' => $schedule['planned_display'] ?? $this->formatAmcScheduleDateTime($planned),
			'actual_start_datetime' => $actualStart,
			'actual_start_display' => !empty($actualStart) ? $this->formatAmcScheduleDateTime($actualStart) : '—',
			'actual_end_datetime' => $actualEnd,
			'actual_end_display' => !empty($actualEnd) ? $this->formatAmcScheduleDateTime($actualEnd) : '—',
			'asset_name' => $loc['asset_name'] ?? '',
			'location_label' => $loc['location_label'] ?? '',
			'status_label' => $schedule['status_label'] ?? '',
			'contract_name' => $schedule['contract_name'] ?? '',
			'vendor_name' => $loc['vendor_name'] ?? ($schedule['party_name'] ?? ''),
		);
	}

	function getAmcRepairOrderScheduleDetailsGet($get, $scheduleId)
	{
		$scheduleGet = array_merge((array) $get, array('schedule_id' => (int) $scheduleId));
		unset($scheduleGet['id'], $scheduleGet['repair_order_id']);
		return $this->getContractAmcScheduleDetails($scheduleGet);
	}

	function getAmcRepairOrderFormData($get)
	{
		global $callConfig;
		$companyId = (int) ($get['company_id'] ?? 0);
		$repairOrderId = (int) ($get['repair_order_id'] ?? $get['id'] ?? 0);
		$complaintId = (int) ($get['complaint_id'] ?? 0);
		$scheduleId = (int) ($get['schedule_id'] ?? 0);
		$contractId = (int) ($get['contract_id'] ?? 0);
		$return = array(
			'repair_order' => array(),
			'lines' => array(),
			'contracts' => array(),
		);
		$admin_or_user_whr = "";
		if($get['admin_type'] != 1){
			$admin_or_user_whr = " AND c.created_by='{$get['user_id']}'";
		}

		$approved = defined('ST_APPROVED') ? (int) ST_APPROVED : 199;
		$cSql = "SELECT c.id, c.contract_name, c.party_type, c.party_id,
			CASE WHEN c.party_type = 'vendor' THEN v.display_name ELSE '' END AS vendor_name
			FROM contracts c
			LEFT JOIN vendors v ON v.id = c.party_id AND c.party_type = 'vendor' AND v.company_id = c.company_id AND v.is_active = 0
			WHERE c.company_id = {$companyId} AND c.is_active = 0 AND c.is_amc_contract = 'Y' AND c.current_status = {$approved}
			AND (c.cancel_status IS NULL OR c.cancel_status != 'Y') ".$admin_or_user_whr."
			ORDER BY c.contract_name ASC";
		$return['contracts'] = $callConfig->getAllRows($cSql);

		if ($repairOrderId > 0) {
			$row = $callConfig->getRow("SELECT * FROM contract_amc_repair_orders WHERE id = {$repairOrderId} AND company_id = {$companyId} AND is_active = 0 LIMIT 1");
			if ($row) {
				$return['repair_order'] = is_array($row) ? $row : (array) $row;
				$contractId = (int) ($return['repair_order']['contract_id'] ?? 0);
				$return['lines'] = $this->getRepairOrderLines($companyId, $repairOrderId);
				if ($scheduleId <= 0) {
					$scheduleId = (int) ($return['repair_order']['schedule_id'] ?? 0);
				}
			}
		} elseif ($complaintId > 0) {
			$cRow = $callConfig->getRow("SELECT * FROM complaints WHERE id = {$complaintId} AND company_id = {$companyId} AND is_active = 0 LIMIT 1");
			if ($cRow) {
				$c = is_array($cRow) ? $cRow : (array) $cRow;
				$contractId = (int) ($c['contract_id'] ?? 0);
				$return['repair_order'] = array(
					'contract_id' => $contractId,
					'location_id' => (int) ($c['location_id'] ?? 0),
					'default_asset_id' => (int) ($c['asset_id'] ?? 0),
					'servicenow_ref' => $c['servicenow_ref'] ?? '',
					'complaint_id' => $complaintId,
					'schedule_id' => (int) ($c['schedule_id'] ?? 0),
					'source_type' => 'adhoc_complaint',
					'repair_date' => date('Y-m-d'),
				);
			}
		} elseif ($scheduleId > 0) {
			$detail = $this->getAmcRepairOrderScheduleDetailsGet($get, $scheduleId);
			$schedule = $detail['schedule'] ?? array();
			if (count((array)$schedule) > 0) {
				$locRow = $callConfig->getRow("SELECT cal.location_id, cal.id AS contract_amc_location_id, ca.asset_id
					FROM contract_amc_service_schedules s
					LEFT JOIN contract_amc_locations cal ON cal.id = s.contract_amc_location_id AND cal.company_id = s.company_id
					LEFT JOIN contract_amc_assets ca ON ca.id = s.contract_amc_asset_id AND ca.company_id = s.company_id
					WHERE s.id = {$scheduleId} AND s.company_id = {$companyId} LIMIT 1");
				$loc = is_array($locRow) ? $locRow : (array) $locRow;
				$contractId = (int) ($schedule['contract_id'] ?? 0);
				$existing = $this->getRepairOrderByScheduleId($companyId, $scheduleId);
				if (count((array)$existing) > 0) {
					$return['repair_order'] = $existing;
					$return['lines'] = $this->getRepairOrderLines($companyId, (int) $existing['id']);
				} else {
					$vendorId = ($schedule['party_type'] ?? '') == 'vendor' ? (int) ($schedule['party_id'] ?? 0) : 0;
					$return['repair_order'] = array(
						'contract_id' => $contractId,
						'location_id' => (int) ($loc['location_id'] ?? 0),
						'default_asset_id' => (int) ($loc['asset_id'] ?? 0),
						'contract_amc_location_id' => (int) ($loc['contract_amc_location_id'] ?? 0),
						'vendor_id' => $vendorId,
						'schedule_id' => $scheduleId,
						'source_type' => 'planned_visit',
						'repair_date' => date('Y-m-d'),
					);
				}
				$return['repair_eligibility'] = $detail['repair_eligibility'] ?? 'hidden';
				$return['schedule'] = $this->buildAmcRepairOrderScheduleSummary($get, $scheduleId, $schedule, $detail);
			}
		}

		if ($scheduleId > 0) {
			$detail = $this->getAmcRepairOrderScheduleDetailsGet($get, $scheduleId);
			$scheduleSummary = $this->buildAmcRepairOrderScheduleSummary($get, $scheduleId, $detail['schedule'] ?? array(), $detail);
			if (count((array)$scheduleSummary) > 0) {
				$return['schedule'] = $scheduleSummary;
			}
			if (empty($return['repair_eligibility'])) {
				$return['repair_eligibility'] = $detail['repair_eligibility'] ?? 'hidden';
			}
		}

		if ($contractId > 0) {
			$return['contract_assets'] = $this->getAmcRepairOrderContractAssetsData($companyId, $contractId);
			$cRow = $callConfig->getRow("SELECT c.*, CASE WHEN c.party_type = 'vendor' THEN v.display_name ELSE '' END AS vendor_name
				FROM contracts c
				LEFT JOIN vendors v ON v.id = c.party_id AND c.party_type = 'vendor' AND v.company_id = c.company_id
				WHERE c.id = {$contractId} AND c.company_id = {$companyId} LIMIT 1");
			if ($cRow) {
				$c = is_array($cRow) ? $cRow : (array) $cRow;
				if (empty($return['repair_order']['vendor_id']) && ($c['party_type'] ?? '') == 'vendor') {
					$return['repair_order']['vendor_id'] = (int) ($c['party_id'] ?? 0);
				}
				$return['repair_order']['vendor_name'] = $c['vendor_name'] ?? '';
				$return['repair_order']['contract_name'] = $c['contract_name'] ?? '';
			}
		}
		$return['items'] = $this->getAmcRepairOrderItemsData($companyId);

		return $return;
	}

	function getAmcRepairOrdersList($get)
	{
		global $callConfig;
		$companyId = (int) ($get['company_id'] ?? 0);
		$admin_or_user_whr = "";
		if($get['admin_type'] != 1){
			$admin_or_user_whr = " AND ro.created_by='{$get['user_id']}'";
		}
		$sql = "SELECT ro.*, c.contract_name,
			CASE WHEN c.party_type = 'vendor' THEN v.display_name ELSE '' END AS vendor_name,
			s.name AS status_name, s.background_color, s.color
			FROM contract_amc_repair_orders ro
			LEFT JOIN contracts c ON c.id = ro.contract_id AND c.company_id = ro.company_id
			LEFT JOIN vendors v ON v.id = ro.vendor_id AND v.company_id = ro.company_id
			LEFT JOIN status s ON s.name_value = ro.current_status AND s.company_id = ro.company_id
			WHERE ro.company_id = {$companyId} AND ro.is_active = 0 ".$admin_or_user_whr."
			ORDER BY ro.id DESC";
		return array('rows' => $callConfig->getAllRows($sql));
	}

	function saveAmcRepairOrder($get, $post)
	{
		global $callConfig;
		$return = array('status' => 'error', 'message' => 'Save failed');
		$companyId = (int) ($get['company_id'] ?? 0);
		$userId = (int) ($get['user_id'] ?? 0);
		$now = date('Y-m-d H:i:s');
		$repairOrderId = (int) ($post['repair_order_id'] ?? $post['id'] ?? 0);
		$scheduleId = (int) ($post['schedule_id'] ?? 0);
		$sourceType = trim((string) ($post['source_type'] ?? 'manual'));
		if ($scheduleId > 0) {
			$sourceType = 'planned_visit';
		}

		$gate = $this->canSaveRepairOrderForSchedule($companyId, $scheduleId, $repairOrderId);
		if (empty($gate['ok'])) {
			$return['message'] = $gate['message'];
			return $return;
		}

		$contractId = (int) ($post['contract_id'] ?? 0);
		if ($contractId <= 0) {
			$return['message'] = 'Contract is required';
			return $return;
		}

		$linesRaw = $post['repair_lines'] ?? $post['lines'] ?? array();
		if (!is_array($linesRaw)) {
			$linesRaw = json_decode($linesRaw, true);
		}
		$linesRaw = is_array($linesRaw) ? $linesRaw : array();
		$defaultPo = trim((string) ($post['default_po_number'] ?? ''));

		$parsedLines = array();
		if (count((array)$linesRaw) > 0) {
			$order = 0;
			foreach ($linesRaw as $line) {
				if (!is_array($line)) {
					continue;
				}
				$part = trim((string) ($line['part_description'] ?? ''));
				$itemId = (int) ($line['item_id'] ?? 0);
				if ($part == '' && $itemId <= 0) {
					continue;
				}
				$po = trim((string) ($line['po_number'] ?? ''));
				if ($po == '' && $defaultPo != '') {
					$po = $defaultPo;
				}
				$parsedLines[] = array(
					'item_id' => $itemId,
					'location_id' => (int) ($line['location_id'] ?? $post['location_id'] ?? 0),
					'part_description' => $part,
					'qty' => (float) ($line['qty'] ?? 1),
					'po_number' => $po,
					'amount' => (float) ($line['amount'] ?? 0),
					'line_order' => $order++,
					'line_id' => (int) ($line['line_id'] ?? $line['id'] ?? 0),
				);
			}
		}

		$lineCheck = $this->validateRepairOrderLineItems($companyId, $parsedLines);
		if (empty($lineCheck['ok'])) {
			$return['message'] = $lineCheck['message'];
			return $return;
		}

		$vendorId = (int) ($post['vendor_id'] ?? 0);
		if ($vendorId <= 0) {
			$cRow = $callConfig->getRow("SELECT party_type, party_id FROM contracts WHERE id = {$contractId} AND company_id = {$companyId} LIMIT 1");
			if ($cRow && ($cRow->party_type ?? '') == 'vendor') {
				$vendorId = (int) ($cRow->party_id ?? 0);
			}
		}

		$header = array(
			'contract_id' => $contractId,
			'vendor_id' => $vendorId > 0 ? $vendorId : null,
			'location_id' => (int) ($post['location_id'] ?? 0),
			'default_asset_id' => (int) ($post['default_asset_id'] ?? 0),
			'contract_amc_location_id' => (int) ($post['contract_amc_location_id'] ?? 0),
			'servicenow_ref' => trim((string) ($post['servicenow_ref'] ?? '')),
			'default_po_number' => $defaultPo,
			'repair_date' => !empty($post['repair_date']) ? date('Y-m-d', strtotime($post['repair_date'])) : date('Y-m-d'),
			'source_type' => $sourceType,
			'complaint_id' => (int) ($post['complaint_id'] ?? 0) ?: null,
			'schedule_id' => $scheduleId > 0 ? $scheduleId : null,
			'remarks' => trim((string) ($post['remarks'] ?? '')),
			'updated_by' => $userId,
			'updated_date' => $now,
		);

		$isNew = ($repairOrderId <= 0);
		if ($isNew) {
			$header['company_id'] = $companyId;
			$header['transaction_no'] = $this->generateRepairOrderTransactionNo($companyId);
			$header['is_active'] = 0;
			$header['current_status'] = 0;
			$header['status_date'] = $now;
			$header['created_by'] = $userId;
			$header['created_date'] = $now;
			$res = $callConfig->insertRecord('contract_amc_repair_orders', $header);
			if (empty($res['LASTID'])) {
				$return['message'] = 'Could not create repair order';
				return $return;
			}
			$repairOrderId = (int) $res['LASTID'];
			if ($sourceType != 'planned_visit') {
				$this->createRepairOrderApprovalLog($get, $repairOrderId, $header['repair_date']);
			}
		} else {
			$existing = $callConfig->getRow("SELECT * FROM contract_amc_repair_orders WHERE id = {$repairOrderId} AND company_id = {$companyId} AND is_active = 0 LIMIT 1");
			if (!$existing) {
				$return['message'] = 'Repair order not found';
				return $return;
			}
			$ex = is_array($existing) ? $existing : (array) $existing;
			if ((int) ($ex['schedule_id'] ?? 0) != $scheduleId && (int) ($ex['schedule_id'] ?? 0) > 0) {
				$return['message'] = 'Schedule link cannot be changed';
				return $return;
			}
			if ((int) ($ex['contract_id'] ?? 0) != $contractId) {
				$return['message'] = 'Contract cannot be changed';
				return $return;
			}
			unset($header['source_type']);
			$callConfig->updateRecord('contract_amc_repair_orders', $header, 'id', $repairOrderId);
		}

		$keptLineIds = array();
		if (count((array)$parsedLines) > 0) {
			foreach ($parsedLines as $line) {
				$lineData = array(
					'item_id' => $line['item_id'] > 0 ? $line['item_id'] : null,
					'asset_id' => null,
					'location_id' => $line['location_id'] > 0 ? $line['location_id'] : null,
					'part_description' => $line['part_description'],
					'qty' => $line['qty'],
					'po_number' => $line['po_number'] != '' ? $line['po_number'] : null,
					'amount' => $line['amount'],
					'line_order' => $line['line_order'],
					'updated_by' => $userId,
					'updated_date' => $now,
				);
				if ($line['line_id'] > 0) {
					$callConfig->updateRecord('contract_amc_repair_order_lines', $lineData, 'id', $line['line_id']);
					$keptLineIds[] = $line['line_id'];
				} else {
					$lineData['repair_order_id'] = $repairOrderId;
					$lineData['company_id'] = $companyId;
					$lineData['is_active'] = 0;
					$lineData['created_by'] = $userId;
					$lineData['created_date'] = $now;
					$ins = $callConfig->insertRecord('contract_amc_repair_order_lines', $lineData);
					if (!empty($ins['LASTID'])) {
						$keptLineIds[] = (int) $ins['LASTID'];
					}
				}
			}
		}

		$existingLines = $this->getRepairOrderLines($companyId, $repairOrderId);
		if (count((array)$existingLines) > 0) {
			foreach ($existingLines as $el) {
				$lid = (int) ($el['id'] ?? 0);
				if ($lid > 0 && !in_array($lid, $keptLineIds)) {
					$callConfig->updateRecord('contract_amc_repair_order_lines', array(
						'is_active' => 1,
						'updated_by' => $userId,
						'updated_date' => $now,
					), 'id', $lid);
				}
			}
		}

		$return['status'] = 'success';
		$return['message'] = 'Repair order saved';
		$return['repair_order_id'] = $repairOrderId;
		$return['repair_order'] = $callConfig->getRow("SELECT * FROM contract_amc_repair_orders WHERE id = {$repairOrderId} LIMIT 1");
		$return['lines'] = $this->getRepairOrderLines($companyId, $repairOrderId);
		return $return;
	}
	function getAmcComplaintsDetails($get)
	{
		global $callConfig;
		$return = array();

		$buildings_details = "SELECT id, building_name FROM buildings WHERE company_id='" . $get['company_id'] . "' AND is_active =0 ";
		$buildings_details = $callConfig->getAllRows($buildings_details);
		$buildings = array();
		$i = 1;
		if (count((array)$buildings_details) > 0) {
			foreach ($buildings_details as $row) {
				$buildings[$i] = $row;
				$i++;
			}
		}
		$return['buildings'] = $buildings;

		$items_details = "SELECT id, item_name, item_type FROM items WHERE company_id='" . $get['company_id'] . "' AND is_active =0  AND item_name != '' ";
		$items_details = $callConfig->getAllRows($items_details);
		$items = array();
		$i = 1;
		if (count((array)$items_details) > 0) {
			foreach ($items_details as $row) {
				$items[$i] = $row;
				$i++;
			}
		}
		$return['items'] = $items;

		$assets_details = "SELECT id, asset_code, asset_description FROM assets WHERE company_id='" . $get['company_id'] . "' AND is_active = 0 ";
		$assets_details = $callConfig->getAllRows($assets_details);
		$assets = array();
		$i = 1;
		if (count((array)$assets_details) > 0) {
			foreach ($assets_details as $row) {
				$assets[$i] = $row;
				$i++;
			}
		}
		$return['assets'] = $assets;

		$locationsquery = "SELECT id, concat(short_name,'-',location_name) AS short_name FROM locations WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
		$return['locations'] = $callConfig->getAllRows($locationsquery);

		$admin_or_user_whr = "";
		if($get['admin_type'] != 1){
			$admin_or_user_whr = " AND c.created_by='{$get['user_id']}'";
		}

		$approved = defined('ST_APPROVED') ? (int) ST_APPROVED : 199;
		$contractsSql = "SELECT c.id, c.contract_name, c.party_type,
			CASE WHEN c.party_type = 'vendor' THEN v.display_name ELSE '' END AS vendor_name
			FROM contracts c
			LEFT JOIN vendors v ON v.id = c.party_id AND c.party_type = 'vendor' AND v.company_id = c.company_id AND v.is_active = 0
			WHERE c.company_id = '" . $get['company_id'] . "' AND c.is_active = 0 AND c.is_amc_contract = 'Y'
			AND c.current_status = {$approved} AND (c.cancel_status IS NULL OR c.cancel_status != 'Y') ".$admin_or_user_whr."
			ORDER BY c.contract_name ASC";
		$return['contracts'] = $callConfig->getAllRows($contractsSql);

		$assetsFilterSql = "SELECT DISTINCT a.id, a.asset_code, a.asset_description
			FROM assets a
			INNER JOIN contract_amc_assets ca ON ca.asset_id = a.id AND ca.company_id = a.company_id AND ca.is_active = 0
			INNER JOIN contracts c ON c.id = ca.contract_id AND c.company_id = ca.company_id AND c.is_active = 0 AND c.is_amc_contract = 'Y'
			WHERE a.company_id = '" . $get['company_id'] . "' AND a.is_active = 0 
			ORDER BY a.asset_description ASC";
		$return['filter_assets'] = $callConfig->getAllRows($assetsFilterSql);

		$vendorsSql = "SELECT DISTINCT v.id, v.display_name
			FROM vendors v
			INNER JOIN contracts c ON c.party_id = v.id AND c.party_type = 'vendor' AND c.company_id = v.company_id
			WHERE v.company_id = '" . $get['company_id'] . "' AND v.is_active = 0 AND c.is_amc_contract = 'Y'
			ORDER BY v.display_name ASC";
		$return['filter_vendors'] = $callConfig->getAllRows($vendorsSql);

		return $return;
	}

	function getAmcComplaintsListDetails($get)
	{
		global $callConfig;
		$return = array();
		if ($get['rid'] && $get['rid'] != '') {
			$table_details = "SELECT c.id as id, c.user_id as user_id, c.complaint_name, c.complaint_type, c.complaint_priority, c.transaction_date,
			c.contract_id, c.servicenow_ref, c.schedule_id, c.source_type, c.location_id,
			i.item_name, a.asset_code, a.asset_description, s.name, s.background_color, s.color, b.building_name,bf.floor_name, bfs.cabin_name , au.display_name , c.created_date,
			ct.contract_name, CASE WHEN ct.party_type = 'vendor' THEN v.display_name ELSE '' END AS vendor_name,
			concat(l.short_name,'-',l.location_name) AS location_name,
			sch.planned_service_datetime, sch.current_status AS schedule_status
			FROM complaints c
			LEFT JOIN items i ON i.id = c.item_id
			LEFT JOIN assets a ON a.id = c.asset_id
			LEFT JOIN status s ON s.name_value = c.current_status
			LEFT JOIN buildings b ON b.id = c.building_id
			LEFT JOIN building_floors bf ON bf.id = c.building_floor_id
			LEFT JOIN building_floor_spaces bfs ON bfs.id = c.building_floor_space_id
			LEFT JOIN application_users au ON au.id = c.created_by
			LEFT JOIN contracts ct ON ct.id = c.contract_id AND ct.company_id = c.company_id
			LEFT JOIN vendors v ON v.id = ct.party_id AND ct.party_type = 'vendor' AND v.company_id = ct.company_id
			LEFT JOIN locations l ON l.id = c.location_id AND l.company_id = c.company_id AND l.is_active = 0
			LEFT JOIN contract_amc_service_schedules sch ON sch.id = c.schedule_id AND sch.company_id = c.company_id AND sch.is_active = 0
			WHERE c.id='{$get['rid']}' AND c.company_id='{$get['company_id']}' AND c.is_active='0'";
			$table_details = $callConfig->getRow($table_details);
			$return['table'] = $table_details;

			$query = "SELECT * FROM application_attachments
				WHERE transaction_id = '{$get['rid']}' AND application_table_name = 'complaints' AND company_id='{$get['company_id']}' AND is_active='0' ";
			$res = $callConfig->getAllRows($query);
			$return['complaints_images_data'] = $res;

			$query = "SELECT t1.id, t1.user_id,t1.transaction_date,t1.remarks, t2.display_name
				FROM complaint_responses t1
				LEFT JOIN application_users t2 ON t2.id = t1.user_id
				WHERE t1.complaint_id = '{$get['rid']}' AND t1.company_id='{$get['company_id']}' AND t1.is_active=0";
			$res = $callConfig->getAllRows($query);
			$return['complaint_responses_data'] = $res;
			$return['complaint_responses_images_data'] = array();
			if (count((array)$res) > 0) {
				foreach ($res as $row) {
					$query = "SELECT * FROM application_attachments
						WHERE transaction_id = $row->id AND application_table_name = 'complaint_responses' AND company_id='{$get['company_id']}' AND is_active='0' ";
					$res1 = $callConfig->getAllRows($query);
					$return['complaint_responses_images_data'][$row->id] = $res1;
				}
			}

			return $return;
		}
		$admin_or_user_whr = "";
		if($get['admin_type'] != 1){
			$admin_or_user_whr = " AND c.created_by='{$get['user_id']}'";
		}

		$query = "SELECT c.id as id, c.user_id as user_id, c.complaint_name, c.complaint_type, c.complaint_priority, c.transaction_date,
		c.contract_id, c.servicenow_ref, c.schedule_id, c.location_id,
		i.item_name, a.asset_code, a.asset_description, s.name, s.background_color, s.color, c.current_status,
		ct.contract_name, CASE WHEN ct.party_type = 'vendor' THEN v.display_name ELSE '' END AS vendor_name,
		concat(l.short_name,'-',l.location_name) AS location_name,
		sch.planned_service_datetime, sch.current_status AS schedule_status
		FROM complaints c
		LEFT JOIN items i ON i.id = c.item_id
		LEFT JOIN assets a ON a.id = c.asset_id
		LEFT JOIN status s ON s.name_value = c.current_status
		LEFT JOIN contracts ct ON ct.id = c.contract_id AND ct.company_id = c.company_id
		LEFT JOIN vendors v ON v.id = ct.party_id AND ct.party_type = 'vendor' AND v.company_id = ct.company_id
		LEFT JOIN locations l ON l.id = c.location_id AND l.company_id = c.company_id AND l.is_active = 0
		LEFT JOIN contract_amc_service_schedules sch ON sch.id = c.schedule_id AND sch.company_id = c.company_id AND sch.is_active = 0
		WHERE c.company_id={$get['company_id']} AND c.is_active='0' ".$admin_or_user_whr." ORDER BY id DESC";
		$return['table'] = $callConfig->getAllRows($query);
		return $return;
	}

	function getAmcComplaintsReportData($get)
	{
		global $callConfig;
		$return = array();
		$company_id = $get['company_id'];
		$whr = " WHERE c.company_id = '{$company_id}' AND c.is_active = 0 ";

		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from_date = date('Y-m-d', strtotime($get['from_date']));
			$to_date = date('Y-m-d', strtotime($get['to_date']));
			$whr .= " AND DATE(c.transaction_date) BETWEEN '{$from_date}' AND '{$to_date}' ";
		}

		if (isset($get['status']) && $get['status'] != '') {
			if ($get['status'] == '0') {
				$whr .= " AND c.current_status = 0 ";
			} elseif ($get['status'] == '1') {
				$whr .= " AND c.current_status > 0 AND c.current_status < 199 ";
			} elseif ($get['status'] == '199') {
				$whr .= " AND c.current_status = 199 ";
			}
		}

		if (!empty($get['contract_id'])) {
			$whr .= " AND c.contract_id = '" . addslashes($get['contract_id']) . "' ";
		}
		if (!empty($get['vendor_id'])) {
			$whr .= " AND ct.party_type = 'vendor' AND ct.party_id = '" . addslashes($get['vendor_id']) . "' ";
		}
		if (!empty($get['location_id'])) {
			$whr .= " AND c.location_id = '" . addslashes($get['location_id']) . "' ";
		}
		if (!empty($get['asset_id'])) {
			$whr .= " AND c.asset_id = '" . addslashes($get['asset_id']) . "' ";
		}

		$sql = "
			SELECT
				c.*,
				i.item_name,
				a.asset_code,
				a.asset_description,
				concat(l.short_name, '-', l.location_name) AS location_name,
				ct.contract_name,
				CASE WHEN ct.party_type = 'vendor' THEN v.display_name ELSE '' END AS vendor_name
			FROM complaints c
			LEFT JOIN items i ON i.id = c.item_id AND i.company_id = c.company_id AND i.is_active = 0
			LEFT JOIN assets a ON a.id = c.asset_id AND a.company_id = c.company_id AND a.is_active = 0
			LEFT JOIN locations l ON l.id = c.location_id AND l.company_id = c.company_id AND l.is_active = 0
			LEFT JOIN contracts ct ON ct.id = c.contract_id AND ct.company_id = c.company_id
			LEFT JOIN vendors v ON v.id = ct.party_id AND ct.party_type = 'vendor' AND v.company_id = ct.company_id
			{$whr}
			ORDER BY c.transaction_date DESC, c.id DESC";

		$return['report_res'] = $callConfig->getAllRows($sql);
		return $return;
	}


//// kotee end

	// koti start

	function getAmcAdminLandingDashboardData($get)
	{
		
		global $callConfig;
		$emptyKpis = array('contracts' => 0, 'active' => 0, 'expired' => 0, 'customers' => 0, 'vendors' => 0);
		$return = array(
			'kpis' => $emptyKpis,
			'yearly_chart' => array(),
			'locations' => array(array('id' => '', 'location_name' => 'All Locations')),
			'contracts' => array(
				'customer' => array('total' => 0, 'rows' => array()),
				'vendor' => array('total' => 0, 'rows' => array()),
				'inhouse' => array('total' => 0, 'rows' => array()),
			),
			'visits' => array(
				'overdue' => array('total' => 0, 'rows' => array()),
				'today' => array('total' => 0, 'rows' => array()),
				'yesterday' => array('total' => 0, 'rows' => array()),
				'next_7_days' => array('total' => 0, 'rows' => array()),
			),
			'complaints' => array('total' => 0, 'rows' => array()),
			'repairs' => array('total' => 0, 'rows' => array()),
			'contract_expiry' => array('expiring' => array(), 'expired' => array()),
			'suggested_contract_id' => 0,
		);

		$companyId = (int) ($get['company_id'] ?? 0);
		if ($companyId <= 0) {
			return $return;
		}

		$dashboardData = $this->getdashboardreact($get);
		$allcontracts = $this->amcLandingRowsToArray($dashboardData['allcontracts'] ?? array());
		$allcustomers = $dashboardData['allcustomers'] ?? array();
		$allvendors = $dashboardData['allvendors'] ?? array();
		$permissions = $this->amcLandingRowsToArray($dashboardData['permissions'] ?? array());
		$return['dashboardData']=$dashboardData;
		$allowedContracts = $this->getAmcAdminLandingAllowedContracts($get, $allcontracts, $permissions);
		$allowedContractIds = array();
		if (count((array)$allowedContracts) > 0) {
			foreach ($allowedContracts as $contract) {
				$allowedContractIds[] = (int) ($contract['id'] ?? 0);
			}
		}
		$allowedContractIds = array_values(array_filter(array_unique($allowedContractIds)));
				
		$split = $this->splitAmcAdminLandingActiveExpired($allowedContracts);
		$return['kpis'] = array(
			'contracts' => count((array)$allowedContracts),
			'active' => $split['active'],
			'expired' => $split['expired'],
			'customers' => count((array)$allcustomers),
			'vendors' => count((array)$allvendors),
		);

		$return['yearly_chart'] = $this->buildAmcAdminLandingYearlyChart($allowedContracts);
		$return['locations'] = $this->buildAmcAdminLandingLocationsList($dashboardData['locations'] ?? array());
				
		$partyTypes = array('customer', 'vendor', 'inhouse');
		if (count((array)$partyTypes) > 0) {
			foreach ($partyTypes as $partyType) {
				$return['contracts'][$partyType] = $this->buildAmcAdminLandingContractsByParty(
					$allowedContracts,
					$partyType
				);
			}
		}

		$return['visits'] = $this->buildAmcAdminLandingVisits($get, $allowedContractIds);
		$return['complaints'] = $this->buildAmcAdminLandingComplaints($get);
		$return['repairs'] = $this->buildAmcAdminLandingRepairs($get);
		$return['contract_expiry'] = $this->buildAmcAdminLandingContractExpiry($allowedContracts);
		$return['suggested_contract_id'] = $this->resolveAmcAdminLandingSuggestedContractId($allowedContracts);

		return $return;
	}
	private function buildAmcAdminLandingContractExpiry($contracts)
	{
		$expiringSections = array(
			array('key' => 'exp_7', 'title' => 'Expires in 7 Days', 'min' => 0, 'max' => 7),
			array('key' => 'exp_30', 'title' => 'Expires in 30 Days', 'min' => 7, 'max' => 30),
			array('key' => 'exp_60', 'title' => 'Expires in 31 to 60 Days', 'min' => 31, 'max' => 60),
			array('key' => 'exp_90', 'title' => 'Expires in 61-90 Days', 'min' => 61, 'max' => 90),
		);
		$expiredSections = array(
			array('key' => 'expd_7', 'title' => 'Expired last 7 Days', 'start_days' => 0, 'days' => 7),
			array('key' => 'expd_30', 'title' => 'Expired last 30 Days', 'start_days' => 7, 'days' => 30),
			array('key' => 'expd_60', 'title' => 'Expired last 60 Days', 'start_days' => 30, 'days' => 60),
			array('key' => 'expd_90', 'title' => 'Expired last 90 Days', 'start_days' => 60, 'days' => 90),
		);

		$expiring = array();
		if (count((array)$expiringSections) > 0) {
			foreach ($expiringSections as $section) {
				$filtered = $this->filterAmcAdminLandingContractsByExpiryRange($contracts, $section['min'], $section['max']);
				$rows = array();
				if (count((array)$filtered) > 0) {
					foreach ($filtered as $contract) {
						$rows[] = $this->mapAmcAdminLandingExpiryRow($contract, 'expiring');
					}
				}
				$expiring[] = array(
					'key' => $section['key'],
					'title' => $section['title'],
					'total' => count((array)$rows),
					'rows' => $rows,
				);
			}
		}

		$expired = array();
		if (count((array)$expiredSections) > 0) {
			foreach ($expiredSections as $section) {
				$filtered = $this->filterAmcAdminLandingContractsByExpiredRange(
					$contracts,
					$section['start_days'],
					$section['days']
				);
				$rows = array();
				if (count((array)$filtered) > 0) {
					foreach ($filtered as $contract) {
						$rows[] = $this->mapAmcAdminLandingExpiryRow($contract, 'expired');
					}
				}
				$expired[] = array(
					'key' => $section['key'],
					'title' => $section['title'],
					'total' => count((array)$rows),
					'rows' => $rows,
				);
			}
		}

		return array('expiring' => $expiring, 'expired' => $expired);
	}

	private function filterAmcAdminLandingContractsByExpiryRange($contracts, $daysMin, $daysMax)
	{
		$today = new DateTime();
		$today->setTime(0, 0, 0);
		$filtered = array();
		if (count((array)$contracts) > 0) {
			foreach ($contracts as $contract) {
				$c = is_array($contract) ? $contract : (array) $contract;
				$endDateRaw = $c['end_date'] ?? '';
				if ($endDateRaw == '') {
					continue;
				}
				$endDate = new DateTime($endDateRaw);
				$endDate->setTime(0, 0, 0);
				if ($endDate < $today) {
					continue;
				}
				$daysLeft = (int) ceil(($endDate->getTimestamp() - $today->getTimestamp()) / 86400);
				if ($daysLeft >= (int) $daysMin && $daysLeft <= (int) $daysMax) {
					$filtered[] = $c;
				}
			}
		}
		return $filtered;
	}

	private function filterAmcAdminLandingContractsByExpiredRange($contracts, $startDays, $days)
	{
		$today = new DateTime();
		$today->setTime(0, 0, 0);
		$startDate = (clone $today)->modify('-' . (int) $startDays . ' days');
		$pastDate = (clone $today)->modify('-' . (int) $days . ' days');
		$filtered = array();
		if (count((array)$contracts) > 0) {
			foreach ($contracts as $contract) {
				$c = is_array($contract) ? $contract : (array) $contract;
				$endDateRaw = $c['end_date'] ?? '';
				if ($endDateRaw == '') {
					continue;
				}
				$endDate = new DateTime($endDateRaw);
				$endDate->setTime(0, 0, 0);
				if ($endDate < $startDate && $endDate >= $pastDate) {
					$filtered[] = $c;
				}
			}
		}
		return $filtered;
	}

	private function mapAmcAdminLandingExpiryRow($contract, $type)
	{
		$c = is_array($contract) ? $contract : (array) $contract;
		$today = new DateTime();
		$today->setTime(0, 0, 0);
		$endDateRaw = $c['end_date'] ?? '';
		$daysLeft = 0;
		$daysExpired = 0;
		if ($endDateRaw != '') {
			$endDate = new DateTime($endDateRaw);
			$endDate->setTime(0, 0, 0);
			$diffDays = (int) ceil(($endDate->getTimestamp() - $today->getTimestamp()) / 86400);
			if ($diffDays >= 0) {
				$daysLeft = $diffDays;
			} else {
				$daysExpired = abs($diffDays);
			}
		}
		$contractType = trim((string) ($c['contract_type'] ?? ''));
		$contractName = trim((string) ($c['contract_name'] ?? ''));
		$displayName = $contractName;
		if ($contractType != '') {
			$displayName .= ' (' . $contractType . ')';
		}
		return array(
			'id' => (int) ($c['id'] ?? 0),
			'contract_name' => $contractName,
			'contract_type' => $contractType,
			'display_name' => $displayName,
			'end_date' => $endDateRaw,
			'days_left' => $daysLeft,
			'days_expired' => $daysExpired,
			'type' => $type,
		);
	}

	function getAmcAdminLandingContractsPopupList($get)
	{
		$return = array('contracts' => array(), 'total' => 0);
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($companyId <= 0) {
			return $return;
		}

		$type = strtolower(trim((string) ($get['type'] ?? '')));
		$sort = strtolower(trim((string) ($get['sort'] ?? '')));
		$validTypes = array('total', 'active', 'expired', 'amc');
		if (!in_array($type, $validTypes)) {
			return $return;
		}

		$dashboardData = $this->getdashboardreact($get);
		$allcontracts = $this->amcLandingRowsToArray($dashboardData['allcontracts'] ?? array());
		$permissions =  array();  // contract_permissions not used for landing scope
		$allowedContracts = $this->getAmcAdminLandingAllowedContracts($get, $allcontracts, $permissions);

		if ($type == 'amc') {
			$filtered = $this->filterAmcAdminLandingAmcContracts($allowedContracts);
		} else {
			$filtered = $this->filterAmcAdminLandingContractsByKpiType($allowedContracts, $type);
		}

		$rollups = array();
		if ($type == 'amc' && count((array)$filtered) > 0) {
			$contractIds = array();
			if (count((array)$filtered) > 0) {
				foreach ($filtered as $contract) {
					$c = is_array($contract) ? $contract : (array) $contract;
					$contractIds[] = (int) ($c['id'] ?? 0);
				}
			}
			$contractIds = array_values(array_filter(array_unique($contractIds)));
			$rollups = $this->buildAmcAdminLandingAssignmentRollups($companyId, $contractIds);
		}

		if ($sort == 'assignment' && $type == 'amc') {
			usort($filtered, function ($a, $b) use ($rollups) {
				$aId = (int) ((is_array($a) ? $a : (array) $a)['id'] ?? 0);
				$bId = (int) ((is_array($b) ? $b : (array) $b)['id'] ?? 0);
				$aUn = (int) ($rollups[$aId]['asset_unassigned'] ?? 0);
				$bUn = (int) ($rollups[$bId]['asset_unassigned'] ?? 0);
				if ($aUn != $bUn) {
					return $bUn - $aUn;
				}
				$dateA = strtotime(!empty($a['updated_date']) ? $a['updated_date'] : ($a['created_date'] ?? ''));
				$dateB = strtotime(!empty($b['updated_date']) ? $b['updated_date'] : ($b['created_date'] ?? ''));
				return $dateB - $dateA;
			});
		} else {
			usort($filtered, function ($a, $b) {
				$dateA = strtotime(!empty($a['updated_date']) ? $a['updated_date'] : ($a['created_date'] ?? ''));
				$dateB = strtotime(!empty($b['updated_date']) ? $b['updated_date'] : ($b['created_date'] ?? ''));
				return $dateB - $dateA;
			});
		}

		$rows = array();
		if (count((array)$filtered) > 0) {
			foreach ($filtered as $contract) {
				$c = is_array($contract) ? $contract : (array) $contract;
				$contractId = (int) ($c['id'] ?? 0);
				$rollup = $rollups[$contractId] ?? array();
				$rows[] = $this->mapAmcAdminLandingContractPopupRow($c, $rollup);
			}
		}

		$return['contracts'] = $rows;
		$return['total'] = count((array)$rows);
		return $return;
	}

	private function filterAmcAdminLandingAmcContracts($contracts)
	{
		$filtered = array();
		if (count((array)$contracts) > 0) {
			foreach ($contracts as $contract) {
				$c = is_array($contract) ? $contract : (array) $contract;
				if (strtoupper($c['is_amc_contract'] ?? 'N') == 'Y') {
					$filtered[] = $c;
				}
			}
		}
		return $filtered;
	}

	private function buildAmcAdminLandingAssignmentRollups($companyId, $contractIds)
	{
		global $callConfig;
		$rollups = array();
		if ($companyId <= 0 || count((array)$contractIds) == 0) {
			return $rollups;
		}

		$idList = implode(',', array_map('intval', $contractIds));

		$countSql = "SELECT ca.contract_id,
			COUNT(*) AS asset_total,
			SUM(CASE
				WHEN COALESCE(cad.service_mode, 'vendor') = 'inhouse' THEN CASE WHEN IFNULL(ca.default_employee_id, 0) > 0 THEN 1 ELSE 0 END
				ELSE CASE WHEN IFNULL(ca.default_technician_id, 0) > 0 THEN 1 ELSE 0 END
			END) AS asset_assigned
			FROM contract_amc_assets ca
			LEFT JOIN contract_amc_details cad ON cad.contract_id = ca.contract_id AND cad.company_id = ca.company_id AND cad.is_active = 0
			WHERE ca.company_id = {$companyId} AND ca.is_active = 0 AND ca.contract_id IN ({$idList})
			GROUP BY ca.contract_id";
		$countRows = $callConfig->getAllRows($countSql);
		if (count((array)$countRows) > 0) {
			foreach ($countRows as $row) {
				$r = is_array($row) ? $row : (array) $row;
				$contractId = (int) ($r['contract_id'] ?? 0);
				$assetTotal = (int) ($r['asset_total'] ?? 0);
				$assetAssigned = (int) ($r['asset_assigned'] ?? 0);
				$assetUnassigned = max(0, $assetTotal - $assetAssigned);
				$status = $this->resolveAmcAdminLandingAssignmentStatus($assetTotal, $assetAssigned);
				$rollups[$contractId] = array(
					'asset_total' => $assetTotal,
					'asset_assigned' => $assetAssigned,
					'asset_unassigned' => $assetUnassigned,
					'assignment_status' => $status['status'],
					'assignment_label' => $status['label'],
					'assignment_class' => $status['class'],
					'assignee_label' => '—',
					'assignee_type' => 'Technician',
				);
			}
		}

		$assigneeSql = "SELECT ca.contract_id,
			CASE WHEN COALESCE(cad.service_mode, 'vendor') = 'inhouse' THEN ca.default_employee_id ELSE ca.default_technician_id END AS assignee_id,
			CASE WHEN COALESCE(cad.service_mode, 'vendor') = 'inhouse' THEN e.display_name ELSE t.account_name END AS assignee_name,
			CASE WHEN COALESCE(cad.service_mode, 'vendor') = 'inhouse' THEN 'Employee' ELSE 'Technician' END AS assignee_type
			FROM contract_amc_assets ca
			LEFT JOIN contract_amc_details cad ON cad.contract_id = ca.contract_id AND cad.company_id = ca.company_id AND cad.is_active = 0
			LEFT JOIN amc_account_details t ON t.id = ca.default_technician_id AND t.company_id = ca.company_id AND t.is_active = 0
			LEFT JOIN employees e ON e.id = ca.default_employee_id AND e.company_id = ca.company_id AND e.is_active = 0
			WHERE ca.company_id = {$companyId} AND ca.is_active = 0 AND ca.contract_id IN ({$idList})
			AND (
				(COALESCE(cad.service_mode, 'vendor') = 'inhouse' AND IFNULL(ca.default_employee_id, 0) > 0)
				OR (COALESCE(cad.service_mode, 'vendor') != 'inhouse' AND IFNULL(ca.default_technician_id, 0) > 0)
			)";
		$assigneeRows = $callConfig->getAllRows($assigneeSql);
		$assigneesByContract = array();
		if (count((array)$assigneeRows) > 0) {
			foreach ($assigneeRows as $row) {
				$r = is_array($row) ? $row : (array) $row;
				$contractId = (int) ($r['contract_id'] ?? 0);
				$assigneeId = (int) ($r['assignee_id'] ?? 0);
				if ($contractId <= 0 || $assigneeId <= 0) {
					continue;
				}
				if (!isset($assigneesByContract[$contractId])) {
					$assigneesByContract[$contractId] = array();
				}
				$assigneesByContract[$contractId][$assigneeId] = array(
					'name' => $r['assignee_name'] ?? '',
					'type' => $r['assignee_type'] ?? 'Technician',
				);
			}
		}

		if (count((array)$contractIds) > 0) {
			foreach ($contractIds as $contractId) {
				$contractId = (int) $contractId;
				if (!isset($rollups[$contractId])) {
					$status = $this->resolveAmcAdminLandingAssignmentStatus(0, 0);
					$rollups[$contractId] = array(
						'asset_total' => 0,
						'asset_assigned' => 0,
						'asset_unassigned' => 0,
						'assignment_status' => $status['status'],
						'assignment_label' => $status['label'],
						'assignment_class' => $status['class'],
						'assignee_label' => '—',
						'assignee_type' => 'Technician',
					);
				}
				$assigneeMap = $assigneesByContract[$contractId] ?? array();
				$distinctIds = array_keys($assigneeMap);
				if (count((array)$distinctIds) == 1) {
					$first = $assigneeMap[$distinctIds[0]];
					$rollups[$contractId]['assignee_label'] = $first['name'] != '' ? $first['name'] : '—';
					$rollups[$contractId]['assignee_type'] = $first['type'] ?? 'Technician';
				} elseif (count((array)$distinctIds) > 1) {
					$rollups[$contractId]['assignee_label'] = 'Multiple';
					$first = reset($assigneeMap);
					$rollups[$contractId]['assignee_type'] = $first['type'] ?? 'Technician';
				}
			}
		}

		return $rollups;
	}

	private function resolveAmcAdminLandingAssignmentStatus($assetTotal, $assetAssigned)
	{
		$assetTotal = (int) $assetTotal;
		$assetAssigned = (int) $assetAssigned;
		if ($assetTotal == 0) {
			return array('status' => 'no_assets', 'label' => 'No assets', 'class' => 'bg-secondary');
		}
		if ($assetAssigned == 0) {
			return array('status' => 'not_assigned', 'label' => 'Not assigned', 'class' => 'bg-danger');
		}
		if ($assetAssigned < $assetTotal) {
			$unassigned = $assetTotal - $assetAssigned;
			return array(
				'status' => 'partial',
				'label' => $unassigned . ' of ' . $assetTotal . ' unassigned',
				'class' => 'bg-warning',
			);
		}
		return array('status' => 'fully_assigned', 'label' => 'Fully assigned', 'class' => 'bg-success');
	}

	private function filterAmcAdminLandingContractsByKpiType($contracts, $type)
	{
		if ($type == 'total') {
			return is_array($contracts) ? $contracts : array();
		}

		$today = new DateTime();
		$filtered = array();
		if (count((array)$contracts) > 0) {
			foreach ($contracts as $contract) {
				$c = is_array($contract) ? $contract : (array) $contract;
				if (($c['is_active'] ?? 0) != 0) {
					continue;
				}
				$endDate = $c['end_date'] ?? '';
				$isExpired = ($endDate != '' && new DateTime($endDate) < $today);
				if ($type == 'expired' && $isExpired) {
					$filtered[] = $c;
				} elseif ($type == 'active' && ($endDate == '' || new DateTime($endDate) >= $today)) {
					$filtered[] = $c;
				}
			}
		}
		return $filtered;
	}

	private function mapAmcAdminLandingContractPopupRow($contract, $rollup = array())
	{
		$c = is_array($contract) ? $contract : (array) $contract;
		$currentStatus = (int) ($c['current_status'] ?? 0);
		$cancelStatus = $c['cancel_status'] ?? 'N';
		$partyType = strtolower(trim((string) ($c['party_type'] ?? '')));
		$row = array(
			'id' => (int) ($c['id'] ?? 0),
			'transaction_no' => $c['transaction_no'] ?? '',
			'contract_name' => $c['contract_name'] ?? '',
			'party_type' => $partyType != '' ? ucfirst($partyType) : '-',
			'status_label' => $this->amcLandingContractStatusLabel($currentStatus, $cancelStatus),
			'status_class' => $this->amcLandingContractStatusClass($currentStatus, $cancelStatus),
			'start_date' => $c['start_date'] ?? '',
			'end_date' => $c['end_date'] ?? '',
			'is_amc' => $c['is_amc_contract'] ?? 'N',
		);
		if (count((array)$rollup) > 0) {
			$row['asset_total'] = (int) ($rollup['asset_total'] ?? 0);
			$row['asset_assigned'] = (int) ($rollup['asset_assigned'] ?? 0);
			$row['asset_unassigned'] = (int) ($rollup['asset_unassigned'] ?? 0);
			$row['assignment_status'] = $rollup['assignment_status'] ?? '';
			$row['assignment_label'] = $rollup['assignment_label'] ?? '';
			$row['assignment_class'] = $rollup['assignment_class'] ?? 'bg-secondary';
			$row['assignee_label'] = $rollup['assignee_label'] ?? '—';
			$row['assignee_type'] = $rollup['assignee_type'] ?? 'Technician';
		}
		return $row;
	}

	private function amcLandingRowsToArray($rows)
	{
		$out = array();
		if (count((array)$rows) > 0) {
			foreach ($rows as $row) {
				$out[] = is_array($row) ? $row : (array) $row;
			}
		}
		return $out;
	}

	private function getAmcAdminLandingAllowedContracts($get, $allcontracts, $permissions)
	{
		// $combinedIds = array();
		// if (count((array)$permissions) > 0) {
		// 	foreach ($permissions as $permission) {
		// 		$combinedIds[] = (int) ($permission['contract_id'] ?? 0);
		// 	}
		// }
		// $combinedIds = array_values(array_filter(array_unique($combinedIds)));

		// if (($get['admin_type'] ?? 0) == 1) {
		// 	return $allcontracts;
		// }

		// $allowed = array();
		// if (count((array)$allcontracts) > 0) {
		// 	foreach ($allcontracts as $contract) {
		// 		if (in_array((int) ($contract['id'] ?? 0), $combinedIds)) {
		// 			$allowed[] = $contract;
		// 		}
		// 	}
		// }
		return $allcontracts;
	}

	private function splitAmcAdminLandingActiveExpired($contracts)
	{
		$today = new DateTime();
		$active = 0;
		$expired = 0;
		if (count((array)$contracts) > 0) {
			foreach ($contracts as $contract) {
				$c = is_array($contract) ? $contract : (array) $contract;
				if (($c['is_active'] ?? 0) != 0) {
					continue;
				}
				$endDate = $c['end_date'] ?? '';
				if ($endDate != '' && new DateTime($endDate) < $today) {
					$expired++;
				} elseif ($endDate == '' || new DateTime($endDate) >= $today) {
					$active++;
				}
			}
		}
		return array('active' => $active, 'expired' => $expired);
	}

	private function buildAmcAdminLandingYearlyChart($contracts)
	{
		$contractsByYear = array();
		if (count((array)$contracts) > 0) {
			foreach ($contracts as $contract) {
				$c = is_array($contract) ? $contract : (array) $contract;
				$dateValue = '';
				if (!empty($c['start_date'])) {
					$dateValue = $c['start_date'];
				} elseif (!empty($c['created_date'])) {
					$dateValue = $c['created_date'];
				}
				if ($dateValue == '') {
					continue;
				}
				$year = date('Y', strtotime($dateValue));
				if (!isset($contractsByYear[$year])) {
					$contractsByYear[$year] = array('y' => $year, 'vendor' => 0, 'customer' => 0, 'inhouse' => 0);
				}
				$partyType = strtolower(trim((string) ($c['party_type'] ?? '')));
				if ($partyType == 'vendor' || $partyType == 'customer' || $partyType == 'inhouse') {
					$contractsByYear[$year][$partyType]++;
				}
			}
		}
		$chart = array_values($contractsByYear);
		usort($chart, function ($a, $b) {
			return strcmp($a['y'], $b['y']);
		});
		return $chart;
	}

	private function buildAmcAdminLandingLocationsList($locations)
	{
		$list = array(array('id' => '', 'location_name' => 'All Locations'));
		if (count((array)$locations) > 0) {
			foreach ($locations as $loc) {
				$l = is_array($loc) ? $loc : (array) $loc;
				$list[] = array(
					'id' => (string) ($l['id'] ?? ''),
					'location_name' => $l['location_name'] ?? '',
				);
			}
		}
		return $list;
	}

	private function amcLandingContractStatusClass($currentStatus, $cancelStatus)
	{
		if (($cancelStatus ?? 'N') == 'Y') {
			return 'bg-danger';
		}
		$currentStatus = (int) $currentStatus;
		if ($currentStatus == 0) {
			return 'bg-secondary';
		}
		if ($currentStatus == (int) ST_APPROVED) {
			return 'bg-success';
		}
		if ($currentStatus == (int) ST_CANCEL) {
			return 'bg-danger';
		}
		if ($currentStatus > 0 && $currentStatus < (int) ST_APPROVED) {
			return 'bg-warning';
		}
		return 'bg-secondary';
	}

	private function amcLandingContractStatusLabel($currentStatus, $cancelStatus = 'N')
	{
		if (($cancelStatus ?? 'N') == 'Y') {
			return 'Cancelled';
		}
		$currentStatus = (int) ($currentStatus ?? 0);
		if ($currentStatus == 0) {
			return 'Draft';
		}
		if (isset($_SESSION['status'][$currentStatus]['name']) && $_SESSION['status'][$currentStatus]['name'] != '') {
			return $_SESSION['status'][$currentStatus]['name'];
		}
		if ($currentStatus == (int) ST_APPROVED) {
			return 'Approved';
		}
		if (defined('ST_REJECTED') && $currentStatus == (int) ST_REJECTED) {
			return 'Rejected';
		}
		if (defined('ST_CANCEL') && $currentStatus == (int) ST_CANCEL) {
			return 'Cancelled';
		}
		if ($currentStatus > 100 && $currentStatus < (int) ST_APPROVED) {
			return 'In Progress';
		}
		return '-';
	}

	private function mapAmcAdminLandingContractRow($contract)
	{
		$c = is_array($contract) ? $contract : (array) $contract;
		$currentStatus = (int) ($c['current_status'] ?? 0);
		$cancelStatus = $c['cancel_status'] ?? 'N';
		return array(
			'id' => (int) ($c['id'] ?? 0),
			'contract_name' => $c['contract_name'] ?? '',
			'contract_type' => $c['contract_type'] ?? '',
			'is_amc' => $c['is_amc_contract'] ?? 'N',
			'status_label' => $this->amcLandingContractStatusLabel($currentStatus, $cancelStatus),
			'status_class' => $this->amcLandingContractStatusClass($currentStatus, $cancelStatus),
			'start_date' => $c['start_date'] ?? '',
			'end_date' => $c['end_date'] ?? '',
			'transaction_no' => $c['transaction_no'] ?? '',
			'can_edit' => ($currentStatus == 0 && ($cancelStatus ?? 'N') != 'Y') ? 1 : 0,
		);
	}

	private function buildAmcAdminLandingContractsByParty($contracts, $partyType)
	{
		$filtered = array();
		if (count((array)$contracts) > 0) {
			foreach ($contracts as $contract) {
				$c = is_array($contract) ? $contract : (array) $contract;
				if (strtolower(trim((string) ($c['party_type'] ?? ''))) == $partyType) {
					$filtered[] = $c;
				}
			}
		}
		usort($filtered, function ($a, $b) {
			$dateA = strtotime(!empty($a['updated_date']) ? $a['updated_date'] : ($a['created_date'] ?? ''));
			$dateB = strtotime(!empty($b['updated_date']) ? $b['updated_date'] : ($b['created_date'] ?? ''));
			return $dateB - $dateA;
		});
		$rows = array();
		$slice = array_slice($filtered, 0, 5);
		if (count((array)$slice) > 0) {
			foreach ($slice as $contract) {
				$rows[] = $this->mapAmcAdminLandingContractRow($contract);
			}
		}
		return array('total' => count((array)$filtered), 'rows' => $rows);
	}

	private function buildAmcAdminLandingVisitJoin()
	{
		return "INNER JOIN contracts c ON c.id = s.contract_id AND c.company_id = s.company_id AND c.is_active = 0 AND c.is_amc_contract = 'Y' AND c.current_status = '" . ST_APPROVED . "'
			LEFT JOIN contract_amc_details cad ON cad.contract_id = c.id AND cad.company_id = c.company_id AND cad.is_active = 0
			LEFT JOIN contract_amc_assets ca ON ca.id = s.contract_amc_asset_id AND ca.company_id = s.company_id AND ca.is_active = 0
			LEFT JOIN assets a ON a.id = COALESCE(NULLIF(s.asset_id, 0), ca.asset_id) AND a.company_id = s.company_id AND a.is_active = 0
			LEFT JOIN contract_amc_locations cal ON cal.id = ca.contract_amc_location_id AND cal.company_id = s.company_id AND cal.is_active = 0
			LEFT JOIN vendors v ON c.party_type = 'vendor' AND v.id = c.party_id AND v.company_id = c.company_id
			LEFT JOIN departments d ON c.party_type = 'inhouse' AND d.id = c.department_id AND d.company_id = c.company_id";
	}

	private function amcAdminLandingVisitHasAssignee($row)
	{
		$r = is_array($row) ? $row : (array) $row;
		$serviceMode = $this->resolveContractAmcServiceMode($r);
		if ($serviceMode == 'inhouse') {
			$assigneeId = (int) ($r['employee_id'] ?? 0);
			$defaultAssigneeId = (int) ($r['default_employee_id'] ?? 0);
		} else {
			$assigneeId = (int) ($r['technician_id'] ?? 0);
			$defaultAssigneeId = (int) ($r['default_technician_id'] ?? 0);
		}
		return ($assigneeId > 0 || $defaultAssigneeId > 0);
	}

	private function amcAdminLandingVisitLocWhere($locId)
	{
		$locId = (int) $locId;
		if ($locId <= 0) {
			return '';
		}
		return " AND (cal.location_id = {$locId} OR a.asset_location = {$locId})";
	}

	private function amcAdminLandingAllowedContractsWhere($allowedContractIds)
	{
		if (count((array)$allowedContractIds) == 0) {
			return ' AND 1=0';
		}
		$idList = implode(',', array_map('intval', $allowedContractIds));
		return " AND c.id IN ({$idList})";
	}

	private function mapAmcAdminLandingVisitRow($row, $section)
	{
		$r = is_array($row) ? $row : (array) $row;
		$status = $this->mapAmcAdminLandingVisitStatus($r, $section);
		$planned = $r['planned_service_datetime'] ?? '';
		$plannedDate = $planned != '' ? date('Y-m-d', strtotime($planned)) : '';
		return array(
			'contract_id' => (int) ($r['contract_id'] ?? 0),
			'contract_name' => $r['contract_name'] ?? '',
			'party_label' => $r['party_label'] ?? '—',
			'asset_label' => $r['asset_label'] ?? '—',
			'planned_date' => $plannedDate,
			'status_label' => $status['status_label'],
			'status_class' => $status['status_class'],
			'action' => $status['action'],
		);
	}

	private function mapAmcAdminLandingVisitStatus($row, $section)
	{
		$r = is_array($row) ? $row : (array) $row;
		$phase = $this->getScheduleVisitPhase($r);
		$status = (int) ($r['visit_status'] ?? $r['current_status'] ?? 0);
		$planned = $r['planned_service_datetime'] ?? '';
		$plannedDate = $planned != '' ? date('Y-m-d', strtotime($planned)) : '';
		$today = date('Y-m-d');
		$yesterday = date('Y-m-d', strtotime('-1 day'));

		if ($section == 'overdue' || ($plannedDate != '' && $plannedDate < $today && $status < 199 && $phase != 'closed')) {
			return array('status_label' => 'Overdue', 'status_class' => 'bg-danger', 'action' => 'view');
		}
		if ($phase == 'closed') {
			return array('status_label' => 'Completed', 'status_class' => 'bg-success', 'action' => 'view');
		}
		if ($phase == 'in_progress') {
			return array('status_label' => 'In Progress', 'status_class' => 'bg-warning', 'action' => 'finish');
		}
		if ($section == 'yesterday' && $plannedDate == $yesterday && $status < 199) {
			return array('status_label' => 'Delayed', 'status_class' => 'bg-danger', 'action' => 'view');
		}
		if ($section == 'next_7_days') {
		    $action = $this->amcAdminLandingVisitHasAssignee($r) ? 'view' : 'assign';
		    return array('status_label' => 'Scheduled', 'status_class' => 'bg-info', 'action' => $action);
		}
		if (!$this->amcAdminLandingVisitHasAssignee($r)) {
			return array('status_label' => 'Pending', 'status_class' => 'bg-primary', 'action' => 'assign');
		}
		return array('status_label' => 'Pending', 'status_class' => 'bg-primary', 'action' => 'start');
	}

	private function buildAmcAdminLandingVisits($get, $allowedContractIds)
	{
		global $callConfig;
		$companyId = (int) ($get['company_id'] ?? 0);
		$join = $this->buildAmcAdminLandingVisitJoin();
		$locWhere = $this->amcAdminLandingVisitLocWhere($get['LOCID'] ?? '');
		$contractWhere = $this->amcAdminLandingAllowedContractsWhere($allowedContractIds);
		$baseWhere = "s.company_id = {$companyId} AND s.is_active = 0{$contractWhere}{$locWhere}";

		$today = date('Y-m-d');
		$yesterday = date('Y-m-d', strtotime('-1 day'));
		$tomorrow = date('Y-m-d', strtotime('+1 day'));
		$next7End = date('Y-m-d', strtotime('+7 days'));

		$listSelect = "SELECT s.id, s.planned_service_datetime, s.current_status AS visit_status, s.actual_start_datetime, s.actual_end_datetime,
			s.technician_id, s.employee_id, ca.default_technician_id, ca.default_employee_id, c.party_type, cad.service_mode,
			c.id AS contract_id, c.contract_name,
			CASE WHEN c.party_type = 'vendor' THEN COALESCE(v.display_name, '—')
				WHEN c.party_type = 'inhouse' THEN COALESCE(d.department_name, '—')
				ELSE '—' END AS party_label,
			COALESCE(NULLIF(ca.asset_name, ''), a.asset_description, '—') AS asset_label
			FROM contract_amc_service_schedules s {$join}
			WHERE {$baseWhere} AND ";

		$sections = array(
			'overdue' => "s.current_status = 0 AND s.planned_service_datetime < NOW()",
			'today' => "DATE(s.planned_service_datetime) = '{$today}'",
			'yesterday' => "DATE(s.planned_service_datetime) = '{$yesterday}'",
			'next_7_days' => "s.current_status = 0 AND DATE(s.planned_service_datetime) BETWEEN '{$tomorrow}' AND '{$next7End}'",
		);

		$return = array();
		if (count((array)$sections) > 0) {
			foreach ($sections as $key => $filter) {
				$countSql = "SELECT COUNT(*) AS c FROM contract_amc_service_schedules s {$join} WHERE {$baseWhere} AND {$filter}";
				$countRow = $callConfig->getRow($countSql);
				$total = (int) ($countRow->c ?? 0);
				$listSql = $listSelect . $filter . " ORDER BY s.planned_service_datetime ASC LIMIT 5";
				$dbRows = $callConfig->getAllRows($listSql);
				$rows = array();
				if (count((array)$dbRows) > 0) {
					foreach ($dbRows as $dbRow) {
						$rows[] = $this->mapAmcAdminLandingVisitRow($dbRow, $key);
					}
				}
				$return[$key] = array('total' => $total, 'rows' => $rows);
			}
		}
		return $return;
	}

	private function buildAmcAdminLandingVisitSearchWhere($search)
	{
		global $callConfig;
		$search = trim((string) $search);
		if ($search == '') {
			return '';
		}
		$esc = mysqli_real_escape_string($callConfig->con, $search);
		$like = "'%" . $esc . "%'";
		return " AND (c.contract_name LIKE {$like} OR c.transaction_no LIKE {$like}"
			. " OR v.display_name LIKE {$like} OR d.department_name LIKE {$like}"
			. " OR ca.asset_name LIKE {$like} OR a.asset_description LIKE {$like} OR a.asset_code LIKE {$like}"
			. " OR s.planned_service_datetime LIKE {$like}"
			. " OR DATE_FORMAT(s.planned_service_datetime, '%d-%m-%Y') LIKE {$like}"
			. " OR DATE_FORMAT(s.planned_service_datetime, '%Y-%m-%d') LIKE {$like}"
			. " OR DATE_FORMAT(s.planned_service_datetime, '%d/%m/%Y') LIKE {$like})";
	}

	private function buildAmcAdminLandingVisitSectionFilter($section)
	{
		$section = strtolower(trim((string) $section));
		$today = date('Y-m-d');
		$yesterday = date('Y-m-d', strtotime('-1 day'));
		$tomorrow = date('Y-m-d', strtotime('+1 day'));
		$next7End = date('Y-m-d', strtotime('+7 days'));
		$filters = array(
			'overdue' => "s.current_status = 0 AND s.planned_service_datetime < NOW()",
			'today' => "DATE(s.planned_service_datetime) = '{$today}'",
			'yesterday' => "DATE(s.planned_service_datetime) = '{$yesterday}'",
			'next_7_days' => "s.current_status = 0 AND DATE(s.planned_service_datetime) BETWEEN '{$tomorrow}' AND '{$next7End}'",
		);
		return $filters[$section] ?? '';
	}

	private function amcAdminLandingVisitSectionTitle($section)
	{
		$titles = array(
			'overdue' => 'Overdue Visits',
			'today' => "Today's Visits",
			'yesterday' => 'Yesterday Visits',
			'next_7_days' => 'Next 7 Days Visits',
		);
		$section = strtolower(trim((string) $section));
		return $titles[$section] ?? 'Overdue Visits';
	}

	private function getAmcAdminLandingAllowedContractIds($get)
	{
		$dashboardData = $this->getdashboardreact($get);
		$allcontracts = $this->amcLandingRowsToArray($dashboardData['allcontracts'] ?? array());
		$permissions = $this->amcLandingRowsToArray($dashboardData['permissions'] ?? array());
		$allowedContracts = $this->getAmcAdminLandingAllowedContracts($get, $allcontracts, $permissions);
		$allowedContractIds = array();
		if (count((array)$allowedContracts) > 0) {
			foreach ($allowedContracts as $contract) {
				$allowedContractIds[] = (int) ($contract['id'] ?? 0);
			}
		}
		return array_values(array_filter(array_unique($allowedContractIds)));
	}

	function getAmcAdminLandingVisitsList($get)
	{
		global $callConfig;
		$section = strtolower(trim((string) ($get['section'] ?? 'overdue')));
		if ($section == '') {
			$section = 'overdue';
		}
		$return = array(
			'rows' => array(),
			'total' => 0,
			'page' => 1,
			'limit' => 20,
			'total_pages' => 0,
			'section' => $section,
			'title' => $this->amcAdminLandingVisitSectionTitle($section),
		);
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($companyId <= 0) {
			return $return;
		}

		$sectionFilter = $this->buildAmcAdminLandingVisitSectionFilter($section);
		if ($sectionFilter == '') {
			$return['section'] = 'overdue';
			$return['title'] = $this->amcAdminLandingVisitSectionTitle('overdue');
			return $return;
		}

		$allowedContractIds = $this->getAmcAdminLandingAllowedContractIds($get);
		$page = max(1, (int) ($get['page'] ?? 1));
		$limit = (int) ($get['limit'] ?? 20);
		if ($limit <= 0) {
			$limit = 20;
		}
		if ($limit > 100) {
			$limit = 100;
		}
		$search = trim((string) ($get['search'] ?? ''));

		$join = $this->buildAmcAdminLandingVisitJoin();
		$locWhere = $this->amcAdminLandingVisitLocWhere($get['LOCID'] ?? '');
		$contractWhere = $this->amcAdminLandingAllowedContractsWhere($allowedContractIds);
		$searchWhere = $this->buildAmcAdminLandingVisitSearchWhere($search);
		$baseWhere = "s.company_id = {$companyId} AND s.is_active = 0{$contractWhere}{$locWhere}{$searchWhere} AND {$sectionFilter}";

		$countSql = "SELECT COUNT(*) AS c FROM contract_amc_service_schedules s {$join} WHERE {$baseWhere}";
		$countRow = $callConfig->getRow($countSql);
		$total = (int) ($countRow->c ?? 0);
		$totalPages = $total > 0 ? (int) ceil($total / $limit) : 0;
		if ($totalPages > 0 && $page > $totalPages) {
			$page = $totalPages;
		}
		$offset = ($page - 1) * $limit;

		$listSql = "SELECT s.id AS schedule_id, s.planned_service_datetime, s.current_status AS visit_status,
			s.actual_start_datetime, s.actual_end_datetime, s.technician_id, s.employee_id,
			ca.default_technician_id, ca.default_employee_id, c.party_type, cad.service_mode,
			c.id AS contract_id, c.contract_name, c.transaction_no,
			CASE WHEN c.party_type = 'vendor' THEN COALESCE(v.display_name, '—')
				WHEN c.party_type = 'inhouse' THEN COALESCE(d.department_name, '—')
				ELSE '—' END AS party_label,
			COALESCE(NULLIF(ca.asset_name, ''), a.asset_description, '—') AS asset_label
			FROM contract_amc_service_schedules s {$join}
			WHERE {$baseWhere}
			ORDER BY s.planned_service_datetime ASC
			LIMIT {$limit} OFFSET {$offset}";
		$dbRows = $callConfig->getAllRows($listSql);
		$rows = array();
		if (count((array)$dbRows) > 0) {
			foreach ($dbRows as $dbRow) {
				$rows[] = $this->mapAmcAdminLandingVisitRow($dbRow, $section);
			}
		}

		return array(
			'rows' => $rows,
			'total' => $total,
			'page' => $page,
			'limit' => $limit,
			'total_pages' => $totalPages,
			'section' => $section,
			'title' => $this->amcAdminLandingVisitSectionTitle($section),
		);
	}


	private function buildAmcAdminLandingComplaints($get)
	{
		global $callConfig;
		$companyId = (int) ($get['company_id'] ?? 0);
		$locWhere = '';
		$locId = (int) ($get['LOCID'] ?? 0);
		if ($locId > 0) {
			$locWhere = " AND c.location_id = {$locId}";
		}
		$admin_or_user_whr = "";
		if ($get['admin_type'] != 1) {
			$admin_or_user_whr = " AND c.created_by='{$get['user_id']}'";
		}
		$baseWhere = "c.company_id = {$companyId} AND c.is_active = 0 AND c.current_status != 199{$locWhere}{$admin_or_user_whr}";

		$countSql = "SELECT COUNT(*) AS c FROM complaints c WHERE {$baseWhere}";
		$countRow = $callConfig->getRow($countSql);
		$total = (int) ($countRow->c ?? 0);

		$sql = "SELECT c.id, c.transaction_date, c.current_status, ct.contract_name,
			CASE WHEN ct.party_type = 'vendor' THEN v.display_name ELSE '' END AS vendor_name,
			a.asset_code, a.asset_description, s.name AS status_name, s.background_color, s.color
			FROM complaints c
			LEFT JOIN assets a ON a.id = c.asset_id AND a.company_id = c.company_id
			LEFT JOIN status s ON s.name_value = c.current_status
			LEFT JOIN contracts ct ON ct.id = c.contract_id AND ct.company_id = c.company_id
			LEFT JOIN vendors v ON v.id = ct.party_id AND ct.party_type = 'vendor' AND v.company_id = ct.company_id
			WHERE {$baseWhere}
			ORDER BY c.transaction_date DESC, c.id DESC
			LIMIT 5";
		$dbRows = $callConfig->getAllRows($sql);
		$rows = array();
		if (count((array)$dbRows) > 0) {
			foreach ($dbRows as $dbRow) {
				$r = is_array($dbRow) ? $dbRow : (array) $dbRow;
				$assetLabel = trim((string) ($r['asset_code'] ?? ''));
				if ($assetLabel != '' && !empty($r['asset_description'])) {
					$assetLabel .= ' — ' . $r['asset_description'];
				} elseif ($assetLabel == '') {
					$assetLabel = $r['asset_description'] ?? '—';
				}
				$statusLabel = trim((string) ($r['status_name'] ?? ''));
				if ($statusLabel == '') {
					$statusLabel = ((int) ($r['current_status'] ?? 0) == 0) ? 'Active' : '-';
				}
				$contractName = trim((string) ($r['contract_name'] ?? ''));
				$vendorName = trim((string) ($r['vendor_name'] ?? ''));
				$rows[] = array(
					'id' => (int) ($r['id'] ?? 0),
					'contract_name' => $contractName != '' ? $contractName : '—',
					'vendor_name' => $vendorName != '' ? $vendorName : '—',
					'asset_label' => $assetLabel,
					'date' => !empty($r['transaction_date']) ? date('Y-m-d', strtotime($r['transaction_date'])) : '',
					'status_label' => $statusLabel,
					'status_color' => !empty($r['background_color']) ? $r['background_color'] : '#6c757d',
					'status_text_color' => !empty($r['color']) ? $r['color'] : '#fff',
				);
			}
		}
		return array('total' => $total, 'rows' => $rows);
	}

	private function buildAmcAdminLandingRepairs($get)
	{
		global $callConfig;
		$companyId = (int) ($get['company_id'] ?? 0);
		$locWhere = '';
		$locId = (int) ($get['LOCID'] ?? 0);
		if ($locId > 0) {
			$locWhere = " AND ro.location_id = {$locId}";
		}
		$admin_or_user_whr = "";
		if ($get['admin_type'] != 1) {
			$admin_or_user_whr = " AND ro.created_by='{$get['user_id']}'";
		}
		$baseWhere = "ro.company_id = {$companyId} AND ro.is_active = 0 AND ro.current_status != 199{$locWhere}{$admin_or_user_whr}";

		$countSql = "SELECT COUNT(*) AS c FROM contract_amc_repair_orders ro WHERE {$baseWhere}";
		$countRow = $callConfig->getRow($countSql);
		$total = (int) ($countRow->c ?? 0);

		$sql = "SELECT ro.id, ro.transaction_no, ro.repair_date, ro.source_type, ro.current_status, c.contract_name,
			CASE WHEN c.party_type = 'vendor' THEN v.display_name ELSE '' END AS vendor_name,
			s.name AS status_name, s.background_color, s.color
			FROM contract_amc_repair_orders ro
			LEFT JOIN contracts c ON c.id = ro.contract_id AND c.company_id = ro.company_id
			LEFT JOIN vendors v ON v.id = ro.vendor_id AND v.company_id = ro.company_id
			LEFT JOIN status s ON s.name_value = ro.current_status
			WHERE {$baseWhere}
			ORDER BY ro.repair_date DESC, ro.id DESC
			LIMIT 5";
		$dbRows = $callConfig->getAllRows($sql);
		$rows = array();
		if (count((array)$dbRows) > 0) {
			foreach ($dbRows as $dbRow) {
				$r = is_array($dbRow) ? $dbRow : (array) $dbRow;
				$sourceType = trim((string) ($r['source_type'] ?? ''));
				$source = $sourceType != '' ? ucfirst(str_replace('_', ' ', $sourceType)) : '-';
				$statusLabel = trim((string) ($r['status_name'] ?? ''));
				if ($statusLabel == '') {
					$statusLabel = ((int) ($r['current_status'] ?? 0) == 0) ? 'Active' : '-';
				}
				$contractName = trim((string) ($r['contract_name'] ?? ''));
				$vendorName = trim((string) ($r['vendor_name'] ?? ''));
				$rows[] = array(
					'id' => (int) ($r['id'] ?? 0),
					'ro_no' => $r['transaction_no'] ?? '',
					'contract_name' => $contractName != '' ? $contractName : '—',
					'vendor_name' => $vendorName != '' ? $vendorName : '—',
					'source' => $source,
					'date' => !empty($r['repair_date']) ? date('Y-m-d', strtotime($r['repair_date'])) : '',
					'status_label' => $statusLabel,
					'status_color' => !empty($r['background_color']) ? $r['background_color'] : '#6c757d',
					'status_text_color' => !empty($r['color']) ? $r['color'] : '#fff',
				);
			}
		}
		return array('total' => $total, 'rows' => $rows);
	}

	private function resolveAmcAdminLandingSuggestedContractId($contracts)
	{
		if (count((array)$contracts) > 0) {
			foreach ($contracts as $contract) {
				$c = is_array($contract) ? $contract : (array) $contract;
				if (strtoupper($c['is_amc_contract'] ?? 'N') == 'Y' && (int) ($c['current_status'] ?? 0) == (int) ST_APPROVED) {
					return (int) ($c['id'] ?? 0);
				}
			}
		}
		return 0;
	}


	// koti end
	//contract excel import functions start

	function generateContractsExcelTemplate($get, $post = array())
	{
		global $callConfig;
		require_once dirname(__DIR__) . '/includes/contracts_excel_helper.php';
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($companyId <= 0) {
			return array('status' => 'error', 'message' => 'Missing company_id');
		}

		$contractHeaders = cex_contract_sheet_headers();
		$sampleRow = array();
		if (count((array)$contractHeaders) > 0) {
			foreach ($contractHeaders as $h) {
				$sampleRow[] = '';
			}
		}
		if (count((array)$sampleRow) > 0) {
			$sampleRow[0] = 'C001';
			$sampleRow[1] = 'amc';
			$sampleRow[2] = 'Sample AMC Contract';
			$sampleRow[3] = date('Y-m-d');
			$sampleRow[4] = date('Y-m-d', strtotime('+1 year'));
			$sampleRow[5] = '100000';
			$sampleRow[6] = '';
			$sampleRow[7] = 'vendor';
			$sampleRow[8] = '';
			$sampleRow[9] = '';
			$sampleRow[10] = 'billing_cycle';
			$sampleRow[11] = 'quarterly';
			$sampleRow[12] = '1';
		}

		$sheets = array();
		$sheets[] = array('name' => 'Contracts', 'rows' => array_merge(array($contractHeaders), array($sampleRow)));
		$sheets[] = array('name' => 'Locations', 'rows' => array(cex_location_sheet_headers()));
		$sheets[] = array('name' => 'Assets', 'rows' => array(cex_asset_sheet_headers()));
		$sheets[] = array('name' => 'Milestones', 'rows' => array(cex_milestone_sheet_headers()));

		$sheets[] = array('name' => 'Ref_ContractTypes', 'rows' => array(array('contract_type'), array('standard'), array('amc')));
		$sheets[] = array('name' => 'Ref_PartyTypes', 'rows' => array(array('party_type'), array('vendor'), array('customer'), array('inhouse')));
		$sheets[] = array('name' => 'Ref_PaymentPlans', 'rows' => array(array('payment_plan'), array('billing_cycle'), array('milestones')));
		$sheets[] = array('name' => 'Ref_BillingCycles', 'rows' => array(array('billing_cycle'), array('monthly'), array('quarterly'), array('half_yearly'), array('yearly')));

		$freqRows = array(array('service_frequency', 'label'));
		$freqSql = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = " . $companyId . " AND is_active = 0 AND genc_code = 'AMCSERVICE' ORDER BY genc_detail_name ASC";
		$freqDb = $callConfig->getAllRows($freqSql);
		$freqMerged = $this->mergeAmcServiceFrequencyOptions($freqDb);
		if (count((array)$freqMerged) > 0) {
			foreach ($freqMerged as $freqRow) {
				$freqRow = is_array($freqRow) ? $freqRow : (array) $freqRow;
				$freqRows[] = array($freqRow['genc_detail_code'] ?? '', $freqRow['genc_detail_name'] ?? '');
			}
		}
		$sheets[] = array('name' => 'Ref_ServiceFrequencies', 'rows' => $freqRows);

		$deptRows = array(array('id', 'department_name'));
		$deptRes = $callConfig->getAllRows("SELECT id, department_name FROM departments WHERE company_id = " . $companyId . " AND is_active = 0 ORDER BY department_name ASC");
		if (count((array)$deptRes) > 0) {
			foreach ($deptRes as $deptRow) {
				$deptRow = is_array($deptRow) ? $deptRow : (array) $deptRow;
				$deptRows[] = array($deptRow['id'] ?? '', $deptRow['department_name'] ?? '');
			}
		}
		$sheets[] = array('name' => 'Ref_Departments', 'rows' => $deptRows);

		$vendorRows = array(array('bp_code', 'company', 'display_name', 'id'));
		$vendorRes = $callConfig->getAllRows("SELECT id, COALESCE(NULLIF(account_code,''), NULLIF(reference_vendor_code,''), NULLIF(short_name,''), CAST(id AS CHAR)) AS bp_code, company, display_name FROM vendors WHERE company_id = " . $companyId . " AND is_active = 0 ORDER BY company ASC");
		if (count((array)$vendorRes) > 0) {
			foreach ($vendorRes as $vendorRow) {
				$vendorRow = is_array($vendorRow) ? $vendorRow : (array) $vendorRow;
				$vendorRows[] = array($vendorRow['bp_code'] ?? '', $vendorRow['company'] ?? '', $vendorRow['display_name'] ?? '', $vendorRow['id'] ?? '');
			}
		}
		$sheets[] = array('name' => 'Ref_Vendors', 'rows' => $vendorRows);

		$customerRows = array(array('bp_code', 'company', 'display_name', 'id'));
		$customerRes = $callConfig->getAllRows("SELECT id, COALESCE(NULLIF(account_code,''), CAST(id AS CHAR)) AS bp_code, company, display_name FROM customers WHERE company_id = " . $companyId . " AND is_active = 0 ORDER BY company ASC");
		if (count((array)$customerRes) > 0) {
			foreach ($customerRes as $customerRow) {
				$customerRow = is_array($customerRow) ? $customerRow : (array) $customerRow;
				$customerRows[] = array($customerRow['bp_code'] ?? '', $customerRow['company'] ?? '', $customerRow['display_name'] ?? '', $customerRow['id'] ?? '');
			}
		}
		$sheets[] = array('name' => 'Ref_Customers', 'rows' => $customerRows);

		$locRows = array(array('id', 'location_name'));
		$locRes = $callConfig->getAllRows("SELECT id, location_name FROM locations WHERE company_id = " . $companyId . " AND is_active = 0 ORDER BY location_name ASC");
		if (count((array)$locRes) > 0) {
			foreach ($locRes as $locRow) {
				$locRow = is_array($locRow) ? $locRow : (array) $locRow;
				$locRows[] = array($locRow['id'] ?? '', $locRow['location_name'] ?? '');
			}
		}
		$sheets[] = array('name' => 'Ref_Locations', 'rows' => $locRows);

		$assetRows = array(array('location_name','asset_code', 'name'));
		$assetRes = $callConfig->getAllRows("SELECT a.asset_code, a.asset_description, l.location_name FROM assets a LEFT JOIN locations l ON l.id = a.asset_location AND l.company_id = a.company_id AND l.is_active = 0 WHERE a.company_id = " . $companyId . " AND a.is_active = 0 ORDER BY a.asset_description ASC");
		if (count((array)$assetRes) > 0) {
			foreach ($assetRes as $assetRow) {
				$assetRow = is_array($assetRow) ? $assetRow : (array) $assetRow;
				$assetRows[] = array($assetRow['location_name'] ?? '', $assetRow['asset_code'] ?? '', $assetRow['asset_description'] ?? '');
			}
		}
		$sheets[] = array('name' => 'Ref_Assets', 'rows' => $assetRows);

		$catRows = array(array('code', 'name'));
		$catRes = $callConfig->getAllRows("SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = " . $companyId . " AND is_active = 0 AND genc_code = 'CONTRACT_TYPE' ORDER BY genc_detail_name ASC");
		if (count((array)$catRes) > 0) {
			foreach ($catRes as $catRow) {
				$catRow = is_array($catRow) ? $catRow : (array) $catRow;
				$catRows[] = array($catRow['genc_detail_code'] ?? '', $catRow['genc_detail_name'] ?? '');
			}
		}
		$sheets[] = array('name' => 'Ref_ContractCategories', 'rows' => $catRows);

		$ptRows = array(array('id', 'payment_term_name'));
		$ptRes = $callConfig->getAllRows("SELECT id, payment_term_name FROM payment_terms WHERE company_id = " . $companyId . " AND is_active = 0 ORDER BY payment_term_name ASC");
		if (count((array)$ptRes) > 0) {
			foreach ($ptRes as $ptRow) {
				$ptRow = is_array($ptRow) ? $ptRow : (array) $ptRow;
				$ptRows[] = array($ptRow['id'] ?? '', $ptRow['payment_term_name'] ?? '');
			}
		}
		$sheets[] = array('name' => 'Ref_PaymentTerms', 'rows' => $ptRows);

		$agrRows = array(array('code', 'name'));
		$agrRes = $callConfig->getAllRows("SELECT id AS genc_detail_code, agreement_name AS genc_detail_name FROM contract_agreement_types WHERE company_id = " . $companyId . " AND is_active = 0 ORDER BY agreement_name ASC");
		if (count((array)$agrRes) == 0) {
			$agrRes = $callConfig->getAllRows("SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = " . $companyId . " AND is_active = 0 AND genc_code = 'AGREEMENTS_TYPE' ORDER BY genc_detail_name ASC");
		}
		if (count((array)$agrRes) > 0) {
			foreach ($agrRes as $agrRow) {
				$agrRow = is_array($agrRow) ? $agrRow : (array) $agrRow;
				$agrRows[] = array($agrRow['genc_detail_code'] ?? '', $agrRow['genc_detail_name'] ?? '');
			}
		}
		$sheets[] = array('name' => 'Ref_AgreementTypes', 'rows' => $agrRows);

		$descRows = array(array('id', 'description_type', 'description'));
		$descRes = $callConfig->getAllRows("SELECT id, description_type, description FROM description_contracts WHERE company_id = " . $companyId . " AND is_active = 0 ORDER BY description_type ASC");
		if (count((array)$descRes) > 0) {
			foreach ($descRes as $descRow) {
				$descRow = is_array($descRow) ? $descRow : (array) $descRow;
				$descRows[] = array($descRow['id'] ?? '', $descRow['description_type'] ?? '', $descRow['description'] ?? '');
			}
		}
		$sheets[] = array('name' => 'Ref_DescriptionTypes', 'rows' => $descRows);

		$svcRows = array(array('id', 'service_name'));
		$svcRes = $callConfig->getAllRows("SELECT id, service_name FROM contract_services WHERE company_id = " . $companyId . " AND is_active = 0 ORDER BY service_name ASC");
		if (count((array)$svcRes) > 0) {
			foreach ($svcRes as $svcRow) {
				$svcRow = is_array($svcRow) ? $svcRow : (array) $svcRow;
				$svcRows[] = array($svcRow['id'] ?? '', $svcRow['service_name'] ?? '');
			}
		}
		$sheets[] = array('name' => 'Ref_Services', 'rows' => $svcRows);

		$userRows = array(array('id', 'display_name', 'user_email'));
		$userRes = $callConfig->getAllRows("SELECT id, display_name, user_email FROM application_users WHERE company_id = " . $companyId . " AND is_active = 0 ORDER BY display_name ASC");
		if (count((array)$userRes) > 0) {
			foreach ($userRes as $userRow) {
				$userRow = is_array($userRow) ? $userRow : (array) $userRow;
				$userRows[] = array($userRow['id'] ?? '', $userRow['display_name'] ?? '', $userRow['user_email'] ?? '');
			}
		}
		$sheets[] = array('name' => 'Ref_Users', 'rows' => $userRows);

		$sheets[] = array('name' => 'Instructions', 'rows' => array(
			array('Contracts Excel Import — Instructions'),
			array('1. Fill Contracts sheet: one row per contract. import_ref must be unique (e.g. C001).'),
			array('2. contract_type: standard or amc. For amc, fill Locations and Assets sheets with matching import_ref.'),
			array('3. party_type: vendor, customer, or inhouse. Resolve party via party_bp_code (account_code / vendor code / id) or party_name using Ref sheets.'),
			array('4. payment_plan: billing_cycle (auto milestones) or milestones (fill Milestones sheet).'),
			array('5. locations_csv (standard contracts): comma-separated location names or IDs from Ref_Locations.'),
			array('6. services_csv: comma-separated service names from Ref_Services.'),
			array('7. Delete sample row before import or replace with real data.'),
		));

		$fileName = 'contracts_import_template_' . $companyId . '_' . time() . '.xlsx';
		$relPath = 'uploads/contracts/templates/' . $fileName;
		$absPath = dirname(__DIR__) . '/' . str_replace('/', DIRECTORY_SEPARATOR, $relPath);
		try {
			cex_write_xlsx($sheets, $absPath);
		} catch (Exception $e) {
			return array('status' => 'error', 'message' => $e->getMessage());
		}

		return array(
			'status' => 'success',
			'file_path' => $relPath,
			'file_name' => $fileName,
			'download_url' => $relPath,
		);
	}

	function validateContractsExcel($get, $post)
	{
		global $callConfig;
		require_once dirname(__DIR__) . '/includes/contracts_excel_helper.php';
		$companyId = (int) ($get['company_id'] ?? 0);
		if ($companyId <= 0) {
			return array('status' => 'error', 'message' => 'Missing company_id', 'errors' => array('Missing company_id'));
		}

		$sheetData = $this->cexLoadContractsExcelImportData($post);
		if (!empty($sheetData['error'])) {
			return array('status' => 'error', 'message' => $sheetData['error'], 'errors' => array($sheetData['error']));
		}
		$contracts = $sheetData['contracts'];
		$locations = $sheetData['locations'];
		$assets = $sheetData['assets'];
		$milestones = $sheetData['milestones'];

		$batchInsert = array(
			'company_id' => $companyId,
			'file_name' => trim((string) ($post['file_name'] ?? '')),
			'uploaded_by' => (int) ($get['user_id'] ?? 0),
			'uploaded_at' => date('Y-m-d H:i:s'),
			'contracts_data' => json_encode($contracts),
			'locations_data' => json_encode($locations),
			'assets_data' => json_encode($assets),
			'milestones_data' => json_encode($milestones),
			'status' => 'uploaded',
			'is_active' => 0,
		);
		if ($batchInsert['file_name'] == '') {
			$batchInsert['file_name'] = basename(trim((string) ($post['temp_xlsx'] ?? 'import.xlsx')));
		}
		$batchRes = $callConfig->insertRecord('contracts_excel_import_batches', $batchInsert);
		$batchId = (int) ($batchRes['LASTID'] ?? 0);
		if ($batchId <= 0) {
			return array('status' => 'error', 'message' => 'Unable to create import batch', 'errors' => array('Batch insert failed'));
		}

		$lookups = $this->cexBuildContractsExcelLookups($companyId);
		$validation = $this->cexValidateContractsExcelData($contracts, $locations, $assets, $milestones, $lookups);

		$batchStatus = 'validated';
		if (count((array)$validation['errors']) > 0 && count((array)$validation['valid_import_refs']) == 0) {
			$batchStatus = 'failed';
		}
		$summaryJson = json_encode(array(
			'validation' => array(
				'valid_count' => count((array)$validation['valid_import_refs']),
				'error_count' => count((array)$validation['errors']),
				'warning_count' => count((array)$validation['warnings']),
				'validated_at' => date('Y-m-d H:i:s'),
			),
		));
		$callConfig->updateRecord('contracts_excel_import_batches', array(
			'status' => $batchStatus,
			'import_summary' => $summaryJson,
		), 'id', $batchId);

		return array(
			'batch_id' => $batchId,
			'status' => $batchStatus,
			'contracts' => $validation['contracts'],
			'locations' => $validation['locations'],
			'assets' => $validation['assets'],
			'milestones' => $validation['milestones'],
			'errors' => $validation['errors'],
			'warnings' => $validation['warnings'],
			'valid_import_refs' => $validation['valid_import_refs'],
		);
	}
	function importInsertContractReact($get, $post)
	{
		global $callConfig;
		$return = array();
		$return['post'] = $post;
		$return['get'] = $get;

		$locations = '';
		if (isset($post['locations']) && is_array($post['locations'])) {
			$locations = json_encode($post['locations']);
		}

		$contract_services_id = '';
		if (isset($post['contract_services_id']) && is_array($post['contract_services_id'])) {
			$contract_services_id = json_encode($post['contract_services_id']);
		}

		$contact_personsary = array();
		if (isset($post['contact_persons']) && is_array($post['contact_persons']) && count((array)($post['contact_persons']['name'] ?? array())) > 0) {
			foreach ($post['contact_persons']['name'] as $key => $conpers) {
				if ($post['contact_persons']['name'][$key] != "" && $post['contact_persons']['number'][$key] != "") {
					$contact_personsary[] = array(
						'name' => $post['contact_persons']['name'][$key],
						'email' => $post['contact_persons']['email'][$key],
						'number' => $post['contact_persons']['number'][$key]
					);
				}
			}
		}
		$contact_persons = json_encode($contact_personsary);

		$contract_type = 'Contract';
		if (!empty($post['parent_contract_id']) && $post['parent_contract_id'] != 0) {
			$contract_type = 'SOW';
		}

		$isAmcContract = (!empty($post['is_amc_contract']) && in_array($post['is_amc_contract'], array('Y', 'Yes', '1', 1), true)) ? 'Y' : 'N';

		$insert_array = array(
			'contract_name' => $post['contract_name'],
			'renewal_frequency' => $post['renewal_frequency'],
			'parent_contract_id' => $post['parent_contract_id'],
			'contract_type' => $contract_type,
			'invoice_number' => $post['invoice_number'],
			'rt_number' => $post['rt_number'],
			'business_code' => $post['business_code'],
			'party_type' => $post['party_type'],
			'contract_owner_name' => $post['contract_owner_name'],
			'contract_owner_email' => $post['contract_owner_email'],
			'contract_owner_number' => $post['contract_owner_number'],
			'party_id' => $post['party_id'],
			'department_id' => $post['department_id'],
			'type_of_contract_id' => $post['type_of_contract_id'],
			'start_date' => $this->dateconvertdbformat($post['start_date']),
			'end_date' => $this->dateconvertdbformat($post['end_date']),
			'contract_amount' => $post['contract_amount'],
			'description' => $post['description'],
			'additional_notes' => $post['additional_notes'],
			'payment_term_id' => $post['payment_term_id'],
			'termination_condition' => $post['termination_condition'],
			'contract_status' => $post['contract_status'],
			'current_status' => 0,
			'auto_renew' => (isset($post['auto_renew']) && ($post['auto_renew'] == '1' || $post['auto_renew'] == 'on' || $post['auto_renew'] === true || $post['auto_renew'] == 'Y')) ? 'Y' : 'N',
			'renew_type' => $post['renew_type'],
			'state' => $post['state'],
			'isp' => $post['isp'],
			'paid_by' => $post['paid_by'],
			'internet_charges' => $post['internet_charges'],
			'hub_spoc_name' => $post['hub_spoc_name'],
			'hub_spoc_number' => $post['hub_spoc_number'],
			'wifi_pass' => $post['wifi_pass'],
			'wifi_provider' => $post['wifi_provider'],
			'additional_document' => $post['additional_document'],
			'contract_payment_terms' => $post['contract_payment_terms'],
			'typeofaggrements' => $post['typeofaggrements'],
			'description_id' => $post['description_id'],
			'locations' => $locations,
			'is_oem' => (isset($post['is_oem']) && ($post['is_oem'] == 'Yes' || $post['is_oem'] == 'Y')) ? 'Y' : 'N',
			'oem_registration_number' => $post['oem_registration_number'] ?? '',
			'oem_distributor_name' => $post['oem_distributor_name'] ?? '',
			'oem_product_category' => $post['oem_product_category'] ?? '',
			'cancel_status' => 'N',
			'is_amc_contract' => $isAmcContract,
			'contract_services_id' => $contract_services_id,
			'contact_persons' => $contact_persons,
			'company_id' => $get['company_id'],
			'is_active' => 0,
			'transaction_no' => $this->toGetApplicationSerialNumber('contracts', $post),
			'created_by' => $get['user_id'],
			'originator' => $get['user_id'],
			'created_date' => date('Y-m-d H:i:s'),
		);

		if (trim((string) ($insert_array['payment_term_id'] ?? '')) == '') {
			unset($insert_array['payment_term_id']);
		}

		$insert_data = $callConfig->insertRecord('contracts', $insert_array);
		$contract_id = $insert_data['LASTID'] ?? 0;
		$return['contract_result'] = $insert_data;

		if (($insert_data['status'] ?? '') == 'sucess' || ($insert_data['status'] ?? '') == 'success') {
			if (isset($post['milestones']) && is_array($post['milestones']) && count((array)$post['milestones']) > 0) {
				foreach ($post['milestones'] as $milestone) {
					if (empty(trim($milestone['milestone_name'] ?? ''))) {
						continue;
					}
					$milestoneData = array(
						'company_id' => $get['company_id'],
						'transaction_id' => $contract_id,
						'transaction_date' => date('Y-m-d H:i:s'),
						'milestone' => trim($milestone['milestone_name']),
						'milestone_date' => !empty($milestone['milestone_date']) ? $this->dateconvertdbformat($milestone['milestone_date']) : null,
						'milestone_price' => !empty($milestone['milestone_price']) ? (float) $milestone['milestone_price'] : 0,
						'is_active' => 0,
						'current_status' => 0,
						'status_date' => date('Y-m-d H:i:s'),
						'created_date' => date('Y-m-d H:i:s'),
						'created_by' => $get['user_id'],
						'updated_by' => $get['user_id'],
						'updated_date' => date('Y-m-d H:i:s'),
					);
					$milestoneInsert = $callConfig->insertRecord('contract_milestones', $milestoneData);
					$return['milestone_inserts'][] = $milestoneInsert;
				}
			} else {
				$return['milestone_inserts'] = 'No milestones found in POST.';
			}

			if ($isAmcContract == 'Y') {
				$return['amc_contract_result'] = $this->saveAmcContractData($contract_id, $get, $post);
			}
		}

		return $return;
	}
	function importContractsExcel($get, $post)
	{
		global $callConfig;
		require_once dirname(__DIR__) . '/includes/contracts_excel_helper.php';
		$companyId = (int) ($get['company_id'] ?? 0);
		$batchId = (int) ($post['batch_id'] ?? 0);
		if ($companyId <= 0 || $batchId <= 0) {
			return array('status' => 'error', 'message' => 'Missing company_id or batch_id');
		}

		$batchSql = "SELECT * FROM contracts_excel_import_batches WHERE id = '" . addslashes($batchId) . "' AND company_id = " . $companyId . " AND is_active = 0 LIMIT 1";
		$batchRow = $callConfig->getRow($batchSql);
		if (!$batchRow) {
			return array('status' => 'error', 'message' => 'Import batch not found');
		}
		$batch = is_array($batchRow) ? $batchRow : (array) $batchRow;

		$contracts = $this->cexDecodeImportJsonField($batch['contracts_data'] ?? '');
		$locations = $this->cexDecodeImportJsonField($batch['locations_data'] ?? '');
		$assets = $this->cexDecodeImportJsonField($batch['assets_data'] ?? '');
		$milestones = $this->cexDecodeImportJsonField($batch['milestones_data'] ?? '');

		$lookups = $this->cexBuildContractsExcelLookups($companyId);
		$validation = $this->cexValidateContractsExcelData($contracts, $locations, $assets, $milestones, $lookups);

		$summaryItems = array();
		$successCount = 0;
		$failCount = 0;

		if (count((array)$validation['valid_import_refs']) > 0) {
			foreach ($validation['valid_import_refs'] as $importRef) {
				$contractPreview = $validation['contracts_by_ref'][$importRef] ?? null;
				if ($contractPreview == null || empty($contractPreview['ok'])) {
					$failCount++;
					$summaryItems[] = array('import_ref' => $importRef, 'status' => 'failed', 'message' => 'Validation failed on re-check');
					continue;
				}
				$payload = $this->cexBuildContractImportPayload($contractPreview, $validation, $get, $post);
				$payload['company_id'] = $companyId;
				$isAmc = !empty($payload['is_amc_contract']) && $payload['is_amc_contract'] == 'Y';
				try {
					$result = $this->importInsertContractReact($get, $payload);
					$contractResult = $result['contract_result'] ?? array();
					$contractId = (int) ($contractResult['LASTID'] ?? 0);
					$resStatus = strtolower((string) ($contractResult['status'] ?? ''));
					if ($contractId > 0 && ($resStatus == 'sucess' || $resStatus == 'success')) {
						$successCount++;
						$summaryItems[] = array('import_ref' => $importRef, 'status' => 'success', 'contract_id' => $contractId);
					} else {
						$failCount++;
						$summaryItems[] = array(
							'import_ref' => $importRef,
							'status' => 'failed',
							'message' => $contractResult['message'] ?? 'Insert failed',
							'contract_result' => $contractResult,
						);
					}
				} catch (Exception $e) {
					$failCount++;
					$summaryItems[] = array('import_ref' => $importRef, 'status' => 'failed', 'message' => $e->getMessage());
				}
			}
		}

		$totalAttempted = $successCount + $failCount;
		$importStatus = 'failed';
		if ($successCount > 0 && $failCount == 0) {
			$importStatus = 'imported';
		} elseif ($successCount > 0 && $failCount > 0) {
			$importStatus = 'partial';
		}

		$importSummary = array(
			'total' => $totalAttempted,
			'success' => $successCount,
			'failed' => $failCount,
			'items' => $summaryItems,
			'imported_at' => date('Y-m-d H:i:s'),
		);
		$callConfig->updateRecord('contracts_excel_import_batches', array(
			'status' => $importStatus,
			'import_summary' => json_encode($importSummary),
		), 'id', $batchId);

		return array(
			'status' => $importStatus,
			'batch_id' => $batchId,
			'summary' => $importSummary,
		);
	}

	function getContractsExcelBatch($get, $post = array())
	{
		global $callConfig;
		$companyId = (int) ($get['company_id'] ?? 0);
		$batchId = (int) ($post['batch_id'] ?? ($get['batch_id'] ?? 0));
		if ($companyId <= 0 || $batchId <= 0) {
			return array('status' => 'error', 'message' => 'Missing company_id or batch_id');
		}
		$sql = "SELECT * FROM contracts_excel_import_batches WHERE id = '" . addslashes($batchId) . "' AND company_id = " . $companyId . " AND is_active = 0 LIMIT 1";
		$row = $callConfig->getRow($sql);
		if (!$row) {
			return array('status' => 'error', 'message' => 'Batch not found');
		}
		$batch = is_array($row) ? $row : (array) $row;
		if (!empty($batch['import_summary'])) {
			$decoded = json_decode($batch['import_summary'], true);
			if (is_array($decoded)) {
				$batch['import_summary_decoded'] = $decoded;
			}
		}
		return array('status' => 'success', 'batch' => $batch);
	}

	private function cexDecodeImportJsonField($value)
	{
		if (is_array($value)) {
			return $value;
		}
		$value = trim((string) $value);
		if ($value == '') {
			return array();
		}
		$decoded = json_decode($value, true);
		return is_array($decoded) ? $decoded : array();
	}

	private function cexLoadContractsExcelImportData($post)
	{
		require_once dirname(__DIR__) . '/includes/contracts_excel_helper.php';
		$contracts = $this->cexDecodeImportJsonField($post['contracts_data'] ?? ($post['parsed_contracts'] ?? array()));
		$locations = $this->cexDecodeImportJsonField($post['locations_data'] ?? ($post['parsed_locations'] ?? array()));
		$assets = $this->cexDecodeImportJsonField($post['assets_data'] ?? ($post['parsed_assets'] ?? array()));
		$milestones = $this->cexDecodeImportJsonField($post['milestones_data'] ?? ($post['parsed_milestones'] ?? array()));

		$tempPath = trim((string) ($post['temp_xlsx'] ?? ($post['file_path'] ?? '')));
		if ($tempPath != '' && count((array)$contracts) == 0) {
			$absPath = $tempPath;
			if (!file_exists($absPath)) {
				$absPath = dirname(__DIR__) . '/' . ltrim(str_replace('\\', '/', $tempPath), '/');
			}
			if (!file_exists($absPath)) {
				return array('error' => 'Excel file not found: ' . $tempPath);
			}
			try {
				$parsed = cex_parse_contracts_workbook($absPath);
				$contracts = $parsed['contracts'] ?? array();
				$locations = $parsed['locations'] ?? array();
				$assets = $parsed['assets'] ?? array();
				$milestones = $parsed['milestones'] ?? array();
			} catch (Exception $e) {
				return array('error' => $e->getMessage());
			}
		}

		if (count((array)$contracts) == 0) {
			return array('error' => 'No contract rows found in upload');
		}

		return array(
			'contracts' => $contracts,
			'locations' => $locations,
			'assets' => $assets,
			'milestones' => $milestones,
		);
	}

	private function cexBuildContractsExcelLookups($companyId)
	{
		global $callConfig;
		$companyId = (int) $companyId;
		$lookups = array(
			'departments' => array(),
			'departments_by_id' => array(),
			'vendors' => array(),
			'vendors_by_bp' => array(),
			'customers' => array(),
			'customers_by_bp' => array(),
			'locations' => array(),
			'locations_by_id' => array(),
			'assets' => array(),
			'assets_by_code' => array(),
			'services' => array(),
			'services_by_id' => array(),
			'payment_terms' => array(),
			'payment_terms_by_id' => array(),
			'contract_categories' => array(),
			'agreement_types' => array(),
			'description_types' => array(),
			'users' => array(),
			'service_frequencies' => array(),
			'description_types_by_id' => array(),
			'employees' => array(),
			'employees_by_id' => array(),
		);

		$deptRes = $callConfig->getAllRows("SELECT id, department_name FROM departments WHERE company_id = " . $companyId . " AND is_active = 0");
		if (count((array)$deptRes) > 0) {
			foreach ($deptRes as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$key = strtolower(trim((string) ($row['department_name'] ?? '')));
				if ($key != '') {
					$lookups['departments'][$key] = (int) ($row['id'] ?? 0);
				}
				$lookups['departments_by_id'][(int) ($row['id'] ?? 0)] = $row['department_name'] ?? '';
			}
		}

		$vendorRes = $callConfig->getAllRows("SELECT id, account_code, reference_vendor_code, short_name, company, display_name FROM vendors WHERE company_id = " . $companyId . " AND is_active = 0");
		if (count((array)$vendorRes) > 0) {
			foreach ($vendorRes as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$id = (int) ($row['id'] ?? 0);
				$names = array($row['company'] ?? '', $row['display_name'] ?? '');
				if (count((array)$names) > 0) {
					foreach ($names as $name) {
						$key = strtolower(trim((string) $name));
						if ($key != '') {
							$lookups['vendors'][$key] = $id;
						}
					}
				}
				$bpCandidates = array($row['account_code'] ?? '', $row['reference_vendor_code'] ?? '', $row['short_name'] ?? '', (string) $id);
				if (count((array)$bpCandidates) > 0) {
					foreach ($bpCandidates as $bpRaw) {
						$bp = strtolower(trim((string) $bpRaw));
						if ($bp != '') {
							$lookups['vendors_by_bp'][$bp] = $id;
						}
					}
				}
			}
		}

		$custRes = $callConfig->getAllRows("SELECT id, account_code, company, display_name FROM customers WHERE company_id = " . $companyId . " AND is_active = 0");
		if (count((array)$custRes) > 0) {
			foreach ($custRes as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$id = (int) ($row['id'] ?? 0);
				$names = array($row['company'] ?? '', $row['display_name'] ?? '');
				if (count((array)$names) > 0) {
					foreach ($names as $name) {
						$key = strtolower(trim((string) $name));
						if ($key != '') {
							$lookups['customers'][$key] = $id;
						}
					}
				}
				$bpCandidates = array($row['account_code'] ?? '', (string) $id);
				if (count((array)$bpCandidates) > 0) {
					foreach ($bpCandidates as $bpRaw) {
						$bp = strtolower(trim((string) $bpRaw));
						if ($bp != '') {
							$lookups['customers_by_bp'][$bp] = $id;
						}
					}
				}
			}
		}

		$locRes = $callConfig->getAllRows("SELECT id, location_name FROM locations WHERE company_id = " . $companyId . " AND is_active = 0");
		if (count((array)$locRes) > 0) {
			foreach ($locRes as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$id = (int) ($row['id'] ?? 0);
				$key = strtolower(trim((string) ($row['location_name'] ?? '')));
				if ($key != '') {
					$lookups['locations'][$key] = $id;
				}
				$lookups['locations_by_id'][$id] = $row['location_name'] ?? '';
			}
		}

		$assetRes = $callConfig->getAllRows("SELECT id, asset_code, asset_description FROM assets WHERE company_id = " . $companyId . " AND is_active = 0");
		if (count((array)$assetRes) > 0) {
			foreach ($assetRes as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$id = (int) ($row['id'] ?? 0);
				$code = strtolower(trim((string) ($row['asset_code'] ?? '')));
				$name = strtolower(trim((string) ($row['asset_description'] ?? '')));
				if ($code != '') {
					$lookups['assets_by_code'][$code] = array('id' => $id, 'name' => $row['asset_description'] ?? '');
				}
				if ($name != '') {
					$lookups['assets'][$name] = array('id' => $id, 'code' => $row['asset_code'] ?? '', 'name' => $row['asset_description'] ?? '');
				}
			}
		}

		$svcRes = $callConfig->getAllRows("SELECT id, service_name FROM contract_services WHERE company_id = " . $companyId . " AND is_active = 0");
		if (count((array)$svcRes) > 0) {
			foreach ($svcRes as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$id = (int) ($row['id'] ?? 0);
				$key = strtolower(trim((string) ($row['service_name'] ?? '')));
				if ($key != '') {
					$lookups['services'][$key] = $id;
				}
				$lookups['services_by_id'][$id] = $row['service_name'] ?? '';
			}
		}

		$ptRes = $callConfig->getAllRows("SELECT id, payment_term_name FROM payment_terms WHERE company_id = " . $companyId . " AND is_active = 0");
		if (count((array)$ptRes) > 0) {
			foreach ($ptRes as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$id = (int) ($row['id'] ?? 0);
				$key = strtolower(trim((string) ($row['payment_term_name'] ?? '')));
				if ($key != '') {
					$lookups['payment_terms'][$key] = $id;
				}
				$lookups['payment_terms_by_id'][$id] = $row['payment_term_name'] ?? '';
			}
		}

		$catRes = $callConfig->getAllRows("SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = " . $companyId . " AND is_active = 0 AND genc_code = 'CONTRACT_TYPE'");
		if (count((array)$catRes) > 0) {
			foreach ($catRes as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$code = trim((string) ($row['genc_detail_code'] ?? ''));
				$name = strtolower(trim((string) ($row['genc_detail_name'] ?? '')));
				if ($code != '') {
					$lookups['contract_categories'][$name] = $code;
					$lookups['contract_categories'][strtolower($code)] = $code;
				}
			}
		}

		$agrRes = $callConfig->getAllRows("SELECT id AS genc_detail_code, agreement_name AS genc_detail_name FROM contract_agreement_types WHERE company_id = " . $companyId . " AND is_active = 0");
		if (count((array)$agrRes) == 0) {
			$agrRes = $callConfig->getAllRows("SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = " . $companyId . " AND is_active = 0 AND genc_code = 'AGREEMENTS_TYPE'");
		}
		if (count((array)$agrRes) > 0) {
			foreach ($agrRes as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$code = trim((string) ($row['genc_detail_code'] ?? ''));
				$name = strtolower(trim((string) ($row['genc_detail_name'] ?? '')));
				if ($code != '') {
					$lookups['agreement_types'][$name] = $code;
					$lookups['agreement_types'][strtolower($code)] = $code;
				}
			}
		}

		$descRes = $callConfig->getAllRows("SELECT id, description_type, description FROM description_contracts WHERE company_id = " . $companyId . " AND is_active = 0");
		if (count((array)$descRes) > 0) {
			foreach ($descRes as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$id = (int) ($row['id'] ?? 0);
				$typeKey = strtolower(trim((string) ($row['description_type'] ?? '')));
				$descKey = strtolower(trim((string) ($row['description'] ?? '')));
				if ($typeKey != '') {
					$lookups['description_types'][$typeKey] = $id;
				}
				if ($descKey != '') {
					$lookups['description_types'][$descKey] = $id;
				}
				$lookups['description_types_by_id'][$id] = $row['description_type'] ?? '';
			}
		}

		$userRes = $callConfig->getAllRows("SELECT id, display_name, user_email FROM application_users WHERE company_id = " . $companyId . " AND is_active = 0");
		if (count((array)$userRes) > 0) {
			foreach ($userRes as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$id = (int) ($row['id'] ?? 0);
				$name = strtolower(trim((string) ($row['display_name'] ?? '')));
				$email = strtolower(trim((string) ($row['user_email'] ?? '')));
				if ($name != '') {
					$lookups['users'][$name] = $id;
				}
				if ($email != '') {
					$lookups['users'][$email] = $id;
				}

			}
		}

		$empRes = $callConfig->getAllRows("SELECT id, display_name, emp_code FROM employees WHERE company_id = " . $companyId . " AND is_active = 0");
		if (count((array)$empRes) > 0) {
			foreach ($empRes as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$id = (int) ($row['id'] ?? 0);
				$name = strtolower(trim((string) ($row['display_name'] ?? '')));
				$empCode = strtolower(trim((string) ($row['emp_code'] ?? '')));
				if ($name != '') {
					$lookups['employees'][$name] = $id;
				}
				if ($empCode != '') {
					$lookups['employees'][$empCode] = $id;
				}
				$lookups['employees_by_id'][$id] = $row['display_name'] ?? '';
			}
		}

		$freqDb = $callConfig->getAllRows("SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = " . $companyId . " AND is_active = 0 AND genc_code = 'AMCSERVICE'");
		$freqMerged = $this->mergeAmcServiceFrequencyOptions($freqDb);
		if (count((array)$freqMerged) > 0) {
			foreach ($freqMerged as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$code = $this->normalizeAmcServiceOn($row['genc_detail_code'] ?? '');
				$name = strtolower(trim((string) ($row['genc_detail_name'] ?? '')));
				if ($code != '') {
					$lookups['service_frequencies'][$name] = $code;
					$lookups['service_frequencies'][strtolower($code)] = $code;
				}
			}
		}

		return $lookups;
	}

	private function cexGroupRowsByImportRef($rows)
	{
		$grouped = array();
		if (count((array)$rows) > 0) {
			foreach ($rows as $row) {
				$row = is_array($row) ? $row : (array) $row;
				$ref = trim((string) ($row['import_ref'] ?? ''));
				if ($ref == '') {
					continue;
				}
				if (!isset($grouped[$ref])) {
					$grouped[$ref] = array();
				}
				$grouped[$ref][] = $row;
			}
		}
		return $grouped;
	}

	private function cexNormalizeContractType($value)
	{
		$v = strtolower(trim((string) $value));
		if ($v == 'amc') {
			return 'amc';
		}
		if ($v == 'standard' || $v == 'contract' || $v == '') {
			return 'standard';
		}
		return $v;
	}

	private function cexNormalizePaymentPlan($value)
	{
		$v = strtolower(trim((string) $value));
		$v = str_replace(array(' ', '-'), '_', $v);
		if ($v == 'milestones' || $v == 'milestone') {
			return 'milestones';
		}
		return 'billing_cycle';
	}

	private function cexResolvePartyId($partyType, $bpCode, $partyName, $lookups)
	{
		$partyType = strtolower(trim((string) $partyType));
		if ($partyType == 'inhouse') {
			return 0;
		}
		$bp = strtolower(trim((string) $bpCode));
		$name = strtolower(trim((string) $partyName));
		if ($partyType == 'customer') {
			if ($bp != '' && isset($lookups['customers_by_bp'][$bp])) {
				return (int) $lookups['customers_by_bp'][$bp];
			}
			if ($name != '' && isset($lookups['customers'][$name])) {
				return (int) $lookups['customers'][$name];
			}
		} else {
			if ($bp != '' && isset($lookups['vendors_by_bp'][$bp])) {
				return (int) $lookups['vendors_by_bp'][$bp];
			}
			if ($name != '' && isset($lookups['vendors'][$name])) {
				return (int) $lookups['vendors'][$name];
			}
		}
		return 0;
	}

	private function cexParseCsvValues($csv)
	{
		$items = array();
		$csv = trim((string) $csv);
		if ($csv == '') {
			return $items;
		}
		$parts = preg_split('/\s*,\s*/', $csv);
		if (count((array)$parts) > 0) {
			foreach ($parts as $part) {
				$part = trim((string) $part);
				if ($part != '') {
					$items[] = $part;
				}
			}
		}
		return $items;
	}

	private function cexValidateContractsExcelData($contracts, $locations, $assets, $milestones, $lookups)
	{
		$errors = array();
		$warnings = array();
		$contractPreviews = array();
		$contractsByRef = array();
		$validImportRefs = array();
		$seenRefs = array();

		$locationsByRef = $this->cexGroupRowsByImportRef($locations);
		$assetsByRef = $this->cexGroupRowsByImportRef($assets);
		$milestonesByRef = $this->cexGroupRowsByImportRef($milestones);

		$locationPreviews = array();
		if (count((array)$locations) > 0) {
			foreach ($locations as $locRow) {
				$locRow = is_array($locRow) ? $locRow : (array) $locRow;
				$locErrors = array();
				$ref = trim((string) ($locRow['import_ref'] ?? ''));
				$locName = trim((string) ($locRow['location_name'] ?? ''));
				$locKey = strtolower($locName);
				$locId = 0;
				if ($locName != '' && ctype_digit($locName) && isset($lookups['locations_by_id'][(int) $locName])) {
					$locId = (int) $locName;
				} elseif ($locKey != '' && isset($lookups['locations'][$locKey])) {
					$locId = (int) $lookups['locations'][$locKey];
				} elseif ($locName != '') {
					$locErrors[] = 'Unknown location: ' . $locName;
				}
				$locationPreviews[] = array('row' => $locRow, 'import_ref' => $ref, 'ok' => count((array)$locErrors) == 0, 'errors' => $locErrors, 'resolved' => array('location_id' => $locId));
			}
		}

		$assetPreviews = array();
		if (count((array)$assets) > 0) {
			foreach ($assets as $assetRow) {
				$assetRow = is_array($assetRow) ? $assetRow : (array) $assetRow;
				$assetErrors = array();
				$ref = trim((string) ($assetRow['import_ref'] ?? ''));
				$assetCode = strtolower(trim((string) ($assetRow['asset_code'] ?? '')));
				$assetId = 0;
				$assetName = '';
				if ($assetCode != '' && isset($lookups['assets_by_code'][$assetCode])) {
					$assetId = (int) ($lookups['assets_by_code'][$assetCode]['id'] ?? 0);
					$assetName = $lookups['assets_by_code'][$assetCode]['name'] ?? '';
				} elseif ($assetCode != '') {
					$assetErrors[] = 'Unknown asset_code: ' . ($assetRow['asset_code'] ?? '');
				}
				$freqRaw = trim((string) ($assetRow['service_frequency'] ?? ''));
				$freqKey = strtolower($freqRaw);
				$freqCode = $lookups['service_frequencies'][$freqKey] ?? $this->normalizeAmcServiceOn($freqRaw);
				if ($freqRaw != '' && $freqCode == '') {
					$assetErrors[] = 'Unknown service_frequency: ' . $freqRaw;
				}
				$assetPreviews[] = array(
					'row' => $assetRow,
					'import_ref' => $ref,
					'ok' => count((array)$assetErrors) == 0,
					'errors' => $assetErrors,
					'resolved' => array('asset_id' => $assetId, 'asset_name' => $assetName, 'service_frequency' => $freqCode),
				);
			}
		}

		$milestonePreviews = array();
		if (count((array)$milestones) > 0) {
			foreach ($milestones as $msRow) {
				$msRow = is_array($msRow) ? $msRow : (array) $msRow;
				$msErrors = array();
				$ref = trim((string) ($msRow['import_ref'] ?? ''));
				$msDate = cex_normalize_date_ymd($msRow['milestone_date'] ?? '');
				if (($msRow['milestone_date'] ?? '') != '' && $msDate == '') {
					$msErrors[] = 'Invalid milestone_date';
				}
				$milestonePreviews[] = array(
					'row' => $msRow,
					'import_ref' => $ref,
					'ok' => count((array)$msErrors) == 0,
					'errors' => $msErrors,
					'resolved' => array(
						'milestone_name' => trim((string) ($msRow['milestone_name'] ?? '')),
						'milestone_date' => $msDate,
						'milestone_price' => (float) ($msRow['milestone_amount'] ?? 0),
					),
				);
			}
		}

		if (count((array)$contracts) > 0) {
			foreach ($contracts as $contractRow) {
				$contractRow = is_array($contractRow) ? $contractRow : (array) $contractRow;
				$rowErrors = array();
				$importRef = trim((string) ($contractRow['import_ref'] ?? ''));
				if ($importRef == '') {
					$rowErrors[] = 'import_ref is required';
				} elseif (isset($seenRefs[$importRef])) {
					$rowErrors[] = 'Duplicate import_ref: ' . $importRef;
				} else {
					$seenRefs[$importRef] = 1;
				}

				$contractType = $this->cexNormalizeContractType($contractRow['contract_type'] ?? 'standard');
				if ($contractType != 'standard' && $contractType != 'amc') {
					$rowErrors[] = 'Invalid contract_type (use standard or amc)';
				}

				if (trim((string) ($contractRow['contract_name'] ?? '')) == '') {
					$rowErrors[] = 'contract_name is required';
				}

				$startDate = cex_normalize_date_ymd($contractRow['start_date'] ?? '');
				$endDate = cex_normalize_date_ymd($contractRow['end_date'] ?? '');
				if (($contractRow['start_date'] ?? '') != '' && $startDate == '') {
					$rowErrors[] = 'Invalid start_date';
				}
				if (($contractRow['end_date'] ?? '') != '' && $endDate == '') {
					$rowErrors[] = 'Invalid end_date';
				}
				if ($startDate != '' && $endDate != '' && $startDate > $endDate) {
					$rowErrors[] = 'start_date must be before end_date';
				}

				$amount = (float) ($contractRow['contract_amount'] ?? 0);
				if ($amount <= 0) {
					$rowErrors[] = 'contract_amount must be greater than zero';
				}

				$partyType = strtolower(trim((string) ($contractRow['party_type'] ?? 'vendor')));
				if (!in_array($partyType, array('vendor', 'customer', 'inhouse'))) {
					$rowErrors[] = 'Invalid party_type';
				}
				$partyId = $this->cexResolvePartyId($partyType, $contractRow['party_bp_code'] ?? '', $contractRow['party_name'] ?? '', $lookups);
				if ($partyType != 'inhouse' && $partyId <= 0) {
					$rowErrors[] = 'Unable to resolve party from party_bp_code or party_name';
				}

				$deptId = 0;
				$deptName = strtolower(trim((string) ($contractRow['department_name'] ?? '')));
				if ($deptName == '') {
					$rowErrors[] = 'department_name is required';
				} elseif (ctype_digit(trim((string) ($contractRow['department_name'] ?? ''))) && isset($lookups['departments_by_id'][(int) $contractRow['department_name']])) {
					$deptId = (int) $contractRow['department_name'];
				} elseif (isset($lookups['departments'][$deptName])) {
					$deptId = (int) $lookups['departments'][$deptName];
				} else {
					$rowErrors[] = 'Unknown department_name';
				}

				$paymentPlan = $this->cexNormalizePaymentPlan($contractRow['payment_plan'] ?? 'billing_cycle');
				$billingCycle = cex_normalize_billing_cycle($contractRow['billing_cycle'] ?? '');
				$billingDayRaw = trim((string) ($contractRow['billing_day'] ?? ''));
				$billingDay = ($billingDayRaw == '') ? 1 : (int) $billingDayRaw;
				if ($paymentPlan == 'billing_cycle') {
					if ($billingCycle == '') {
						$billingCycle = 'quarterly';
					}
					if (!in_array($billingCycle, array('monthly', 'quarterly', 'half_yearly', 'yearly'))) {
						$rowErrors[] = 'Invalid billing_cycle (use monthly, quarterly, half_yearly, yearly)';
					}
					if ($billingDay < 1 || $billingDay > 31) {
						$rowErrors[] = 'billing_day must be between 1 and 31';
					}
				} else {
					if ($billingCycle == '') {
						$billingCycle = 'quarterly';
					}
					if ($billingDay < 1 || $billingDay > 31) {
						$billingDay = 1;
					}
				}

				$categoryRaw = strtolower(trim((string) ($contractRow['contract_category'] ?? '')));
				$typeOfContractId = $lookups['contract_categories'][$categoryRaw] ?? trim((string) ($contractRow['contract_category'] ?? ''));

				$paymentTermId = 0;
				$paymentTermText = trim((string) ($contractRow['payment_terms'] ?? ''));
				$ptKey = strtolower($paymentTermText);
				if ($paymentTermText != '') {
					if (ctype_digit($paymentTermText) && isset($lookups['payment_terms_by_id'][(int) $paymentTermText])) {
						$paymentTermId = (int) $paymentTermText;
					} elseif (isset($lookups['payment_terms'][$ptKey])) {
						$paymentTermId = (int) $lookups['payment_terms'][$ptKey];
					}
					if ($paymentTermId <= 0) {
						$rowErrors[] = 'Unknown payment_terms: ' . $paymentTermText;
					}
				}

				$typeofaggrements = '';
				$agrRawText = trim((string) ($contractRow['agreement_type'] ?? ''));
				$agrRaw = strtolower($agrRawText);
				if ($agrRawText != '') {
					if (isset($lookups['agreement_types'][$agrRaw])) {
						$typeofaggrements = $lookups['agreement_types'][$agrRaw];
					} else {
						$rowErrors[] = 'Unknown agreement_type: ' . $agrRawText;
					}
				}

				$descriptionId = '';
				$descRawText = trim((string) ($contractRow['description_type'] ?? ''));
				$descRaw = strtolower($descRawText);
				if ($descRawText != '') {
					if (ctype_digit($descRawText) && isset($lookups['description_types_by_id'][(int) $descRawText])) {
						$descriptionId = (int) $descRawText;
					} elseif (isset($lookups['description_types'][$descRaw])) {
						$descriptionId = $lookups['description_types'][$descRaw];
					} else {
						$rowErrors[] = 'Unknown description_type: ' . $descRawText;
					}
				}

				$serviceIds = array();
				$serviceParts = $this->cexParseCsvValues($contractRow['services_csv'] ?? '');
				if (count((array)$serviceParts) > 0) {
					foreach ($serviceParts as $svcPart) {
						$svcKey = strtolower(trim((string) $svcPart));
						if (ctype_digit($svcPart) && isset($lookups['services_by_id'][(int) $svcPart])) {
							$serviceIds[] = (int) $svcPart;
						} elseif (isset($lookups['services'][$svcKey])) {
							$serviceIds[] = (int) $lookups['services'][$svcKey];
						} elseif ($svcPart != '') {
							$rowErrors[] = 'Unknown service: ' . $svcPart;
						}
					}
				}

				$locationIds = array();
				$amcLoc = array();
				$amcAsset = array();

				if ($contractType == 'amc') {
					$locRows = $locationsByRef[$importRef] ?? array();
					if (count((array)$locRows) == 0) {
						$rowErrors[] = 'AMC contract requires at least one Locations sheet row';
					} else {
						$locIdx = 0;
						if (count((array)$locRows) > 0) {
							foreach ($locRows as $locRow) {
								$locName = trim((string) ($locRow['location_name'] ?? ''));
								$locKey = strtolower($locName);
								$locId = 0;
								if ($locName != '' && ctype_digit($locName) && isset($lookups['locations_by_id'][(int) $locName])) {
									$locId = (int) $locName;
								} elseif ($locKey != '' && isset($lookups['locations'][$locKey])) {
									$locId = (int) $lookups['locations'][$locKey];
								} else {
									$rowErrors[] = 'Unknown AMC location: ' . $locName;
								}
								$locStart = cex_normalize_date_ymd($locRow['service_start_date'] ?? '') ?: $startDate;
								$locEnd = cex_normalize_date_ymd($locRow['service_end_date'] ?? '') ?: $endDate;
								$amcLoc[] = array(
									'location_id' => $locId,
									'service_start_date' => $locStart,
									'service_end_date' => $locEnd,
								);
								if ($locId > 0) {
									$locationIds[] = $locId;
								}

								$amcAsset[$locIdx] = array();
								$assetRowsForRef = $assetsByRef[$importRef] ?? array();
								if (count((array)$assetRowsForRef) > 0) {
									foreach ($assetRowsForRef as $assetRow) {
										$assetLocName = trim((string) ($assetRow['location_name'] ?? ''));
										if ($assetLocName != '' && strcasecmp($assetLocName, $locName) != 0) {
											continue;
										}
										$assetCode = strtolower(trim((string) ($assetRow['asset_code'] ?? '')));
										$assetInfo = $lookups['assets_by_code'][$assetCode] ?? null;
										if ($assetInfo == null) {
											$rowErrors[] = 'Unknown asset_code for location ' . $locName . ': ' . ($assetRow['asset_code'] ?? '');
											continue;
										}
										$freqRaw = trim((string) ($assetRow['service_frequency'] ?? ''));
										$freqCode = $lookups['service_frequencies'][strtolower($freqRaw)] ?? $this->normalizeAmcServiceOn($freqRaw);
										$amcAsset[$locIdx][] = array(
											'asset_id' => (int) ($assetInfo['id'] ?? 0),
											'asset_name' => $assetInfo['name'] ?? '',
											'service_frequency' => $freqCode,
											'visits_per_interval' => max(1, (int) ($assetRow['visits_per_interval'] ?? 1)),
											'service_start_date' => cex_normalize_date_ymd($assetRow['service_start_date'] ?? '') ?: $locStart,
											'service_end_date' => cex_normalize_date_ymd($assetRow['service_end_date'] ?? '') ?: $locEnd,
											'service_slots' => array(),
										);
									}
								}
								$locIdx++;
							}
						}
					}
					$hasAsset = false;
					if (count((array)$amcAsset) > 0) {
						foreach ($amcAsset as $assetList) {
							if (count((array)$assetList) > 0) {
								$hasAsset = true;
								break;
							}
						}
					}
					if (!$hasAsset) {
						$rowErrors[] = 'AMC contract requires at least one Assets sheet row';
					}
				} else {
					$locParts = $this->cexParseCsvValues($contractRow['locations_csv'] ?? '');
					if (count((array)$locParts) > 0) {
						foreach ($locParts as $locPart) {
							$locKey = strtolower(trim((string) $locPart));
							if (ctype_digit($locPart) && isset($lookups['locations_by_id'][(int) $locPart])) {
								$locationIds[] = (int) $locPart;
							} elseif (isset($lookups['locations'][$locKey])) {
								$locationIds[] = (int) $lookups['locations'][$locKey];
							} elseif ($locPart != '') {
								$rowErrors[] = 'Unknown location in locations_csv: ' . $locPart;
							}
						}
					}
				}

				$resolvedMilestones = array();
				if ($paymentPlan == 'milestones') {
					$msRows = $milestonesByRef[$importRef] ?? array();
					if (count((array)$msRows) == 0) {
						$rowErrors[] = 'payment_plan milestones requires Milestones sheet rows';
					} else {
						$msTotal = 0;
						if (count((array)$msRows) > 0) {
							foreach ($msRows as $msRow) {
								$msName = trim((string) ($msRow['milestone_name'] ?? ''));
								if ($msName == '') {
									$rowErrors[] = 'Milestone name is required';
									continue;
								}
								$msDate = cex_normalize_date_ymd($msRow['milestone_date'] ?? '');
								if (($msRow['milestone_date'] ?? '') != '' && $msDate == '') {
									$rowErrors[] = 'Invalid milestone_date for ' . $msName;
								}
								$msPrice = (float) ($msRow['milestone_amount'] ?? 0);
								$msTotal += $msPrice;
								$resolvedMilestones[] = array(
									'milestone_name' => $msName,
									'milestone_date' => $msDate,
									'milestone_price' => $msPrice,
								);
							}
						}
						if ($msTotal > 0 && abs($msTotal - $amount) > 0.05) {
							$warnings[] = 'import_ref ' . $importRef . ': milestone total (' . $msTotal . ') differs from contract_amount (' . $amount . ')';
						}
					}
				} elseif ($startDate != '' && $endDate != '') {
					$resolvedMilestones = cex_generate_billing_milestones($startDate, $endDate, $billingCycle, $billingDay, $amount);
				}

				$customerConnectId = 0;
				$ccNameRaw = trim((string) ($contractRow['customer_connect_name'] ?? ''));
				$ccName = strtolower($ccNameRaw);
				if ($ccNameRaw != '') {
					if (isset($lookups['customers'][$ccName])) {
						$customerConnectId = (int) $lookups['customers'][$ccName];
					} else {
						$rowErrors[] = 'Unknown customer_connect_name: ' . $ccNameRaw;
					}
				}

				$defaultEmployeeId = 0;
				$empNameRaw = trim((string) ($contractRow['default_employee_name'] ?? ''));
				$empName = strtolower($empNameRaw);
				if ($empNameRaw != '') {
					if (ctype_digit($empNameRaw) && isset($lookups['employees_by_id'][(int) $empNameRaw])) {
						$defaultEmployeeId = (int) $empNameRaw;
					} elseif (isset($lookups['employees'][$empName])) {
						$defaultEmployeeId = (int) $lookups['employees'][$empName];
					} else {
						$rowErrors[] = 'Unknown default_employee_name: ' . $empNameRaw;
					}
				}

				$isOem = strtolower(trim((string) ($contractRow['is_oem'] ?? '')));
				$isOemVal = ($isOem == 'y' || $isOem == 'yes' || $isOem == '1') ? 'Y' : 'N';

				$preview = array(
					'row' => $contractRow,
					'import_ref' => $importRef,
					'ok' => count((array)$rowErrors) == 0,
					'errors' => $rowErrors,
					'resolved' => array(
						'contract_type' => $contractType,
						'start_date' => $startDate,
						'end_date' => $endDate,
						'contract_amount' => $amount,
						'party_type' => $partyType,
						'party_id' => $partyId,
						'department_id' => $deptId,
						'payment_plan' => $paymentPlan,
						'billing_cycle' => $billingCycle,
						'billing_day' => $billingDay,
						'type_of_contract_id' => $typeOfContractId,
						'payment_term_id' => $paymentTermId,
						'contract_payment_terms' => $paymentTermId > 0 ? $paymentTermId : '',
						'typeofaggrements' => $typeofaggrements,
						'description_id' => $descriptionId,
						'contract_services_id' => $serviceIds,
						'locations' => array_values(array_unique($locationIds)),
						'amc_loc' => $amcLoc,
						'amc_asset' => $amcAsset,
						'milestones' => $resolvedMilestones,
						'customer_connect_id' => $customerConnectId,
						'amc_default_employee_id' => $defaultEmployeeId,
						'is_oem' => $isOemVal,
					),
				);

				$contractPreviews[] = $preview;
				if ($importRef != '') {
					$contractsByRef[$importRef] = $preview;
					if ($preview['ok']) {
						$validImportRefs[] = $importRef;
					} else {
						if (count((array)$rowErrors) > 0) {
							foreach ($rowErrors as $errMsg) {
								$errors[] = 'import_ref ' . $importRef . ': ' . $errMsg;
							}
						}
					}
				}
			}
		}

		return array(
			'contracts' => $contractPreviews,
			'contracts_by_ref' => $contractsByRef,
			'locations' => $locationPreviews,
			'assets' => $assetPreviews,
			'milestones' => $milestonePreviews,
			'errors' => $errors,
			'warnings' => $warnings,
			'valid_import_refs' => $validImportRefs,
		);
	}

	private function cexBuildContractImportPayload($contractPreview, $validation, $get, $post)
	{
		$row = $contractPreview['row'] ?? array();
		$res = $contractPreview['resolved'] ?? array();
		$contractType = $res['contract_type'] ?? 'standard';
		$isAmc = ($contractType == 'amc');

		$payload = array(
			'contract_name' => trim((string) ($row['contract_name'] ?? '')),
			'invoice_number' => trim((string) ($row['invoice_number'] ?? '')),
			'rt_number' => trim((string) ($row['rt_number'] ?? '')),
			'business_code' => trim((string) ($row['business_code'] ?? '')),
			'parent_contract_id' => 0,
			'contract_payment_terms' => $res['contract_payment_terms'] ?? '',
			'locations' => $res['locations'] ?? array(),
			'typeofaggrements' => $res['typeofaggrements'] ?? '',
			'party_type' => $res['party_type'] ?? 'vendor',
			'contract_owner_name' => trim((string) ($row['contract_owner_name'] ?? '')),
			'contract_owner_email' => trim((string) ($row['contract_owner_email'] ?? '')),
			'contract_owner_number' => trim((string) ($row['contract_owner_number'] ?? '')),
			'party_id' => $res['party_id'] ?? 0,
			'department_id' => $res['department_id'] ?? 0,
			'type_of_contract_id' => $res['type_of_contract_id'] ?? '',
			'start_date' => $res['start_date'] ?? '',
			'end_date' => $res['end_date'] ?? '',
			'renewal_frequency' => 'quarterly',
			'contract_amount' => $res['contract_amount'] ?? 0,
			'description' => trim((string) ($row['description'] ?? '')),
			'additional_notes' => trim((string) ($row['additional_notes'] ?? '')),
			'payment_term_id' => $res['payment_term_id'] ?? '',
			'termination_condition' => trim((string) ($row['termination_condition'] ?? '')),
			'contract_status' => 'Pending',
			'auto_renew' => false,
			'renew_type' => '',
			'state' => '',
			'isp' => '',
			'paid_by' => '',
			'internet_charges' => '',
			'hub_spoc_name' => '',
			'hub_spoc_number' => '',
			'wifi_provider' => '',
			'wifi_pass' => '',
			'additional_document' => '',
			'description_id' => $res['description_id'] ?? '',
			'is_oem' => ($res['is_oem'] ?? 'N') == 'Y' ? 'Yes' : 'No',
			'oem_registration_number' => trim((string) ($row['oem_registration_number'] ?? '')),
			'oem_distributor_name' => trim((string) ($row['oem_distributor_name'] ?? '')),
			'oem_product_category' => trim((string) ($row['oem_product_category'] ?? '')),
			'contract_services_id' => $res['contract_services_id'] ?? array(),
			'contact_persons' => array('name' => array(), 'email' => array(), 'number' => array()),
			'milestones' => $res['milestones'] ?? array(),
			'permission_data' => $post['permission_data'] ?? array(),
			'workflow_data' => $post['workflow_data'] ?? array(),
			'document_data' => $post['document_data'] ?? array(),
			'version_id' => $post['version_id'] ?? 0,
			'amc_payment_mode' => $res['payment_plan'] ?? 'billing_cycle',
			'amc_billing_cycle' => $res['billing_cycle'] ?? 'quarterly',
			'amc_billing_day' => $res['billing_day'] ?? 1,
		);

		if ($isAmc) {
			$payload['is_amc_contract'] = 'Y';
			$payload['amc_loc'] = $res['amc_loc'] ?? array();
			$payload['amc_asset'] = $res['amc_asset'] ?? array();
			$payload['customer_connect_id'] = $res['customer_connect_id'] ?? '';
			$payload['amc_default_employee_id'] = $res['amc_default_employee_id'] ?? '';
			$payload['amc_service_mode'] = (($res['party_type'] ?? '') == 'inhouse') ? 'inhouse' : 'vendor';
			$payload['gate_entry'] = 'yes';
			$payload['reminder_days'] = array('15');
			$payload['completion_req'] = array('service_report', 'technician_signoff', 'customer_signoff');
			$payload['amc_escalation_config'] = array(
				array('condition' => 'not_started', 'hours' => 4, 'notify_role' => 'service_manager'),
				array('condition' => 'not_completed', 'hours' => 24, 'notify_role' => 'regional_manager'),
			);
		} else {
			$payload['is_amc_contract'] = 'N';
		}

		return $payload;
	}

	//contract excel import functions end

	}
	?>
