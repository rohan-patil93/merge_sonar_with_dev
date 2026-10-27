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














/**	function toGETNavDetails($type,$menu,$admin_type,$rolepermission){
		//$menu  = $_SESSION['application_menu'];
		$application_no_permission_pages =$rolepermission['application_no_permission_pages'];
		$application_pages = $rolepermission['application_pages'];
		foreach ($menu as $key => $value) { 
			if($value->menu_location == $type && $value->parent_menu == 0){
				
				if($admin_type==0){
				  if(in_array($value->page_name,(array)$application_no_permission_pages['option'])){
				  	$res = 1;
				  }else{
				  	$res = $this->checkPremission($value->page_name,$application_pages,$rolepermission);
				  }
				  
				}else{
				  $res = 1;
				}
				$child = $this->getParentidmenus($value->id,1,$application_pages,$admin_type,$rolepermission);
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
		return $array;
	}
	function checkPremission($name,$application_pages,$rolepermission){
		$return = 0;
		$permission = $rolepermission['permission'];
		$page_permission = $rolepermission['page_permission'];
		if(strpos($name,'&')){
			$explode = explode('&', $name);
			$name = $explode[0];
		}
		foreach ($application_pages as  $value) {
			if($value->list_page_name==$name || $value->insert_page_name==$name || $value->edit_page_name==$name || $value->view_page_name==$name){
				$table = $value->application_table_name;
				if($value->list_page_name==$name){	$type = 'permission_list';	}
				if($value->insert_page_name==$name){	$approvaltype = 'permission_approval';	}
				if($value->insert_page_name==$name){	$type = 'permission_insert'; }
				if($value->edit_page_name==$name){	$type = 'permission_update';	}
				if($value->view_page_name==$name){	$type = 'permission_list';	}
			}
		}

		if(isset($permission[$table][$type]) && $permission[$table][$type] == 0){
			$return =1;
		}
		if($return == 0){
			if( isset($page_permission[$name]) && $page_permission[$name]['permission_list'] == 0){
				$return =1;	
			}
		}
		//approval check
		if(isset($permission[$table][$approvaltype]) && $permission[$table][$approvaltype] == 0){
			$return =1;
		}
		//print_r($_SESSION['role_permission']);	
		echo $name." -- ".$table." -- ".$type." --- ".$return."</br></br></br></br></br>"; //exit;
		//approval check
		return $return; 
	}



	function getParentidmenus($pid,$len,$menu,$admin_type,$rolepermission){
		//$menu  = $_SESSION['application_menu'];
		$application_no_permission_pages =$rolepermission['application_no_permission_pages'];
		$application_pages = $rolepermission['application_pages'];
		$array1 = array();
		foreach ($menu as $key => $value) {
			$res = 1;
			if($value->parent_menu == $pid && $admin_type==0){
				$child = array();
				$child = $this->getParentidmenuscheck($value->id,$len+1,$menu,$admin_type);
				if(count((array)$child)>0){
		 			$res=1;
				}else{
				  if(in_array($value->page_name,(array)$application_no_permission_pages['option'])){
				  	$res = 1;
				  }else{ 
				  	$res=$this->checkPremission($value->page_name,$application_pages,$rolepermission);
				  }
				}
			}
			
			if($value->parent_menu == $pid && $res == 1){
				$array1[$value->id]['id'] =  $value->id;
				$array1[$value->id]['Label'] =  $value->menu_label;
				$array1[$value->id]['Menu_Icon'] =  $value->menu_icon;
				$array1[$value->id]['Page_Name'] =  $value->page_name;
				$array1[$value->id]['dashboard'] =  $value->dashboard;
				//$array1[$value->id]['child'] = $rolepermission;
				$array1[$value->id]['child'] = $this->getParentidmenus($value->id,$len+1,$menu,$admin_type,$rolepermission);
			} 
		}
		return $array1; 
	}


	function getParentidmenuscheck($pid,$len,$menu,$admin_type){
		//$menu  = $_SESSION['application_menu'];
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
**/
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


	//rect function added by omkar - 2025-04-10
	// function insertcontractsreact($get, $post)
	// {
	// 	global $callConfig;

	// 	$return = [];
	// 	$return['post'] = $post;
	// 	$return['get'] = $get;

	// 	$locations = '';
	// 	if (isset($post['locations']) && is_array($post['locations'])) { 
	// 		$locations = json_encode($post['locations']); 
	// 	}


	// 	$insert_array = array( 'contract_name' => $post['contract_name'],'renewal_frequency' => $post['renewal_frequency'], 'parent_contract_id' => $post['parent_contract_id'], 'invoice_number' => $post['invoice_number'], 'rt_number' => $post['rt_number'], 'business_code' => $post['business_code'], 'party_type' => $post['party_type'], 'contract_owner_name' => $post['contract_owner_name'], 'contract_owner_email' => $post['contract_owner_email'], 'contract_owner_number' => $post['contract_owner_number'], 'party_id' => $post['party_id'], 'start_date' => $this->dateconvertdbformat($post['start_date']), 'end_date' => $this->dateconvertdbformat($post['end_date']), 'contract_amount' => $post['contract_amount'], 'description' => $post['description'], 'additional_notes' => $post['additional_notes'], 'payment_term_id' => $post['payment_term_id'], 'termination_condition' => $post['termination_condition'], 'contract_status' => $post['contract_status'], 'current_status' => 0, 'auto_renew' => $post['auto_renew'], 'renew_type' => $post['renew_type'], 'state' => $post['state'], 'isp' => $post['isp'], 'paid_by' => $post['paid_by'], 'internet_charges' => $post['internet_charges'], 'hub_spoc_name' => $post['hub_spoc_name'], 'hub_spoc_number' => $post['hub_spoc_number'], 'wifi_pass' => $post['wifi_pass'], 'wifi_provider' => $post['wifi_provider'], 'additional_document' => $post['additional_document'], 'contract_payment_terms' => $post['contract_payment_terms'], 'typeofaggrements' => $post['typeofaggrements'], 'description_id' => $post['description_id'], 'locations' => $locations,'is_oem' => $post['is_oem'] ?? 'No', 'oem_registration_number' => $post['oem_registration_number'] ?? '', 'oem_distributor_name' => $post['oem_distributor_name'] ?? '', 'oem_product_category' => $post['oem_product_category'] ?? ''); // additional document new - stores comma-separated filenames

	// 	if (!empty($post['Rid'])) {
	// 		$insert_array['updated_by'] = $get['user_id'];
	// 		$insert_array['updated_date'] = date('Y-m-d H:i:s');
	// 		$res = $callConfig->updateRecord('contracts', $insert_array, 'id', $post['Rid']);
	// 		$contract_id = $post['Rid'];
	// 		$return['contract_result'] = $res;
	// 	} else {
	// 		$insert_array['company_id'] = $get['company_id'];
	// 		$insert_array['is_active'] = 0;
	// 		$insert_array['transaction_no'] = $this->toGetApplicationSerialNumber('contracts',$post);
	// 		$insert_array['created_by'] = $get['user_id'];
	// 		$insert_array['originator'] = $get['user_id'];
	// 		$insert_array['created_date'] = date('Y-m-d H:i:s');
	// 		$insert_data = $callConfig->insertRecord('contracts', $insert_array);
	// 		$contract_id = $insert_data['LASTID'];
	// 		$return['contract_result'] = $insert_data;
	// 	}
 
	// 	$control_array = array(
	// 		'permissions' => isset($post['permission_data']) ? json_encode($post['permission_data']) : json_encode([]),
	// 		'workflow' => isset($post['workflow_data']) ? json_encode($post['workflow_data']) : json_encode([]),
	// 		'documents' => isset($post['document_data']) ? json_encode($post['document_data']) : json_encode([]),
	// 		'contract_details' => json_encode($insert_array), 'version_id' => $post['version_id'] ?? 0, 'company_id' => $get['company_id'] ?? 0, 'contract_id' => $contract_id, 'is_active' => 0, 'created_by' => $get['user_id'] ?? 0, 'created_date' => date('Y-m-d H:i:s') );
	// 	$control_insert = $callConfig->insertRecord('contract_version', $control_array);
	// 	$return['control_version_result'] = $control_insert;

	// 	// ---- Contract Activity ----
	// 	$activity_array = array( 'company_id' => $get['company_id'] ?? 0, 'activity' => 'contract', 'contract_id' => $contract_id, 'activity_date' => date('Y-m-d H:i:s'), 'is_active' => 0, 'created_by' => $get['user_id'] ?? 0, 'created_date' => date('Y-m-d H:i:s') );
	// 	$activity_insert = $callConfig->insertRecord('contract_activity', $activity_array);
	// 	$return['activity_result'] = $activity_insert;

	// 	if (isset($post['milestones']) && is_array($post['milestones']) && count($post['milestones']) > 0) {
 
	// 		if (!empty($post['Rid'])) {
	// 			$updateQuery = "UPDATE milestone_contracts SET is_active = NULL, updated_date = '" . date('Y-m-d H:i:s') . "',  updated_by = '" . addslashes($get['user_id']) . "' WHERE transaction_id = '" . addslashes($contract_id) . "'";
	// 			$callConfig->executeQuery($updateQuery);
	// 		}
 
	// 		foreach ($post['milestones'] as $milestone) { 
	// 			if (empty(trim($milestone['milestone_name'] ?? '')))
	// 				continue; 
	// 			$milestoneData = ['company_id' => $get['company_id'], 'transaction_id' => $contract_id, 'transaction_date' => date('Y-m-d H:i:s'), 'milestone' => trim($milestone['milestone_name']), 'milestone_date' => !empty($milestone['milestone_date']) ? $this->dateconvertdbformat($milestone['milestone_date']) : null, 'milestone_price' => !empty($milestone['milestone_price']) ? (float) $milestone['milestone_price'] : 0, 'is_active' => 0, 'current_status' => 0, 'status_date' => date('Y-m-d H:i:s'), 'created_date' => date('Y-m-d H:i:s'), 'created_by' => $get['user_id'], 'updated_by' => $get['user_id'], 'updated_date' => date('Y-m-d H:i:s'), ];

	// 			$milestoneInsert = $callConfig->insertRecord('milestone_contracts', $milestoneData);
	// 			$return['milestone_inserts'][] = $milestoneInsert;
	// 		}

	// 	} else {
	// 		$return['milestone_inserts'] = 'No milestones found in POST.';
	// 	}


	// 	return $return;
	// }

	function insertcontractsreact($get, $post)
	{
		global $callConfig;

		$return = [];
		$return['post'] = $post;
		$return['get'] = $get;

		$locations = '';
		if (isset($post['locations']) && is_array($post['locations'])) {
			$locations = json_encode($post['locations']);
		}


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
			'cancel_status' => 'N'
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
				if($insert_array['originator'] !=""){
					$conditins = " AND (JSON_SEARCH(role_id, 'one', 'CONTRACT_ADMIN') IS NOT NULL OR id = '".$insert_array['originator']."')";
				}else{
					$conditins = " AND JSON_SEARCH(role_id, 'one', 'CONTRACT_ADMIN') IS NOT NULL";
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

		return $return;
	}
	function getcontractslist($get)
	{
		global $callConfig;
		$return = array();
		$contractssql = "SELECT * FROM contracts WHERE company_id =" . $get['company_id'] . " AND is_active = 0 ORDER BY id ASC";
		$contractsres = $callConfig->getAllRows($contractssql);
		$return['result'] = $contractsres;
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
					$hwr = " AND user_id = ".$get['user_id'];
				}
				if($get['LOCID']!=''){
					$hwr = " AND JSON_SEARCH(locations, 'one', '".$get['LOCID']."') IS NOT NULL";
				}
        $allcontractssql = "SELECT id, `originator`, `contract_name`, `transaction_no`, `party_type`, `party_id`, `parent_contract_id`, `start_date`, `end_date`, `description`, `additional_notes`, `payment_term_id`, `termination_condition`, `contract_status`, `auto_renew`, `renew_type`, `owner_user_id`, `contract_owner_name`, `contract_owner_email`, `contract_owner_number`, `rt_number`, `current_status`,  `created_date`, locations, contract_type  FROM contracts WHERE company_id =" . $get['company_id'] . " ".$hwr." AND is_active = 0";
        $allcontractsres = $callConfig->getAllRows($allcontractssql);
        $return['allcontractssql'] = $allcontractssql;
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

		$Associatedcontractsql = "SELECT `id`, `company_id`, `originator`, `contract_name`, `transaction_no`, `contract_owner_name`, `contract_owner_email`, `contract_owner_number`, `invoice_number`, `rt_number`, `party_type`, `party_id`, `parent_contract_id`, `start_date`, `end_date`, `description`, `additional_notes`, `payment_term_id`, `termination_condition`, `contract_status`, `auto_renew`, `renew_type`, `contract_payment_terms`, `is_active` FROM contracts WHERE company_id =" . $get['company_id'] . " AND parent_contract_id = '" . $rid . "' AND is_active = 0";
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
        'transaction_no' => toGetApplicationSerialNumber('contracts', $post),
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
        'start_date' => dateconvertdbformat($post['start_date']),
        'end_date' => dateconvertdbformat($post['end_date']),
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
        'paid_by' =>
            $post['paid_by'],
        'internet_charges' => $post['internet_charges'],
        'hub_spoc_name' => $post['hub_spoc_name'],
        'hub_spoc_number' => $post['hub_spoc_number'],
        'wifi_pass' => $post['wifi_pass'],
        'wifi_provider' =>
            $post['wifi_provider'],
        'additional_document' => $post['additional_document'],
        'contract_payment_terms' =>
            $post['contract_payment_terms'],
        'typeofaggrements' => $post['typeofaggrements'],
        'description_id' =>
            $post['description_id'],
        'locations' => $locations,
        'parent_contract' => $post['Rid'],
        'is_oem' => (isset($post['is_oem']) && ($post['is_oem'] == 'Yes' || $post['is_oem'] == 'Y')) ? 'Y' : 'N',
        'oem_registration_number' => $post['oem_registration_number'] ?? '',
        'oem_distributor_name' =>
            $post['oem_distributor_name'] ?? '',
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
                'transaction_date' => date('Y-m-d
H:i:s'),
                'milestone' => trim($milestone['milestone_name']),
                'milestone_date' => !empty($milestone['milestone_date']) ?
                    $milestone['milestone_date'] : null,
                'milestone_price' => !empty($milestone['milestone_price']) ? (float) 
                    $milestone['milestone_price'] : 0,
                'is_active' => 0,
                'current_status' => 0,
                'status_date' => date('Y-m-d H:i:s'),
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $get['user_id'],
                'updated_by' => $get['user_id'],
                'updated_date'
                => date('Y-m-d H:i:s'),
            ];

            $milestoneInsert = $callConfig->insertRecord('contract_milestones', $milestoneData);
            $return['milestone_inserts'][] = $milestoneInsert;
        }
    } else {
        $return['milestone_inserts'] = 'No milestones found in POST.';
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

}
?>