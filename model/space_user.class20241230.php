<?php 
class spaceUserClass extends userClass{
	function insertBuildings($get,$post){
		global $callConfig;
		$building_floors = $post['building_floors'];
		$insertary=array('building_name'=>$post['building_name'],'building_address'=>$post['building_address'],'building_image'=>$post['building_image'],'location_id'=>$post['location_id'],'originator'=>$post['originator']);
		if($post['rid'] !=""){
			$insertary['updated_date']=date('Y-m-d h:i:s');
			$insertary['updated_by']=$post['user_id'];
			$return['result'] = $callConfig->updateRecord('buildings',$insertary,'id',$post['rid']);
			$building_id = $post['rid'];
		}else{
			$insertary['created_by']=$post['user_id'];
			$insertary['company_id']=$post['company_id'];
			$insertary['is_active']=0;
			$insertary['created_date']=date("Y-m-d H:i:s");
			$tapres = $callConfig->insertRecord('buildings',$insertary);
			$building_id = $tapres['LASTID'];
			$return['result'] =$tapres;	
		}
		
		$wh = "company_id = '".$post['company_id']."' AND is_active =0 AND building_id = '".$building_id."'";
		$callConfig->updateRecord('building_floors',array('is_active'=>null),'','',$wh); // all disabled

		foreach($building_floors['floor_name'] as $key=>$keyvalue){
			$insertary=array('building_id'=>$building_id,'floor_name'=>$building_floors['floor_name'][$key],'floor_image'=>$building_floors['floor_image'][$key],'is_active'=>'0');
			if($post['building_floors_id'][$key] !=""){
				$insertary['updated_date']=date('Y-m-d h:i:s');
				$insertary['updated_by']=$post['user_id'];
				$return['building_floors'][] = $callConfig->updateRecord('building_floors',$insertary,'id',$post['building_floors_id'][$key]);
			}else{
				$insertary['created_by']=$post['user_id'];
				$insertary['company_id']=$post['company_id'];
				$insertary['created_date']=date("Y-m-d H:i:s");
				$tapres = $callConfig->insertRecord('building_floors',$insertary);
				$return['building_floors'][] =$tapres;	
			}
		}
		return $return;
	}
	
	function getBuildings($get){
		global $callConfig;	
		$buildingssql = "SELECT * FROM buildings WHERE company_id = '".$get['company_id']."'  AND  id = '".$get['rid']."' AND is_active = 0";
		$buildingsres = $callConfig->getRow($buildingssql);
		$return['buildings'] =$buildingsres;

		$building_floorssql = "SELECT id,building_id,floor_name,floor_image FROM building_floors WHERE company_id = '".$get['company_id']."'  AND  building_id = '".$get['rid']."' AND is_active = 0";
		$building_floorsres = $callConfig->getAllRows($building_floorssql);
		$return['building_floors'] =$building_floorsres;

		$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND JSON_SEARCH(role_id, 'one', '".BUILDING_ADMIN_ROLE."') IS NOT NULL";
		$application_usersres = $callConfig->getAllRows($application_userssql);
		$return['building_admin_application_users'] =$application_usersres;

		$locationssql = "SELECT id,location_name FROM locations WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$locationsres = $callConfig->getAllRows($locationssql);
		$return['locations'] =$locationsres;

		return $return;
	}
	function getBuildingFloors($get){
		global $callConfig;	
		
		

		$building_floorssql = "SELECT id,building_id,floor_name,floor_image FROM building_floors WHERE company_id = '".$get['company_id']."'  AND  id = '".$get['rid']."' AND is_active = 0";
		$building_floorsres = $callConfig->getRow($building_floorssql);
		$return['building_floors'] =$building_floorsres;

		$buildingssql = "SELECT id, building_name, building_address FROM buildings WHERE company_id = '".$get['company_id']."'  AND  id = '".$building_floorsres->building_id."' AND is_active = 0";
		$buildingsres = $callConfig->getRow($buildingssql);
		$return['buildings'] =$buildingsres;

		$allbuilding_floorssql = "SELECT id,building_id,floor_name,floor_image FROM building_floors WHERE company_id = '".$get['company_id']."'  AND building_id = '".$building_floorsres->building_id."' AND is_active = 0";
		$allbuilding_floorsres = $callConfig->getAllRows($allbuilding_floorssql);
		$return['all_building_floors'] =$allbuilding_floorsres;

		$space_typesql = "SELECT id,space_type_name FROM space_types WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$space_typeres = $callConfig->getAllRows($space_typesql);
		$return['space_types'] =$space_typeres;

		$building_floor_spacessql = "SELECT * FROM building_floor_spaces WHERE company_id = '".$get['company_id']."' AND  building_floor_id = '".$get['rid']."' AND is_active = 0";
		$building_floor_spacesres = $callConfig->getAllRows($building_floor_spacessql);
		$return['building_floor_spaces'] =$building_floor_spacesres;

		foreach($building_floor_spacesres as $bfspae){
			$buildingfloorspaceid[$bfspae->id] = $bfspae->id;
		}
		$building_floor_space_id = implode(',', (array)$buildingfloorspaceid);
		$building_floor_space_allocationssql = "SELECT id, building_floor_space_id, emp_id, emp_allocation_datetime, reserve_pool_id, reserve_pool_allocation_datetime FROM `building_floor_space_allocations` WHERE company_id = '".$get['company_id']."' AND building_floor_space_id IN(".$building_floor_space_id.") AND is_active = 0";
		$building_floor_space_allocationsres = $callConfig->getAllRows($building_floor_space_allocationssql);
		$return['building_floor_space_allocations'] =$building_floor_space_allocationsres;

		$employeessql = "SELECT id, display_name, emp_code, email_id, image FROM employees WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$employeesres = $callConfig->getAllRows($employeessql);
		$return['employees'] =$employeesres;


		$seating_poolssql = "SELECT id, pool_name, bg_color FROM seating_pools WHERE transaction_table = 'building_floor_space_allocations' AND company_id = '".$get['company_id']."' AND is_active = 0";
		$seating_poolsres=$callConfig->getAllRows($seating_poolssql);
		foreach($seating_poolsres as $resrv){
			$seating_pools[$resrv->id] = $resrv;
		}
		$return['seating_pools'] = $seating_pools;


		$assetssql ="SELECT * FROM assets WHERE company_id = '".$get['company_id']."' AND id = '".$get['asset_id']."' AND is_active = 0";
		$assetsres = $callConfig->getRow($assetssql);
		$return['assets'] =$assetsres;
		
		$assetssql ="SELECT * FROM asset_allocations WHERE company_id = '".$get['company_id']."' AND  building_floor_id = '".$get['rid']."' AND asset_id = '".$get['asset_id']."' AND is_active = 0";
		$assetsres = $callConfig->getRow($assetssql);
		$return['asset_allocations'] =$assetsres;

		return $return;
	}
	function insertBuildingFloorCabins($get,$post){
		global $callConfig;
		$return['post'] =$post;	
		$insertary=array('building_floor_id'=>$post['building_floor_id'],'cabin_name'=>$post['cabin_name'],'space_type_id'=>$post['space_type_id'],'x_size'=>$post['x_size'],'y_size'=>$post['y_size'],'width'=>$post['width'],'height'=>$post['height']);
		if($post['building_floor_cabin_id'] !=""){
			$insertary['updated_date']=date('Y-m-d h:i:s');
			$insertary['updated_by']=$post['user_id'];
			$return['result'] = $callConfig->updateRecord('building_floor_spaces',$insertary,'id',$post['building_floor_cabin_id']);
			$building_id = $post['building_floor_cabin_id'];
		}else{
			$insertary['created_by']=$post['user_id'];
			$insertary['company_id']=$post['company_id'];
			$insertary['is_active']=0;
			$insertary['created_date']=date("Y-m-d H:i:s");
			$tapres = $callConfig->insertRecord('building_floor_spaces',$insertary);
			$building_id = $tapres['LASTID'];
			$return['result'] =$tapres;	
		}
		return $return;

	}
	function getEquipmentAllocations($get){
		global $callConfig;
		// if($get['gettype']=="buildings_list"){
		// 	$buildingssql = "SELECT id, building_name, building_address,building_image FROM buildings WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		// 	$buildingsres = $callConfig->getAllRows($buildingssql);
		// 	$buildingresult = array();
		// 	foreach($buildingsres as $buldg){
		// 		$buildingresult[$buldg->id] = $buldg;
		// 	}
		// 	$return['buildings'] =$buildingresult;

		// 	$building_floorssql = "SELECT id, floor_name, building_id, floor_image FROM building_floors WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		// 	$building_floorsres = $callConfig->getAllRows($building_floorssql);
		// 	$building_floors =$building_with_floors = array();
		// 	foreach($building_floorsres as $buldg){
		// 		$building_with_floors[$buldg->building_id][$buldg->id] = $buldg;
		// 		$building_floors[$buldg->id] = $buldg;
		// 	}
		// 	$return['building_with_floors'] =$building_with_floors;
		// 	$return['building_floors'] =$building_floors;

		// }else{
		if($get['pagetype'] == 'HOME'){

			if($get['admin_type']!='1'){
				$condition = "AND originator = '".$get['user_id']."'";
				$approvalcondition = "AND approval_user_id = '".$get['user_id']."'";
			}
			$buildings_id = array();
			$buildingssql = "SELECT id, building_name, building_address, building_image FROM buildings WHERE company_id = '".$get['company_id']."' ".$condition." AND is_active = 0";
			$buildingsres = $callConfig->getAllRows($buildingssql);
			foreach($buildingsres as $build){
				$buildings_id[$build->id] = $build->id;
			}
			$return['buildings_report'] =$buildingsres;
			$building_ids = implode(',', (array)$buildings_id);


			$building_floorssql = "SELECT id, building_id, floor_name FROM building_floors WHERE company_id = '".$get['company_id']."' AND building_id IN(".$building_ids.") AND is_active = 0";
			$building_floorsres = $callConfig->getAllRows($building_floorssql);
			foreach($building_floorsres as $bfloor){
				$building_floors_id[$bfloor->id] = $bfloor->id;
			}
			$building_floor_ids = implode(',', (array)$building_floors_id);
			$return['building_floors_report'] =$building_floorsres;

			$building_floor_spacessql = "SELECT bfs.id, bfs.building_floor_id, bfs.cabin_name, bfs.space_type_id, bfsa.building_floor_space_id, bfsa.emp_id, bfsa.reserve_pool_id, bfsa.current_status FROM building_floor_spaces bfs LEFT JOIN building_floor_space_allocations bfsa ON bfs.company_id = bfsa.company_id AND bfs.id = bfsa.building_floor_space_id AND bfs.is_active = bfsa.is_active   WHERE bfs.company_id = '".$get['company_id']."' AND bfs.building_floor_id IN(".$building_floor_ids.") AND bfs.is_active = 0 ";
			$building_floor_spacesres = $callConfig->getAllRows($building_floor_spacessql);
			$return['building_floor_spaces_report_sql'] =$building_floor_spacessql;
			$return['building_floor_spaces_report'] =$building_floor_spacesres;
			foreach($building_floor_spacesres as $bfspace){
				$building_floor_spaces_id[$bfspace->id] = $bfspace->id;
			}
			$building_floor_spaces_ids = implode(',', (array)$building_floor_spaces_id);
	
			$cntsql ="SELECT (SELECT COUNT(id) as cnt FROM `buildings` WHERE company_id = '".$get['company_id']."' AND id IN(".$building_ids.") AND is_active = 0) as building_cnt,(SELECT COUNT(id) as cnt FROM `building_floors` WHERE company_id = '".$get['company_id']."' AND building_id IN(".$building_ids.") AND is_active = 0) as building_floor_cnt,(SELECT COUNT(id) as cnt FROM `building_floor_spaces` WHERE company_id = '".$get['company_id']."' AND building_floor_id IN(".$building_floor_ids.") AND is_active = 0) as building_floor_spaces_cnt,(SELECT COUNT(id) AS cnt FROM building_floor_space_allocations WHERE company_id ='".$get['company_id']."' AND emp_id IS NOT NULL AND company_id = '".$get['company_id']."' AND building_floor_space_id IN(".$building_floor_spaces_ids.") AND is_active = 0) as building_floor_space_allocation_cnt,(SELECT COUNT(id) FROM `asset_allocations` WHERE company_id ='".$get['company_id']."' AND is_active = 0) as equipment_allocation_cnt";
			$dash_cnt = $callConfig->getRow($cntsql);
			$return['dash_cnt_Sql'] =$cntsql;
			$return['dash_cnt'] =$dash_cnt;

			$seating_poolssql = "SELECT sp.id, sp.pool_name, sp.bg_color, (SELECT COUNT(bfsa.id) AS cnt FROM building_floor_space_allocations bfsa WHERE bfsa.company_id = sp.company_id AND bfsa.reserve_pool_id = sp.id AND bfsa.is_active = sp.is_active) as building_floor_space_allocation_cnt FROM seating_pools sp WHERE sp.company_id = '".$get['company_id']."' AND sp.transaction_table = 'building_floor_space_allocations' AND sp.is_active = 0 limit 6";
			$seating_poolsres = $callConfig->getAllRows($seating_poolssql);
			$return['seating_pools'] =$seating_poolsres;

			// $equipmentssql = "SELECT id, equipment_type_id, equipment_name, employee_seating FROM equipments WHERE company_id = '".$get['company_id']."' AND is_active = 0 ORDER BY created_date DESC limit 6";
			// $equipmentsres = $callConfig->getAllRows($equipmentssql);
			// $return['equipments_list'] =$equipmentsres;

			$equipmentssql = "SELECT id,asset_code,asset_description FROM assets WHERE company_id = '".$get['company_id']."' AND is_active = 0 ORDER BY created_date DESC limit 6";
			$equipmentsres = $callConfig->getAllRows($equipmentssql);
			$return['equipments_list'] =$equipmentsres;

			$employeessql = "SELECT id, display_name, emp_code, email_id, image FROM employees WHERE company_id = '".$get['company_id']."' AND is_active = 0 ORDER BY created_date DESC limit 6";
			$employeesres = $callConfig->getAllRows($employeessql);
			$return['employees_list'] =$employeesres;


			$bfsasql = "SELECT bfsa.id, bfsa.building_floor_space_id, bfsa.emp_id, bfsa.emp_allocation_datetime, bfsa.approval_type, bfsa.current_status FROM building_floor_space_allocations bfsa WHERE bfsa.company_id = '".$get['company_id']."' AND bfsa.approval_type = 'Y' AND bfsa.approval_user_id !='' ".$approvalcondition." AND bfsa.is_active = 0 ORDER BY emp_allocation_datetime DESC";
			$bfsares = $callConfig->getAllRows($bfsasql);
			$return['building_floor_space_allocations'] =$bfsares;
			

			$locationbdydashboard = "SELECT (SELECT count(id) as all_emp_cnt FROM `employees` WHERE company_id = '".$get['company_id']."' AND loc_buddy = '".$get['employee_id']."' AND is_active = 0) as all_emp_cnt,(SELECT count(id) as alocated_emp_cnt  FROM building_floor_space_allocations WHERE company_id = '".$get['company_id']."' AND emp_id IN(SELECT id FROM `employees` WHERE company_id = '".$get['company_id']."' AND loc_buddy = '".$get['employee_id']."' AND is_active = 0) AND is_active = 0) as alocated_emp_cnt";
			$locationbdydashboardres = $callConfig->getRow($locationbdydashboard);
			$return['locationbdydashboardsql'] =$locationbdydashboard;
			$return['locationbdydashboard'] =$locationbdydashboardres;

			//Start reporting manager Part
			$remportingemployee = "SELECT emp.id, emp.display_name, emp.emp_code, emp.email_id, emp.image, emp.reporting_manager_id FROM employees emp WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0";
			$remportingemployeeres = $callConfig->getAllRows($remportingemployee);
			$return['reporting_employee'] =$remportingemployeeres;


			$empwithallocationpositionsql = "SELECT bfa.id, bfsa.emp_id, bfsa.current_status, bfa.building_floor_id FROM building_floor_space_allocations bfsa, building_floor_spaces bfa WHERE bfsa.company_id = '".$get['company_id']."' AND bfsa.building_floor_space_id = bfa.id AND bfsa.is_active = bfa.is_active AND bfsa.emp_id IS NOT NULL AND bfsa.is_active = 0";
			$empwithallocationpositionres = $callConfig->getAllRows($empwithallocationpositionsql);
			foreach($empwithallocationpositionres as $bfsr){
				$empwithallocationposition[$bfsr->emp_id] = $bfsr;
			}
			$return['empwithallocationposition'] =$empwithallocationposition;
			//End reporting manager Part
		}elseif($get['pagetype'] == 'getemployeelistwithbuildingdetailfilter'){
			if($get['value'] !=""){
				$employeessql = "SELECT emp.id, emp.display_name, emp.emp_code, emp.email_id, emp.image FROM employees emp WHERE emp.company_id = '".$get['company_id']."' AND (emp.display_name like '%".$get['value']."%' OR emp.emp_code like '%".$get['value']."%') AND emp.is_active = 0 ORDER BY emp.display_name asc limit 4";
				$employeesres = $callConfig->getAllRows($employeessql);
				$return['employee_fillter'] =$employeesres;
				$emp_ids = array();
				foreach($employeesres as $empl){
					$emp_id[$empl->id] = $empl->id;
				}
				$emp_ids = implode(',', (array)$emp_id);
				
				$building_floor_spacessql = "SELECT bfs.id, bfs.building_floor_id, bfs.cabin_name, bfs.space_type_id, bfsa.building_floor_space_id, bfsa.emp_id, bfsa.reserve_pool_id FROM building_floor_spaces bfs, building_floor_space_allocations bfsa WHERE bfs.company_id = '".$get['company_id']."' AND bfs.company_id = bfsa.company_id AND bfs.id = bfsa.building_floor_space_id AND bfs.is_active = bfsa.is_active AND bfsa.emp_id IN(".$emp_ids.") AND bfs.is_active = 0 ";
				$building_floor_spacesres = $callConfig->getAllRows($building_floor_spacessql);
				$return['building_floor_spaces_sql'] =$building_floor_spacessql; 
				
				$building_floor_spaces = array();
				foreach($building_floor_spacesres as $empspace){
					$building_floor_spaces[$empspace->emp_id] = $empspace;
				}
				$return['building_floor_spaces'] =$building_floor_spaces;
			}else{
				$return['employee_fillter'] = null;
			}	
		}elseif($get['pagetype'] == 'REPORT'){

			if($get['admin_type']!='1'){
				$condition = "AND originator = '".$get['user_id']."'";
			}
			$buildings_id = array();
			$buildingssql = "SELECT id, building_name, building_address, building_image FROM buildings WHERE company_id = '".$get['company_id']."' ".$condition." AND is_active = 0";
			$buildingsres = $callConfig->getAllRows($buildingssql);
			foreach($buildingsres as $build){
				$buildings_id[$build->id] = $build->id;
			}
			$return['buildings_report'] =$buildingsres;
			$building_ids = implode(',', (array)$buildings_id);


			$building_floorssql = "SELECT id, building_id, floor_name FROM building_floors WHERE company_id = '".$get['company_id']."' AND building_id IN(".$building_ids.") AND is_active = 0";
			$building_floorsres = $callConfig->getAllRows($building_floorssql);
			foreach($building_floorsres as $bfloor){
				$building_floors_id[$bfloor->id] = $bfloor->id;
			}
			$building_floor_ids = implode(',', (array)$building_floors_id);
			$return['building_floors_report'] =$building_floorsres;

			$building_floor_spacessql = "SELECT bfs.id, bfs.building_floor_id, bfs.cabin_name, bfs.space_type_id, bfsa.building_floor_space_id, bfsa.emp_id, bfsa.reserve_pool_id FROM building_floor_spaces bfs LEFT JOIN building_floor_space_allocations bfsa ON bfs.company_id = bfsa.company_id AND bfs.id = bfsa.building_floor_space_id AND bfs.is_active = bfsa.is_active   WHERE bfs.company_id = '".$get['company_id']."' AND bfs.building_floor_id IN(".$building_floor_ids.") AND bfs.is_active = 0 ";
			$building_floor_spacesres = $callConfig->getAllRows($building_floor_spacessql);
			$return['building_floor_spaces_report_sql'] =$building_floor_spacessql;
			$return['building_floor_spaces_report'] =$building_floor_spacesres;
			foreach($building_floor_spacesres as $bfspace){
				$building_floor_spaces_id[$bfspace->id] = $bfspace->id;
			}


			$reservepoolsql = "SELECT id, pool_name, bg_color FROM seating_pools WHERE transaction_table = 'building_floor_space_allocations' AND company_id = '".$get['company_id']."' AND is_active = 0";
			$reservepoolres=$callConfig->getAllRows($reservepoolsql);
			foreach($reservepoolres as $resrv){
				$reservepool[$resrv->id] = $resrv;
			}
			$return['seating_pools'] = $reservepool;
			
		}elseif($get['pagetype'] == 'B'){
				if($get['admin_type']!='1'){
					$condition = " AND bu.originator = '".$get['user_id']."'";
				}
				$buildingsql = "SELECT bu.id, bu.building_name,bu.building_address,bu.building_image,(SELECT COUNT(bf.id) AS cnt FROM building_floors bf WHERE bf.company_id = bu.company_id AND bf.building_id = bu.id AND bf.is_active = bu.is_active) AS floor_cnt, (  SELECT  COUNT(bfs.id) AS cnt FROM building_floor_spaces bfs WHERE  bfs.company_id = bu.company_id AND bfs.building_floor_id IN(  SELECT bf.id FROM building_floors bf WHERE bf.company_id = bu.company_id AND bf.building_id = bu.id AND bf.is_active = bu.is_active) AND bfs.is_active = 0 ) AS building_floor_space_cnt,( SELECT   COUNT(bfsa.id) AS cnt  FROM building_floor_space_allocations bfsa  WHERE  bfsa.company_id = bu.company_id AND bfsa.emp_id IS NOT NULL AND bfsa.building_floor_space_id IN(  SELECT  bfs.id FROM building_floor_spaces bfs WHERE bfs.company_id = bu.company_id AND bfs.building_floor_id IN( SELECT bf.id FROM building_floors bf WHERE bf.company_id = bu.company_id AND bf.building_id = bu.id AND bf.is_active = bu.is_active) AND bfs.is_active = 0 AND(bfsa.reserve_pool_id IS NULL OR bfsa.emp_id IS NULL)) AND bfsa.is_active = bu.is_active) AS usedspace_cnt FROM buildings bu WHERE bu.company_id = '".$get['company_id']."' AND bu.is_active = 0".$condition;
				$buildingres = $callConfig->getAllRows($buildingsql);
				$return['buildingsql'] =$buildingsql;
				$return['buildings'] =$buildingres;
				
				// $buildingssql = "SELECT id, building_name, building_address,building_image FROM buildings WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				// $buildingsres = $callConfig->getAllRows($buildingssql);
				// $buildingresult = array();
				// foreach($buildingsres as $buldg){
				// 	$buildingresult[$buldg->id] = $buldg;
				// }
				// $return['buildings'] =$buildingresult;

				$building_floorssql = "SELECT id, floor_name, building_id, floor_image FROM building_floors WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$building_floorsres = $callConfig->getAllRows($building_floorssql);
				$building_floors = array();
				foreach($building_floorsres as $buldg){
					$building_floors[$buldg->building_id][$buldg->id] = $buldg;
				}
				$return['building_floors'] =$building_floors;

				$building_floor_spacessql = "SELECT building_floor_id, count(id) as cnt FROM `building_floor_spaces` WHERE company_id = '".$get['company_id']."' AND is_active = 0 GROUP BY building_floor_id ";
				$building_floor_spacesres = $callConfig->getAllRows($building_floor_spacessql);
				$building_floor_spaces_cnt = array();
				foreach($building_floor_spacesres as $buldg){
					$building_floor_spaces_cnt[$buldg->building_floor_id] = $buldg->cnt;
				}
				$return['building_floor_spaces_cnt'] =$building_floor_spaces_cnt;
			
			}elseif($get['pagetype'] == 'building_floors'){
				

				$building_floorssql = "SELECT id,building_id,floor_name,floor_image FROM building_floors WHERE company_id = '".$get['company_id']."' AND id = '".$get['building_floor_id']."' AND is_active = 0";
				$building_floorsres = $callConfig->getRow($building_floorssql);
				$return['building_floors'] =$building_floorsres;

				$building_floor_spacessql = "SELECT id,cabin_name FROM building_floor_spaces WHERE company_id = '".$get['company_id']."'  AND  building_floor_id = '".$get['building_floor_id']."' AND is_active = 0";
				$building_floor_spacesres = $callConfig->getallRows($building_floor_spacessql);
				$building_floor_spaces = $bfsids = array();
				foreach($building_floor_spacesres as $bfs){
					$bfsids[$bfs->id] = $bfs->id;
					$building_floor_spaces[$bfs->id] = $bfs;
				}
				$return['building_floor_spaces'] =$building_floor_spaces;
				$bfsid = implode(',',(array)$bfsids);
				
				$equipment_allocationssql = "SELECT * FROM equipment_allocations WHERE company_id = '".$get['company_id']."' AND building_floor_space_id IN (".$bfsid.") AND is_active = 0";
					$equipment_allocationsres = $callConfig->getAllRows($equipment_allocationssql);
				$return['equipment_allocations'] =$equipment_allocationsres;

				$buildingssql = "SELECT id, building_name, building_address,building_image FROM buildings WHERE company_id = '".$get['company_id']."' AND id = '".$building_floorsres->building_id."'AND is_active = 0";
				$buildingsres = $callConfig->getRow($buildingssql);
				$return['buildings'] =$buildingsres;

			}elseif($get['pagetype']=="equipmentallocationspopup"){
				if($get['asset_allocation_id'] !=""){
					$asset_allocations_coindition = " AND id = '".$get['asset_allocation_id']."'";
				}
				$building_floorssql = "SELECT id,building_id,floor_name,floor_image FROM building_floors WHERE company_id = '".$get['company_id']."' AND id = '".$get['building_floor_id']."' AND is_active = 0";
				$building_floorsres = $callConfig->getRow($building_floorssql);
				$return['building_floors'] =$building_floorsres;

				$asset_allocationssql = "SELECT id, building_floor_id, asset_id, top_size, left_size FROM asset_allocations WHERE company_id = '".$get['company_id']."' AND building_floor_id = '".$building_floorsres->id."' AND is_active = 0".$asset_allocations_coindition;
				$asset_allocationsres = $callConfig->getAllRows($asset_allocationssql);
				$return['asset_allocations'] =$asset_allocationsres;
				
				$assetssql = "SELECT id, asset_code, asset_description,bar_code,qr_code FROM assets WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$assetsres = $callConfig->getAllRows($assetssql);
				$return['assets'] =$assetsres;

				/**
				if($get['building_floor_space_id'] !=""){  //building_floor_space_id is Availabled this time used
					$building_floor_spacessql = "SELECT id, building_floor_id, cabin_name, space_type_id, x_size, y_size, width, height, cabin_image FROM building_floor_spaces WHERE company_id = '".$get['company_id']."'  AND  id = '".$get['building_floor_space_id']."' AND is_active = 0";
					$building_floor_spacesres = $callConfig->getRow($building_floor_spacessql);
					$return['building_floor_spaces'] =$building_floor_spacesres;
					$get['building_floor_id'] = $building_floor_spacesres->building_floor_id;
				}

				$building_floorssql = "SELECT id,building_id,floor_name,floor_image FROM building_floors WHERE company_id = '".$get['company_id']."' AND id = '".$get['building_floor_id']."' AND is_active = 0";
				$building_floorsres = $callConfig->getRow($building_floorssql);
				$return['building_floors'] =$building_floorsres;

				$building_floor_spacessql = "SELECT id, building_floor_id, cabin_name, space_type_id, x_size, y_size, width, height, cabin_image FROM building_floor_spaces WHERE company_id = '".$get['company_id']."'  AND  building_floor_id = '".$get['building_floor_id']."' AND is_active = 0";
				$all_building_floor_spaces = $callConfig->getAllRows($building_floor_spacessql);
				$return['all_building_floor_spaces'] =$all_building_floor_spaces;
				
				if($get['building_floor_space_id'] ==""){  //building_floor_space_id not get this time used
					$building_floor_spacessql = "SELECT id, building_floor_id, cabin_name, space_type_id, x_size, y_size, width, height, cabin_image FROM building_floor_spaces WHERE company_id = '".$get['company_id']."'  AND  id = '".$all_building_floor_spaces[0]->id."' AND is_active = 0";
					$building_floor_spacesres = $callConfig->getRow($building_floor_spacessql);
					$return['building_floor_spaces'] =$building_floor_spacesres;
			    }
				foreach($all_building_floor_spaces as $abfs){
					if($building_floor_spacesres->id != $abfs->id){
						$remaining_sapce_ids[$abfs->id] = $abfs->id;
					}
				}
				$equipment_allocationssql = "SELECT id,building_floor_space_id, equipment_category_id, equipment_type_id, equipment_id, emp_id, top_size, left_size FROM equipment_allocations WHERE company_id = '".$get['company_id']."' AND building_floor_space_id = '".$building_floor_spacesres->id."' AND is_active = 0";
				$equipment_allocationsres = $callConfig->getAllRows($equipment_allocationssql);
				$return['equipment_allocations'] =$equipment_allocationsres;

				$implodid = implode(',', (array)$remaining_sapce_ids);
				$remaining_equipment_allocationssql = "SELECT id,building_floor_space_id, equipment_category_id, equipment_type_id, equipment_id, emp_id, top_size, left_size FROM equipment_allocations WHERE company_id = '".$get['company_id']."' AND building_floor_space_id IN(".$implodid.") AND is_active = 0";
				$remaining_equipment_allocationsres = $callConfig->getAllRows($remaining_equipment_allocationssql);
				$return['remaining_equipment_allocations'] =$remaining_equipment_allocationsres;

				//remaing employee list without edit space employee
				
				$remaining_all_equipment_allocationssql = "SELECT equipment_id, emp_id FROM equipment_allocations WHERE company_id = '".$get['company_id']."' AND building_floor_space_id != '".$building_floor_spacesres->id."' AND is_active = 0";
				$remaining_all_equipment_allocationsres = $callConfig->getAllRows($remaining_all_equipment_allocationssql);
				
				$notshowemployeeid =$notshowequipmentid = array();
				foreach($remaining_all_equipment_allocationsres as $rea){
					if($rea->emp_id !=""){
						$notshowemployeeid[$rea->emp_id] = $rea->emp_id;
					}
					$notshowequipmentid[$rea->equipment_id] = $rea->equipment_id;
				}	
				$implodnotshowemployeeid = implode(',', (array)$notshowemployeeid);
				$implodnotshowequipmentid = implode(',', (array)$notshowequipmentid);
				$equipmentssql = "SELECT id, equipment_type_id, equipment_name, employee_seating FROM equipments WHERE company_id = '".$get['company_id']."' AND id NOT IN(".$implodnotshowequipmentid.") AND is_active = 0";
				$equipmentsres = $callConfig->getAllRows($equipmentssql);
				$return['equipments_list'] =$equipmentsres;

				$employeessql = "SELECT id, display_name, emp_code, email_id, image FROM employees WHERE company_id = '".$get['company_id']."' AND id NOT IN(".$implodnotshowemployeeid.") AND is_active = 0";
				$employeesres = $callConfig->getAllRows($employeessql);
				$return['employees_list'] =$employeesres;
				$return['employeessql'] =$employeessql; **/
				//End remaing employee list without edit space employee	
			}elseif($get['pagetype']=="ALLOCATEEMP"){
				$return['get'] =$get;
				$equipment_allocationssql = "SELECT id, equipment_id, emp_id FROM equipment_allocations WHERE company_id = '".$get['company_id']."' AND id = '".$get['equipment_allocation_id']."' AND is_active = 0";
				$equipment_allocationsres = $callConfig->getRow($equipment_allocationssql);
				$return['equipment_allocations'] =$equipment_allocationsres;
				$return['equipment_allocations_sql'] =$equipment_allocationssql;
				$return['emp_id'] =$equipment_allocationsres->emp_id;
				if($equipment_allocationsres->emp_id !=""){
					$empconditions = "OR id = '".$equipment_allocationsres->emp_id."'";
					$orderby = " CASE WHEN id = '".$equipment_allocationsres->emp_id."' THEN 0 ELSE 1 END, ";
				}
				$employeessql = "SELECT id, display_name, emp_code, email_id, image FROM employees WHERE company_id = '".$get['company_id']."' ".$empconditions." AND id NOT IN(SELECT emp_id FROM equipment_allocations WHERE company_id = '".$get['company_id']."' AND emp_id IS NOT NULL AND is_active = 0) AND is_active = 0  ORDER BY ".$orderby." display_name ASC"; 
				$employeesres = $callConfig->getAllRows($employeessql);
				$return['selected_employees'] =$employeesres;
			}elseif($get['pagetype']=="viewassetallocations"){
			

				$building_floorssql = "SELECT id,building_id,floor_name,floor_image FROM building_floors WHERE company_id = '".$get['company_id']."'  AND  id = '".$get['rid']."' AND is_active = 0";
				$building_floorsres = $callConfig->getRow($building_floorssql);
				$return['building_floors'] =$building_floorsres;

				$buildingssql = "SELECT id, building_name, building_address FROM buildings WHERE company_id = '".$get['company_id']."'  AND  id = '".$building_floorsres->building_id."' AND is_active = 0";
				$buildingsres = $callConfig->getRow($buildingssql);
				$return['buildings'] =$buildingsres;
				
				$asset_allocationssql = "SELECT * FROM `asset_allocations` WHERE company_id = '".$get['company_id']."' AND building_floor_id = '".$building_floorsres->id."' AND is_active = 0";
				$asset_allocationsres = $callConfig->getAllRows($asset_allocationssql);
				$return['asset_allocations'] =$asset_allocationsres;

				$assetssql = "SELECT * FROM assets WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$assetsres=$callConfig->getAllRows($assetssql);
				foreach($assetsres as $resrv){
					$assets[$resrv->id] = $resrv;
				}
				$return['assets'] = $assets;



			}elseif($get['pagetype']=="getremaingequipmentslist"){

				$equipmentssql = "SELECT id,equipment_name FROM equipments WHERE company_id = '".$get['company_id']."' AND equipment_type_id = '".$get['equipment_type_id']."' AND id NOT IN(SELECT equipment_id FROM equipment_allocations WHERE company_id = '".$get['company_id']."' AND is_active = 0) AND is_active = 0";
				$equipmentsres = $callConfig->getAllRows($equipmentssql);
				$return['equipments_list'] =$equipmentsres;
			}elseif($get['pagetype'] == 'AR'){ //Equipment Tab
				$buildingsql = "SELECT bu.id, bu.building_name, bu.building_address, bu.building_image, (SELECT COUNT(bf.id) as cnt FROM building_floors bf WHERE bf.company_id = bu.company_id AND bf.building_id = bu.id AND bf.is_active = bu.is_active) as floor_cnt, (SELECT COUNT(bfs.id) AS cnt FROM building_floor_spaces bfs WHERE bfs.company_id = bu.company_id AND bfs.building_floor_id IN(SELECT bf.id FROM building_floors bf WHERE bf.company_id = bu.company_id AND bf.building_id = bu.id AND bf.is_active = bu.is_active) AND bfs.is_active =0) as building_floor_space_cnt,(SELECT count(ea.id) as cnt FROM equipment_allocations ea WHERE ea.company_id = bu.company_id AND ea.building_floor_space_id IN(SELECT bfs.id FROM building_floor_spaces bfs WHERE bfs.company_id = bu.company_id AND bfs.building_floor_id IN(SELECT bf.id FROM building_floors bf WHERE bf.company_id = bu.company_id AND bf.building_id = bu.id AND bf.is_active = bu.is_active) AND bfs.is_active =0) AND ea.is_active = bu.is_active) AS equipments_cnt, (SELECT count(ea.id) as cnt FROM equipment_allocations ea WHERE ea.company_id = bu.company_id AND ea.building_floor_space_id IN(SELECT bfs.id FROM building_floor_spaces bfs WHERE bfs.company_id = bu.company_id AND bfs.building_floor_id IN(SELECT bf.id FROM building_floors bf WHERE bf.company_id = bu.company_id AND bf.building_id = bu.id AND bf.is_active = bu.is_active) AND bfs.is_active =0) AND ea.is_active = bu.is_active AND emp_id IS NULL) AS free_equipments FROM buildings bu WHERE bu.company_id = '".$get['company_id']."' AND bu.is_active = 0";
				$buildingres = $callConfig->getAllRows($buildingsql);
				$return['buildings'] =$buildingres;

			
			}elseif($get['pagetype'] == 'AR_SEAT'){ //Equipment Tab
				$building_floorssql = "SELECT id, floor_name, floor_image FROM building_floors WHERE company_id = '".$get['company_id']."' AND building_id = '".$get['building_id']."' AND is_active = 0";
				$building_floorsres = $callConfig->getAllRows($building_floorssql);
				foreach($building_floorsres as $bfloor){
					$buildingfloorid[$bfloor->id] = $bfloor->id;
				}
				 	
				$return['building_floors'] =$building_floorsres;
				$buildingfloor_id = implode(',', (array)$buildingfloorid);

				$equipment_allocationssql = "SELECT ea.id, ea.equipment_id, ea.emp_id,eq.equipment_code, eq.equipment_name, eq.employee_seating, bfs.building_floor_id, ea.building_floor_space_id FROM building_floor_spaces bfs, equipment_allocations ea, equipments eq WHERE bfs.company_id = '".$get['company_id']."' AND bfs.company_id = ea.company_id AND bfs.company_id = eq.company_id AND bfs.building_floor_id IN(".$buildingfloor_id.") AND bfs.id =ea.building_floor_space_id AND ea.equipment_id = eq.id AND bfs.is_active = 0 AND bfs.is_active = ea.is_active AND bfs.is_active = eq.is_active";
				$equipment_allocationsres = $callConfig->getAllRows($equipment_allocationssql);
				foreach($equipment_allocationsres as $bfloor){
					$equipment_allocations[$bfloor->building_floor_id][] = $bfloor;
				}
				$return['equipment_allocations'] =$equipment_allocations;

			}elseif($get['pagetype'] == 'CSA'){ //Cubicle Space Allocation
				$buildingsql = "SELECT bu.id, bu.building_name,bu.building_address,bu.building_image,(SELECT COUNT(bf.id) AS cnt FROM building_floors bf WHERE bf.company_id = bu.company_id AND bf.building_id = bu.id AND bf.is_active = bu.is_active) AS floor_cnt, (  SELECT  COUNT(bfs.id) AS cnt FROM building_floor_spaces bfs WHERE  bfs.company_id = bu.company_id AND bfs.building_floor_id IN(  SELECT bf.id FROM building_floors bf WHERE bf.company_id = bu.company_id AND bf.building_id = bu.id AND bf.is_active = bu.is_active) AND bfs.is_active = 0 ) AS building_floor_space_cnt,( SELECT   COUNT(bfsa.id) AS cnt  FROM building_floor_space_allocations bfsa  WHERE  bfsa.company_id = bu.company_id AND bfsa.emp_id IS NOT NULL AND bfsa.building_floor_space_id IN(  SELECT  bfs.id FROM building_floor_spaces bfs WHERE bfs.company_id = bu.company_id AND bfs.building_floor_id IN( SELECT bf.id FROM building_floors bf WHERE bf.company_id = bu.company_id AND bf.building_id = bu.id AND bf.is_active = bu.is_active) AND bfs.is_active = 0 AND(bfsa.reserve_pool_id IS NULL OR bfsa.emp_id IS NULL)) AND bfsa.is_active = bu.is_active) AS usedspace_cnt FROM buildings bu WHERE bu.company_id = '".$get['company_id']."' AND bu.is_active = 0";
				$buildingres = $callConfig->getAllRows($buildingsql);
				$return['buildingsql'] =$buildingsql;
				$return['buildings'] =$buildingres;
			}elseif($get['pagetype'] == 'CSA_ALLOCATION'){ //Equipment Tab
				
				$buildingssql = "SELECT id, building_name, building_address, building_image FROM buildings WHERE company_id = '".$get['company_id']."' AND id = '".$get['building_id']."' AND is_active = 0";
				$buildingsres = $callConfig->getRow($buildingssql);
				$return['buildings'] =$buildingsres;
				$building_floorssql = "SELECT id, floor_name, floor_image FROM building_floors WHERE company_id = '".$get['company_id']."' AND building_id = '".$get['building_id']."' AND is_active = 0";
				$building_floorsres = $callConfig->getAllRows($building_floorssql);
				foreach($building_floorsres as $bfloor){
					$buildingfloorid[$bfloor->id] = $bfloor->id;
				}
				$return['building_floors'] =$building_floorsres;
				$buildingfloor_id = implode(',', (array)$buildingfloorid);

				$building_floor_spacessql = "SELECT id, building_floor_id, cabin_name, space_type_id, x_size, y_size, width, height, cabin_image FROM `building_floor_spaces` WHERE company_id = '".$get['company_id']."' AND building_floor_id IN(".$buildingfloor_id.") AND is_active = 0";
				$building_floor_spacesres = $callConfig->getAllRows($building_floor_spacessql);
				foreach($building_floor_spacesres as $bfspae){
					$building_floor_spaces[$bfspae->building_floor_id][] = $bfspae;
					$buildingfloorspaceid[$bfspae->id] = $bfspae->id;
				}
				$return['building_floor_spaces'] =$building_floor_spaces;
				$building_floor_space_id = implode(',', (array)$buildingfloorspaceid);
				
				$building_floor_space_allocationssql = "SELECT id, building_floor_space_id, emp_id, emp_allocation_datetime, reserve_pool_id, reserve_pool_allocation_datetime, current_status FROM `building_floor_space_allocations` WHERE company_id = '".$get['company_id']."' AND building_floor_space_id IN(".$building_floor_space_id.") AND is_active = 0";
				$building_floor_space_allocationsres = $callConfig->getAllRows($building_floor_space_allocationssql);
				foreach($building_floor_space_allocationsres as $bfspae){
					$building_floor_space_allocations[$bfspae->building_floor_space_id] = $bfspae;
				}
				$return['building_floor_space_allocations'] =$building_floor_space_allocations;
				$return['building_floor_space_allocations_sql'] =$building_floor_space_allocationssql;

				$reservepoolsql = "SELECT id, pool_name, bg_color FROM seating_pools WHERE transaction_table = 'building_floor_space_allocations' AND company_id = '".$get['company_id']."' AND is_active = 0";
				$reservepoolres=$callConfig->getAllRows($reservepoolsql);
				foreach($reservepoolres as $resrv){
					$reservepool[$resrv->id] = $resrv;
				}
				$return['reservepool'] = $reservepool;

			}elseif($get['pagetype']=="CSA_ALLOCATION_EMP"){
				$return['get'] =$get;
				
				$building_floor_spacessql = "SELECT id, building_floor_id, cabin_name, space_type_id, x_size, y_size, width, height, cabin_image FROM `building_floor_spaces` WHERE company_id = '".$get['company_id']."' AND id ='".$get['building_floor_space_id']."' AND is_active = 0";
				$building_floor_spacesres = $callConfig->getRow($building_floor_spacessql);	
				$return['building_floor_spacessql'] =$building_floor_spacessql;
				$return['building_floor_spaces'] =$building_floor_spacesres;

				$building_floor_space_allocationssql = "SELECT id, building_floor_space_id, emp_id, emp_allocation_datetime, reserve_pool_id, reserve_pool_allocation_datetime, current_status  FROM building_floor_space_allocations WHERE company_id = '".$get['company_id']."' AND building_floor_space_id = '".$building_floor_spacesres->id."' AND is_active = 0";
				$building_floor_space_allocationsres = $callConfig->getRow($building_floor_space_allocationssql);
				$return['building_floor_space_allocationssql'] =$building_floor_space_allocationssql;
				$return['building_floor_space_allocations'] =$building_floor_space_allocationsres;

				$return['emp_id'] =$building_floor_space_allocationsres->emp_id;
				$return['reserve_pool_id'] =$building_floor_space_allocationsres->reserve_pool_id;


				if($building_floor_space_allocationsres->emp_id !=""){
					$empconditions = " AND (id NOT IN(SELECT emp_id FROM building_floor_space_allocations WHERE company_id = '".$get['company_id']."' AND emp_id IS NOT NULL AND is_active = 0)  OR id = '".$building_floor_space_allocationsres->emp_id."')";
					$orderby = " CASE WHEN id = '".$building_floor_space_allocationsres->emp_id."' THEN 0 ELSE 1 END, ";
				}else{
					$empconditions =  "AND id NOT IN(SELECT emp_id FROM building_floor_space_allocations WHERE company_id = '".$get['company_id']."' AND emp_id IS NOT NULL AND is_active = 0)";
				}
				$employeessql = "SELECT id, display_name, emp_code, email_id, image FROM employees WHERE company_id = '".$get['company_id']."'  ".$empconditions."  AND is_active = 0 ORDER BY ".$orderby." display_name ASC"; 
				$employeesres = $callConfig->getAllRows($employeessql);
				$return['selected_employees_sql'] =$employeessql;
				$return['selected_employees'] =$employeesres;	


				$reservepoolsql = "SELECT id, pool_name, bg_color FROM seating_pools WHERE transaction_table = 'building_floor_space_allocations' AND company_id = '".$get['company_id']."' AND is_active = 0";
				$reservepoolres=$callConfig->getAllRows($reservepoolsql);
				foreach($reservepoolres as $resrv){
					$reservepool[$resrv->id] = $resrv;
				}
				$return['reservepool'] = $reservepool;


				$building_floor_spacessql = "SELECT id, cabin_name FROM building_floor_spaces bfs WHERE bfs.company_id = '".$get['company_id']."' AND id NOT IN(SELECT bfsa.building_floor_space_id FROM building_floor_space_allocations bfsa WHERE bfsa.company_id  = bfs.company_id AND (bfsa.emp_id IS NOT NULL OR bfsa.reserve_pool_id IS NOT NULL) AND bfsa.is_active = bfs.is_active) AND building_floor_id = '".$building_floor_spacesres->building_floor_id."' AND bfs.is_active = 0"; 
				$building_floor_spacesres = $callConfig->getAllRows($building_floor_spacessql);
				$return['unallocated_building_floor_spaces'] =$building_floor_spacesres;





				$asset_allocationssql = "SELECT id,asset_id,building_floor_space_id FROM asset_allocations WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND building_floor_space_id ='".$get['building_floor_space_id']."'";
				$asset_allocationsres = $callConfig->getAllRows($asset_allocationssql);
				$asset_allocations = $alreadyusedassetid = array();
				foreach($asset_allocationsres as $ass){
					$alreadyusedassetid[$ass->asset_id] = $ass->id;
					$asset_allocations[$ass->asset_id] = $ass;
				}
				$return['asset_allocations'] =$asset_allocations;
				$alreadyusedassetids = implode(',', (array)$alreadyusedassetid);

				$assetssql = "SELECT ass.id,ass.asset_description,ass.asset_code FROM assets ass WHERE ass.company_id = '".$get['company_id']."' AND ass.id NOT IN(SELECT aa.asset_id FROM asset_allocations aa WHERE aa.company_id = ass.company_id AND aa.is_active = ass.is_active AND building_floor_space_id !='".$get['building_floor_space_id']."') AND ass.is_active = 0"; 
				$assetsres = $callConfig->getAllRows($assetssql);
				$return['assetssql'] =$assetssql;
				$return['assets'] =$assetsres;

			}elseif($get['pagetype'] == 'IEQ'){ //Equipment Tab
				$condition = "";
				if($get['equipment_category_id'] !=""){
					$condition .= " AND eq_cat.id = ".$get['equipment_category_id'];
				}
				if($get['equipment_type_id'] !=""){
					$condition .= " AND eq_type.id = ".$get['equipment_type_id'];
				}
				if($get['equipment_id'] !=""){
					$condition .= " AND eq.id = ".$get['equipment_id'];
				}
				if($get['seating_equipment'] !=""){
					$condition .= " AND eq_type.seating_equipment = '".$get['seating_equipment']."'";
				}
				$equipmentsql ="SELECT eq.id, eq.equipment_type_id, eq.equipment_name, eq.employee_seating, eq.qr_code, eq.bar_code, eq.equipment_image, eq_cat.category_name, eq_type.equipment_types_name,eq_type.seating_equipment FROM equipments eq, equipment_categories eq_cat, equipment_types eq_type WHERE eq.company_id = '".$get['company_id']."' AND eq.company_id = eq_cat.company_id AND eq.company_id = eq_type.company_id AND eq.equipment_type_id = eq_type.id AND eq_type.equipment_category_id = eq_cat.id AND eq.is_active = 0 AND eq.is_active = eq_cat.is_active AND eq.is_active = eq_type.is_active".$condition;
				$equipments = $callConfig->getAllRows($equipmentsql);
				$return['equipmentsql'] =$equipmentsql;
				$return['equipments_list'] =$equipments;

				$equipment_allocationssql = "SELECT eq_al.id, eq_al.building_floor_space_id, eq_al.equipment_category_id, eq_al.equipment_type_id, eq_al.equipment_id, eq_al.emp_id, eq_al.top_size, eq_al.left_size, bfs.cabin_name, bfs.building_floor_id, bf.floor_name, bf.building_id FROM equipment_allocations eq_al, building_floor_spaces bfs, building_floors bf WHERE eq_al.company_id = '".$get['company_id']."' AND eq_al.company_id = bfs.company_id AND eq_al.company_id = bf.company_id AND eq_al.building_floor_space_id = bfs.id AND bfs.building_floor_id = bf.id AND eq_al.is_active = 0 ";
				$equipment_allocationsres = $callConfig->getAllRows($equipment_allocationssql);
				$return['equipment_allocations'] =$equipment_allocationsres;
			}elseif($get['pagetype'] == 'ASSETS'){ //Equipment Tab
				$condition = "";
				if($get['equipment_category_id'] !=""){
					$assets_condition .= " AND category_id = ".$get['equipment_category_id'];
				}
				if($get['equipment_type_id'] !=""){
					$assets_condition .= " AND id = ".$get['equipment_type_id'];
				}
				// if($get['equipment_id'] !=""){
				// 	$condition .= " AND eq.id = ".$get['equipment_id'];
				// }
				// if($get['seating_equipment'] !=""){
				// 	$condition .= " AND eq_type.seating_equipment = '".$get['seating_equipment']."'";
				// }
				// $equipmentsql ="SELECT eq.id, eq.equipment_type_id, eq.equipment_name, eq.employee_seating, eq.qr_code, eq.bar_code, eq.equipment_image, eq_cat.category_name, eq_type.equipment_types_name,eq_type.seating_equipment FROM equipments eq, equipment_categories eq_cat, equipment_types eq_type WHERE eq.company_id = '".$get['company_id']."' AND eq.company_id = eq_cat.company_id AND eq.company_id = eq_type.company_id AND eq.equipment_type_id = eq_type.id AND eq_type.equipment_category_id = eq_cat.id AND eq.is_active = 0 AND eq.is_active = eq_cat.is_active AND eq.is_active = eq_type.is_active".$condition;
				// $equipments = $callConfig->getAllRows($equipmentsql);
				// $return['equipmentsql'] =$equipmentsql;
				// $return['equipments_list'] =$equipments;

				// $equipment_allocationssql = "SELECT eq_al.id, eq_al.building_floor_space_id, eq_al.equipment_category_id, eq_al.equipment_type_id, eq_al.equipment_id, eq_al.emp_id, eq_al.top_size, eq_al.left_size, bfs.cabin_name, bfs.building_floor_id, bf.floor_name, bf.building_id FROM equipment_allocations eq_al, building_floor_spaces bfs, building_floors bf WHERE eq_al.company_id = '".$get['company_id']."' AND eq_al.company_id = bfs.company_id AND eq_al.company_id = bf.company_id AND eq_al.building_floor_space_id = bfs.id AND bfs.building_floor_id = bf.id AND eq_al.is_active = 0 ";
				// $equipment_allocationsres = $callConfig->getAllRows($equipment_allocationssql);
				// $return['equipment_allocations'] =$equipment_allocationsres;
					
				$assetssql ="SELECT * FROM assets WHERE company_id = '".$get['company_id']."' ".$assets_condition." AND is_active = 0";
				$assetsres = $callConfig->getAllRows($assetssql);
				$return['assets'] =$assetsres;

				$asset_categorie = "SELECT id,category_description FROM  asset_categories WHERE  company_id = '".$get['company_id']."' AND is_active =0";
				$asset_categorieres=$callConfig->getAllRows($asset_categorie);
				$asset_categories = array();
				foreach($asset_categorieres as $aas){
					$asset_categories[$aas->id] = $aas;
				}
				$return['asset_categories'] = $asset_categories; 

				$asset_allocationssql = "SELECT * FROM asset_allocations WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$asset_allocationsres = $callConfig->getAllRows($asset_allocationssql);
				$return['asset_allocations'] =$asset_allocationsres;



			}elseif($get['pagetype'] == 'IEM'){ // Employees Tab
				$condition = "";
				//$return['get'] = $get;
				if($get['building_id'] !=""){
					$condition .= " AND bf.building_id = ".$get['building_id'];
				}
				if($get['building_floor_id'] !=""){
					$condition .= " AND bf.id = ".$get['building_floor_id'];
				}
				if($get['equipment_id'] !=""){
					$condition .= " AND eq_al.equipment_id = ".$get['equipment_id'];
				}
				if($get['emp_id'] !=""){
					$condition .= " AND eq_al.emp_id = ".$get['emp_id'];
					$empcondition .= " AND id = ".$get['emp_id'];
				}
				// $equipmentsql ="SELECT eq.id, eq.equipment_type_id, eq.equipment_name, eq.employee_seating, eq.qr_code, eq.bar_code, eq_cat.category_name, eq_type.equipment_types_name FROM equipments eq, equipment_categories eq_cat, equipment_types eq_type WHERE eq.company_id = '".$get['company_id']."' AND eq.company_id = eq_cat.company_id AND eq.company_id = eq_type.company_id AND eq.equipment_type_id = eq_type.id AND eq_type.equipment_category_id = eq_cat.id AND  eq.is_active = 0 AND eq.is_active = eq_cat.is_active AND eq.is_active = eq_type.is_active";
				// $equipments = $callConfig->getAllRows($equipmentsql);
				// $return['equipments_list'] =$equipments;

				$employeessql = "SELECT id, display_name, emp_code, email_id, image FROM employees WHERE company_id = '".$get['company_id']."' ".$empcondition." AND id NOT IN(SELECT emp_id FROM equipment_allocations WHERE company_id = '".$get['company_id']."'  AND emp_id IS NOT NULL AND is_active = 0) AND is_active = 0";
				$employeesres = $callConfig->getAllRows($employeessql);
				$return['employeessql'] =$employeessql;
				$return['employees_list'] =$employeesres;


				$equipment_allocationssql = "SELECT eq_al.id, eq_al.building_floor_space_id, eq_al.equipment_category_id, eq_al.equipment_type_id, eq_al.equipment_id, eq_al.emp_id, eq_al.top_size, eq_al.left_size, bfs.cabin_name, bfs.building_floor_id, bf.floor_name, bf.building_id, emp.id as empp_id, emp.display_name, emp.emp_code, emp.email_id, emp.image FROM equipment_allocations eq_al, building_floor_spaces bfs, building_floors bf, employees emp WHERE eq_al.company_id = '".$get['company_id']."' AND eq_al.company_id = bfs.company_id AND eq_al.company_id = bf.company_id AND eq_al.company_id = emp.company_id AND eq_al.building_floor_space_id = bfs.id AND bfs.building_floor_id = bf.id AND eq_al.emp_id = emp.id  AND eq_al.is_active = emp.is_active  AND eq_al.is_active = 0 ".$condition;
				$equipment_allocationsres = $callConfig->getAllRows($equipment_allocationssql);
				$return['equipment_allocations'] =$equipment_allocationsres;
 				
 				$buildingssql = "SELECT id, building_name, building_address,building_image FROM buildings WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$buildingsres = $callConfig->getAllRows($buildingssql);
				$return['buildings'] =$buildingsres;

				$building_floorssql = "SELECT id, floor_name, building_id, floor_image FROM building_floors WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$building_floorsres = $callConfig->getAllRows($building_floorssql);
				$return['building_floors'] =$building_floorsres;

			}elseif($get['pagetype'] == 'BSEM'){ // Employees Tab
				$condition = "";
				//$return['get'] = $get;
				if($get['building_id'] !=""){
					$condition .= " AND bf.building_id = ".$get['building_id'];
				}
				if($get['building_floor_id'] !=""){
					$condition .= " AND bf.id = ".$get['building_floor_id'];
				}
				if($get['location_id'] !=""){
					$emplocatincondition .= " AND location_id = ".$get['location_id'];

					$locsql = "SELECT id, location_name FROM locations WHERE company_id = '".$get['company_id']."' AND id = '".$get['location_id']."' AND is_active = 0";
					$locres = $callConfig->getRow($locsql);
					$return['select_locations'] =$locres;

				}

				// if($get['equipment_id'] !=""){
				// 	$condition .= " AND eq_al.equipment_id = ".$get['equipment_id'];
				// }
				// if($get['emp_id'] !=""){
				// 	$condition .= " AND eq_al.emp_id = ".$get['emp_id'];
				// 	$empcondition .= " AND id = ".$get['emp_id'];
				// }
				$employeessql = "SELECT id, display_name, emp_code, email_id, image, reporting_manager_id, user_id FROM employees WHERE company_id = '".$get['company_id']."' ".$emplocatincondition." AND is_active = 0";
				$return['select_employees_sql'] =$employeessql;
				$employeesres = $callConfig->getAllRows($employeessql);
				$return['select_employees'] =$employeesres;


				$building_floor_space_allocationssql = "SELECT bfs.id, bfs.building_floor_id, bfs.cabin_name, bfs.space_type_id, bfsa.building_floor_space_id, bfsa.emp_id, bfsa.reserve_pool_id, bf.floor_name, b.building_name FROM building_floor_spaces bfs, building_floor_space_allocations bfsa, building_floors bf, buildings b  WHERE bfs.company_id = '".$get['company_id']."' AND bfs.company_id = bfsa.company_id AND bfs.id = bfsa.building_floor_space_id AND bfs.is_active = bfsa.is_active 
				AND bf.company_id = bfsa.company_id AND bf.id = bfs.building_floor_id AND bf.is_active = bfsa.is_active AND b.company_id = bfsa.company_id AND b.id = bf.building_id AND b.is_active = bfsa.is_active AND bfs.is_active = 0 AND bfsa.emp_id IS NOT NULL";
				$building_floor_space_allocationsres = $callConfig->getAllRows($building_floor_space_allocationssql);
				$return['building_floor_space_allocations'] =$building_floor_space_allocationsres;


				// $equipment_allocationssql = "SELECT eq_al.id, eq_al.building_floor_space_id, eq_al.equipment_category_id, eq_al.equipment_type_id, eq_al.equipment_id, eq_al.emp_id, eq_al.top_size, eq_al.left_size, bfs.cabin_name, bfs.building_floor_id, bf.floor_name, bf.building_id, emp.id as empp_id, emp.display_name, emp.emp_code, emp.email_id, emp.image FROM equipment_allocations eq_al, building_floor_spaces bfs, building_floors bf, employees emp WHERE eq_al.company_id = '".$get['company_id']."' AND eq_al.company_id = bfs.company_id AND eq_al.company_id = bf.company_id AND eq_al.company_id = emp.company_id AND eq_al.building_floor_space_id = bfs.id AND bfs.building_floor_id = bf.id AND eq_al.emp_id = emp.id  AND eq_al.is_active = emp.is_active  AND eq_al.is_active = 0 ".$condition;
				// $equipment_allocationsres = $callConfig->getAllRows($equipment_allocationssql);
				// $return['equipment_allocations'] =$equipment_allocationsres;
 				
 				$buildingssql = "SELECT id, building_name, building_address,building_image FROM buildings WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$buildingsres = $callConfig->getAllRows($buildingssql);
				$return['buildings'] =$buildingsres;

				$building_floorssql = "SELECT id, floor_name, building_id, floor_image FROM building_floors WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$building_floorsres = $callConfig->getAllRows($building_floorssql);
				$return['building_floors'] =$building_floorsres;

			}elseif($get['pagetype']=="setlocationequipmentspopup"){
				
				$buildingssql = "SELECT id, building_name, building_address,building_image FROM buildings WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$buildingsres = $callConfig->getAllRows($buildingssql);
				$return['buildings'] =$buildingsres;

				$assetssql ="SELECT * FROM assets WHERE company_id = '".$get['company_id']."' AND id = '".$get['eq_id']."' AND is_active = 0";
				$assetsres = $callConfig->getRow($assetssql);
				$return['assets'] =$assetsres;

			}elseif($get['pagetype']=="setlocationemployeespopup"){
				
				$employeessql = "SELECT id, display_name, emp_code, email_id, image FROM employees WHERE company_id = '".$get['company_id']."' AND id = '".$get['emp_id']."' AND  is_active = 0";
				$employeesres = $callConfig->getRow($employeessql);
				$return['employees_list'] =$employeesres;

				$buildingssql = "SELECT id, building_name, building_address,building_image FROM buildings WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$buildingsres = $callConfig->getAllRows($buildingssql);
				$return['buildings'] =$buildingsres;

			}elseif($get['pagetype']=="getequipmentlogs"){
				
				$equipment_logssql = "SELECT * FROM equipment_logs WHERE company_id = '".$get['company_id']."' AND equipment_id = '".$get['eq_id']."' AND  is_active = 0";
				$equipment_logsres = $callConfig->getAllRows($equipment_logssql);
				$return['equipment_logs'] =$equipment_logsres;

				$building_floor_spacessql = "SELECT id,cabin_name FROM building_floor_spaces WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$building_floor_spacesres = $callConfig->getAllRows($building_floor_spacessql);
				$return['building_floor_spaces'] =$building_floor_spacesres;

			}elseif($get['pagetype']=="getemployeedetails"){
				
				$employeessql = "SELECT emp.id, emp.display_name FROM employees emp, application_users au WHERE emp.company_id = '".$get['company_id']."'  AND emp.company_id = au.company_id AND emp.user_id = au.id AND JSON_SEARCH(au.role_id, 'one', '".SPACE_LOCATION_ADMIN_ROLE."') IS NOT NULL AND emp.is_active = au.is_active AND emp.is_active = 0";
				$employeesres1 = $callConfig->getAllRows($employeessql);
				$return['location_buddy_employees_list_sql'] =$employeessql;
				$return['location_buddy_employees_list'] =$employeesres1;


				$employeessql = "SELECT * FROM employees WHERE company_id = '".$get['company_id']."' AND id = '".$get['emp_id']."' AND is_active = 0";
				$employeesres = $callConfig->getRow($employeessql);
				$return['employees_list'] =$employeesres;

				$departmentssql = "SELECT id, department_name FROM departments WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$departmentsres = $callConfig->getAllRows($departmentssql);
				$return['departments'] =$departmentsres;

				$locationssql = "SELECT id, location_name FROM locations WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$locationsres = $callConfig->getAllRows($locationssql);
				$return['locations'] =$locationsres;


			}else{ 	
				if($get['rid'] !=""){
					$building_floor_spacessql = "SELECT * FROM building_floor_spaces WHERE company_id = '".$get['company_id']."'  AND  id = '".$get['rid']."' AND is_active = 0";
					$building_floor_spacesres = $callConfig->getRow($building_floor_spacessql);
					$return['building_floor_spaces'] =$building_floor_spacesres;

					// $equipment_allocationssql = "SELECT id,building_floor_space_id, equipment_category_id, equipment_type_id, equipment_id, emp_id, top_size, left_size FROM equipment_allocations WHERE company_id = '".$get['company_id']."' AND building_floor_space_id = '".$get['rid']."' AND is_active = 0";
					// $equipment_allocationsres = $callConfig->getAllRows($equipment_allocationssql);
					// $return['equipment_allocations'] =$equipment_allocationsres;

					$building_floorssql = "SELECT id,building_id,floor_name,floor_image FROM building_floors WHERE company_id = '".$get['company_id']."' AND id = '".$building_floor_spacesres->building_floor_id."' AND is_active = 0";
					$building_floorsres = $callConfig->getRow($building_floorssql);
					$return['building_floors'] =$building_floorsres;

					//Get All Spaces In floor with Equipments
					$same_floor_spacessql = "SELECT id FROM building_floor_spaces WHERE company_id = '".$get['company_id']."'  AND  building_floor_id = '".$building_floor_spacesres->building_floor_id."' AND  id !='".$get['rid']."' AND is_active = 0";
					$same_floor_spacesres = $callConfig->getAllRows($same_floor_spacessql);
					$remaining_sapce_ids = array();
					foreach($same_floor_spacesres as $sfs){
						$remaining_sapce_ids[$sfs->id] = $sfs->id;
					}
					$implodid = implode(',', $remaining_sapce_ids);
					$equipment_allocationssql = "SELECT id,building_floor_space_id, equipment_category_id, equipment_type_id, equipment_id, emp_id, top_size, left_size FROM equipment_allocations WHERE company_id = '".$get['company_id']."' AND building_floor_space_id IN(".$implodid.") AND is_active = 0";
					$equipment_allocationsres = $callConfig->getAllRows($equipment_allocationssql);
					$return['remaining_equipment_allocations'] =$equipment_allocationsres;
					//End Get All Spaces In floor with Equipments

				}
			}
				$equipment_categoriessql = "SELECT id, category_name FROM equipment_categories WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$equipment_categoriesres = $callConfig->getAllRows($equipment_categoriessql);
				$return['equipment_categories'] =$equipment_categoriesres;
				
				$equipment_typessql = "SELECT id, equipment_category_id, equipment_types_name, seating_equipment FROM equipment_types WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$equipment_typesres = $callConfig->getAllRows($equipment_typessql);
				$return['equipment_types'] =$equipment_typesres;

				$equipmentssql = "SELECT id, equipment_type_id, equipment_name, employee_seating FROM equipments WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$equipmentsres = $callConfig->getAllRows($equipmentssql);
				$return['equipments'] =$equipmentsres;

				$employeessql = "SELECT id, display_name, emp_code, email_id, image, reporting_manager_id, user_id FROM employees WHERE company_id = '".$get['company_id']."' AND is_active = 0";
				$employeesres = $callConfig->getAllRows($employeessql);
				$return['employees'] =$employeesres;

		//}
		return $return;
	}
	function insertEquipmentAllocations($get,$post){
		global $callConfig;
		$return['post'] =$post;
		if($post['building_floor_space_id'] !=""){
			$return['error'] =0;
			if($post['equipment_allocation_id'] !=""){ //unallocation asssets

				
				
				$detailassetsql = "SELECT aa.id, aa.building_floor_space_id,(SELECT build.originator FROM building_floors bf, buildings build WHERE bf.company_id = aa.company_id AND build.company_id = aa.company_id AND bf.id = aa.building_floor_id AND bf.building_id = build.id AND bf.is_active = aa.is_active AND build.is_active = aa.is_active) as building_originator,(SELECT bfs.cabin_name FROM building_floor_spaces bfs WHERE bfs.company_id = aa.company_id AND bfs.id = aa.building_floor_space_id AND bfs.is_active = aa.is_active) as cabin_name, aa.asset_id, (SELECT ass.asset_description FROM assets ass WHERE ass.company_id = aa.company_id AND ass.id = aa.asset_id AND ass.is_active = aa.is_active) as asset_description FROM asset_allocations aa WHERE aa.company_id = '".$get['company_id']."' AND aa.id = '".$post['equipment_allocation_id']."' AND aa.is_active = 0";
				$detailasset = $callConfig->getRow($detailassetsql);

				$return['result'] = $callConfig->updateRecord('asset_allocations',array('is_active'=>NULL),'id',$post['equipment_allocation_id']);

				$ausql = "SELECT id as user_id, user_email as email_id, display_name FROM application_users WHERE company_id = ".$get['company_id']." AND id = '".$detailasset->building_originator."' AND is_active = 0"; 
				$ausres = $callConfig->getRow($ausql);
				$return['ausres'] =$ausres;
				$return['detailassetsql'] = $detailassetsql;
				//notification
					$notarry['notification_users'] = array($ausres->id);
					$notarry['company_id']= $get['company_id'];
					$notarry['option'] = 'COM_VIEW_OFFICE_MANAGEMENT&TAB=7';
					$notarry['type'] = "INFORMATION";
					$notarry['notification_message'] = $detailasset->asset_description." - (".$detailasset->cabin_name.") Successful asset Un-Allocated";
				//notification
				//email data	
					$emailarray['building_admin_email_id'] = $ausres->email_id;
					$emailarray['building_admin_display_name'] = $ausres->display_name;
					$emailarray['transaction_date_and_time'] = date("Y-m-d H:i:s");
					$emailarray['to_company_id'] = $get['company_id'];
					$emailarray['session_company_id'] = $get['company_id'];
					$emailarray['default_module_id'] = 34;
					$emailarray['created_by'] = $get['user_id'];
					$emailarray['space_name'] = $detailasset->cabin_name;
					$emailarray['asset_name'] = $detailasset->asset_description;
				//email data	
					$insertApplication['emails'] =$this->sendNotificationAndEmails($notarry,$emailarray,68);


			}else{ //allocation asssets
				$eqalsql = "SELECT id FROM asset_allocations WHERE company_id = '".$post['company_id']."' AND asset_id = '".$post['asset_id']."' AND is_active = 0";
				$eqalres = $callConfig->getRow($eqalsql);
				if($eqalres->id !=""){ //Allreadu allocation asssets
					$return['error'] =9113;
				}else{ //Not allocation asssets
					$bfssql = "SELECT bfs.x_size, bfs.y_size, bfs.cabin_name, (SELECT build.originator FROM building_floors bf, buildings build WHERE bf.company_id = bfs.company_id AND build.company_id = bfs.company_id AND bf.id = bfs.building_floor_id AND bf.building_id = build.id AND bf.is_active = bfs.is_active AND build.is_active = bfs.is_active limit 1) as building_originator FROM building_floor_spaces bfs WHERE bfs.company_id = '".$post['company_id']."' AND bfs.id = '".$post['building_floor_space_id']."' AND bfs.is_active = 0";
					$bfsres = $callConfig->getRow($bfssql);
					
					$insertary=array('company_id'=>$post['company_id'],'building_floor_id'=>$post['building_floor_id'],'top_size'=>$bfsres->x_size,'left_size'=>$bfsres->y_size,'asset_id'=>$post['asset_id'],'building_floor_space_id'=>$post['building_floor_space_id'],'is_active'=>0);
					$tapres = $callConfig->insertRecord('asset_allocations',$insertary);
					$return['result'] =$tapres;
					$return['bfssql'][] = $bfssql;


					$sqlasset = "SELECT asset_description FROM assets WHERE company_id ='".$post['company_id']."'  AND id = '".$post['asset_id']."' AND is_active = 0";
					$sqlasres = $callConfig->getRow($sqlasset);
					
					$ausql = "SELECT id as user_id, user_email as email_id, display_name FROM application_users WHERE company_id = ".$get['company_id']." AND id = '".$bfsres->building_originator."' AND is_active = 0"; 
					$ausres = $callConfig->getRow($ausql);
					$return['ausql'][] = $ausql;
				//notification
					$notarry['notification_users'] = array($ausres->id);
					$notarry['company_id']= $get['company_id'];
					$notarry['option'] = 'COM_VIEW_OFFICE_MANAGEMENT&TAB=7';
					$notarry['type'] = "INFORMATION";
					$notarry['notification_message'] = $sqlasres->asset_description." - (".$bfsres->cabin_name.") Successful asset Allocated";
				//notification
				//email data	
					$emailarray['building_admin_email_id'] = $ausres->email_id;
					$emailarray['building_admin_display_name'] = $ausres->display_name;
					$emailarray['transaction_date_and_time'] = date("Y-m-d H:i:s");
					$emailarray['to_company_id'] = $get['company_id'];
					$emailarray['session_company_id'] = $get['company_id'];
					$emailarray['default_module_id'] = 34;
					$emailarray['created_by'] = $get['user_id'];
					$emailarray['space_name'] = $bfsres->cabin_name;
					$emailarray['asset_name'] = $sqlasres->asset_description;
				//email data	
					$return['emailarray'][] = $emailarray;
					$return['emails'][] =$this->sendNotificationAndEmails($notarry,$emailarray,67);
					
				}
			}
		}else{

			$return['error'] =0;		
			$eqalsql = "SELECT id FROM asset_allocations WHERE company_id = '".$post['company_id']."' AND asset_id = '".$post['asset_id']."' AND id != '".$post['equipment_allocation_id']."' AND is_active = 0";
			$eqalres = $callConfig->getRow($eqalsql);
			if($eqalres->id !=""){
				$return['error'] =9113;	
			}else{

				$insertary=array('building_floor_id'=>$post['building_floor_id'],'top_size'=>$post['top'],'left_size'=>$post['left'],'asset_id'=>$post['asset_id']);
				if($post['equipment_allocation_id'] !=""){
					$insertary['updated_date']=date('Y-m-d h:i:s');
					$insertary['updated_by']=$post['user_id'];
					$return['result'] = $callConfig->updateRecord('asset_allocations',$insertary,'id',$post['equipment_allocation_id']);
				
					$insertlog=array('company_id'=>$post['company_id'],'equipment_id'=>$post['asset_id'],'log_type'=>'EAU','log_datetime'=>date("Y-m-d H:i:s"),'user_id'=>$post['user_id'],'building_floor_space_id'=>$post['building_floor_id'],'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"), 'current_status'=>'0','is_active'=>'0');
					$callConfig->insertRecord('equipment_logs',$insertlog);	
				}else{
					$insertary['created_by']=$post['user_id'];
					$insertary['company_id']=$post['company_id'];
					$insertary['is_active']=0;
					$insertary['created_date']=date("Y-m-d H:i:s");
					$tapres = $callConfig->insertRecord('asset_allocations',$insertary);
					$building_id = $tapres['LASTID'];
					$return['result'] =$tapres;	
					
					$insertlog=array('company_id'=>$post['company_id'],'equipment_id'=>$post['asset_id'],'log_type'=>'EA','log_datetime'=>date("Y-m-d H:i:s"),'user_id'=>$post['user_id'],'building_floor_space_id'=>$post['building_floor_id'],'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"), 'current_status'=>'0','is_active'=>'0');
					$callConfig->insertRecord('equipment_logs',$insertlog);
				
				}
			}

			$return['insertary'] =$insertary;
		}
		return $return;
	}
	function getequipmentIncidents($get){
		global $callConfig;
		$equipmentssql = "SELECT id, equipment_type_id, equipment_name, employee_seating FROM equipments WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$equipmentsres = $callConfig->getAllRows($equipmentssql);
		$return['equipments'] =$equipmentsres;
		return $return;
	}
	function getEquipments($get){
		global $callConfig;
		$return['get'] =$get;
		$equipment_typessql = "SELECT id, equipment_types_name, seating_equipment FROM equipment_types WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$equipment_typesres = $callConfig->getAllRows($equipment_typessql);
		$return['equipment_types'] =$equipment_typesres;

		$equipmentssql = "SELECT * FROM equipments WHERE company_id = '".$get['company_id']."' AND id = '".$get['eq_id']."' AND is_active = 0";
		$equipmentsres = $callConfig->getRow($equipmentssql);
		$return['equipments'] =$equipmentsres;

		$display_attributessql = "SELECT id, file_upload FROM display_attributes WHERE company_id = '".$get['company_id']."' AND application_table_name = 'equipments' AND is_active = 0";
		$display_attributesres = $callConfig->getRow($display_attributessql);
		$return['display_attributes'] =$display_attributesres;
		return $return;
	}
	function intertEquipments($get,$post){
		global $callConfig;
		$qruploadDir =  SITEROOT.'/uploads/equipments/qr_code/';
		$employee_seating = $post['employee_seating'] =='Y'?"Y":"N";
		$insertary=array('equipment_type_id'=>$post['equipment_type_id'],'equipment_name'=>$post['equipment_name'],'description'=>$post['description'],'employee_seating'=>$employee_seating,'equipment_image'=>$post['equipment_image']);
		if($post['eq_id'] !=""){
			$insertary['updated_date']=date('Y-m-d h:i:s');
			$insertary['updated_by']=$post['user_id'];

			$equipmentssql = "SELECT id,equipment_code,qr_code FROM equipments WHERE company_id = '".$post['company_id']."' AND id = '".$post['eq_id']."' AND is_active = 0";
			$equipmentsres = $callConfig->getRow($equipmentssql);
			if (file_exists($qruploadDir.$equipmentsres->qr_code)) {
				unlink($qruploadDir.$equipmentsres->qr_code);
			}

			$text = $equipmentsres->equipment_code."-".$post['equipment_name'];
			$qrfile_name = $this->creatQRCode($text,$qruploadDir); // Creater QR Code
			$insertary['qr_code'] = $qrfile_name;

			$return = $callConfig->updateRecord('equipments',$insertary,'id',$post['eq_id']);
		

			$insertlog=array('company_id'=>$post['company_id'],'equipment_id'=>$post['eq_id'],'log_type'=>'UP','log_datetime'=>date("Y-m-d H:i:s"),'user_id'=>$post['user_id'],'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"), 'current_status'=>'0','is_active'=>'0');
			$callConfig->insertRecord('equipment_logs',$insertlog);
		}else{
			$insertary['originator']=$post['user_id'];
			$insertary['created_by']=$post['user_id'];
			$insertary['company_id']=$post['company_id'];
			$insertary['is_active']=0;
			$insertary['created_date']=date("Y-m-d H:i:s");
			
			$equipment_code = $this->toGetApplicationSerialNumber('equipments');
			$insertary['equipment_code'] = $equipment_code;

			$text = $equipment_code."-".$post['equipment_name'];
			$qrfile_name = $this->creatQRCode($text,$qruploadDir); // Creater QR Code
			$insertary['qr_code'] = $qrfile_name;


			$tapres = $callConfig->insertRecord('equipments',$insertary);
			$return =$tapres;

			$insertlog=array('company_id'=>$post['company_id'],'equipment_id'=>$tapres['LASTID'],'log_type'=>'IN','log_datetime'=>date("Y-m-d H:i:s"),'user_id'=>$post['user_id'],'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"), 'current_status'=>'0','is_active'=>'0');
			$callConfig->insertRecord('equipment_logs',$insertlog);
		}
		return $return;
	}
	function allocateEmployeeSeat($get){
		global $callConfig;
		$return['get'] =$get;
		$updatedary['emp_id']=$get['emp_id'];
		$updatedary['updated_date']=date('Y-m-d h:i:s');
		$updatedary['updated_by']=$get['user_id'];
		$wh = " id = '".$get['equipment_allocation_id']."'";
		$return['equipment_allocations'] = $callConfig->updateRecord('equipment_allocations',$updatedary,'','',$wh);

		$sql = "SELECT bf.building_id FROM equipment_allocations ea, building_floor_spaces bfs, building_floors bf WHERE ea.company_id = '".$get['company_id']."' AND ea.company_id = bfs.company_id AND ea.building_floor_space_id = bfs.id AND ea.is_active = bfs.is_active AND bf.company_id = bfs.company_id AND bf.id = bfs.building_floor_id AND bf.is_active = 0 AND ea.id  = '".$get['equipment_allocation_id']."'";
		$building_res=$callConfig->getRow($sql);
		$return['building_id']  = $building_res->building_id;
		return $return;
	}
	function insertBuildingFloorSpaceAllocations($get,$post){
		global $callConfig;
		$return['post'] =$post;
		$informationlist = array();
		$location_user_id = ""; 
		$ausql = "SELECT id as user_id, user_email as email_id, display_name FROM application_users WHERE company_id = ".$get['company_id']." AND is_active = 0"; 
		$ausres = $callConfig->getAllRows($ausql);
		foreach($ausres as $ausress){
			$application_users[$ausress->user_id] =  $ausress;
		}

		$sql = "SELECT bf.building_id, bfs.space_type_id, bfs.id, bfs.cabin_name, build.originator, build.building_name FROM building_floor_spaces bfs, building_floors bf, buildings build WHERE bfs.company_id = '".$post['company_id']."' AND bfs.company_id = bf.company_id AND build.company_id = bf.company_id AND bfs.building_floor_id = bf.id AND build.id = bf.building_id AND bfs.is_active = bf.is_active AND build.is_active = bf.is_active AND bfs.is_active = 0 AND bfs.id  = '".$post['building_floor_space_id']."'";
		$building_res=$callConfig->getRow($sql);
		$return['building_id_sql'] = $sql;
		$return['building_id']  = $building_res->building_id;
		$informationlist['buildingadmin'][] =  $application_users[$building_res->originator];
		$informationlist['assigner'][] =  $application_users[$post['user_id']];

		$emp_asssign_status = $emp_id_available = "";
		$postdataary = array('building_floor_space_id'=>$post['building_floor_space_id']);
		if($post['emp_id'] !=""){
			$postdataary['emp_id'] = $post['emp_id'];
			$postdataary['emp_allocation_datetime'] = date('Y-m-d H:i:s');
			if($building_res->space_type_id == '7'){
				$postdataary['current_status'] = ST_INPROGRESS;
				$postdataary['approval_type'] = 'Y';
			}else{
				$postdataary['current_status'] = ST_APPROVED;
			}
			$emp_id_available = $post['emp_id']; //emp id available
			$emp_asssign_status = "Y";
		}else{
			$bfspaceallocsql = "SELECT emp_id FROM building_floor_space_allocations WHERE company_id = ".$get['company_id']." AND building_floor_space_id=".$post['building_floor_space_id']." AND is_active = 0"; 
			$bfspaceallocres = $callConfig->getRow($bfspaceallocsql);
			$return['bfspaceallocsql']  = $bfspaceallocsql;
			if($bfspaceallocres->emp_id !=""){
				$emp_id_available =  $bfspaceallocres->emp_id;
				$emp_asssign_status = "N";
			}
		}
		$return['emp_id_available']  = $emp_id_available;
		$return['emp_asssign_status']  = $emp_asssign_status;

		//update all record same building space 
		$callConfig->updateRecord('building_floor_space_allocations',array("is_active"=>null),'building_floor_space_id',$post['building_floor_space_id']);
		//update all record same building space 

		//Start approval usert id get and email or notification get
		if($emp_asssign_status=="Y" OR $emp_asssign_status=="N"){
			$emp_query = "SELECT id, display_name, email_id, user_id, hr_buddy, admin_buddy, loc_buddy  FROM employees WHERE company_id = ".$get['company_id']." AND id=".$emp_id_available; 
			$emp_data = $callConfig->getRow($emp_query);
			$informationlist['employee'][] = $emp_data;

			$locationemp_query = "SELECT id, display_name, email_id, user_id  FROM employees WHERE company_id = ".$get['company_id']." AND id=".$emp_data->loc_buddy; 
			$locationemp_data = $callConfig->getRow($locationemp_query);
			if($locationemp_data->id !=""){
				$informationlist['location_connect'][] = $locationemp_data;
			}
			$location_user_id = $locationemp_data->user_id;
			$location_user_display_name = $locationemp_data->display_name;
			$location_user_email_id = $locationemp_data->email_id;
			$postdataary['approval_user_id'] = $locationemp_data->user_id;
		}
		//End approval usert id get and email or notification get
		if($post['reserve_pool_id'] !=""){
			$postdataary['reserve_pool_allocation_datetime'] = "N";
			$postdataary['reserve_pool_id'] = $post['reserve_pool_id'];
		}
		$resbfsa_id = "";
		if($post['building_floor_space_allocation_id']!=""){	
			$postdataary['updated_date'] = date('Y-m-d H:i:s');
			$postdataary['updated_by'] = $post['user_id'];
			//$postdataary['is_active'] = 0;
			$return['building_floor_space_allocations']=$callConfig->updateRecord('building_floor_space_allocations',$postdataary,'id',$post['building_floor_space_allocation_id']);
			$resbfsa_id = $post['building_floor_space_allocation_id'];
		}else{
			$postdataary['company_id'] = $post['company_id'];
			$postdataary['created_date'] = date('Y-m-d H:i:s');
			$postdataary['created_by'] = $post['user_id'];
			$postdataary['is_active'] = 0;
			$resbfsa = $callConfig->insertRecord('building_floor_space_allocations',$postdataary);	
			$return['building_floor_space_allocations']=$resbfsa;
			$resbfsa_id = $resbfsa['LASTID'];		
		}

		$return['informationlist'] = $informationlist;
		$return['location_user_id'] = $location_user_id;
		$return['location_user_email_id'] = $location_user_email_id;
		$return['location_user_display_name'] = $location_user_display_name;
		if($building_res->space_type_id == '7' AND $location_user_id !="" AND $emp_asssign_status=="Y"){
				unset($informationlist['location_connect']);
				unset($informationlist['employee']);
					$notarry['notification_users'] = array($location_user_id);
					$notarry['company_id']= $get['company_id'];
					$notarry['option'] = 'COM_VIEW_OFFICE_MANAGEMENT&TAB=7';
					$notarry['type'] = "ACTION";
					$notarry['id'] = $resbfsa_id;
					$notarry['notification_message'] = " Request for Cubicle Space Allocation Approval for ".$emp_data->display_name;
				//notification
					$emailarray['approval_user_email_id'] = $location_user_email_id;
					$emailarray['user_display_name'] = $location_user_display_name;
					$emailarray['transaction_date_and_time'] = date("Y-m-d H:i:s");
					$emailarray['to_company_id'] = $get['company_id'];
					$emailarray['session_company_id'] = $get['company_id'];
					$emailarray['default_module_id'] = 34;
					$emailarray['created_by'] = $get['user_id'];
					$emailarray['requester_user_email_id'] = $application_users[$post['user_id']]->user_email;
					$emailarray['requester_display_name'] = $application_users[$post['user_id']]->display_name;
					
					$insertApplication['emails'] =$this->sendNotificationAndEmails($notarry,$emailarray,61);
					$return['notarry'][]=$notarry;
					$return['emailarray'][]=$emailarray;
		}
		if($emp_asssign_status=="Y" OR $emp_asssign_status=="N"){
			
			foreach($informationlist as $keydata=>$userdetails){
				foreach($userdetails as $listdetails){
					//notification
						$notarry['notification_users'] = array($listdetails->user_id);
						$notarry['company_id']= $get['company_id'];
						$notarry['option'] = 'COM_VIEW_OFFICE_MANAGEMENT&TAB=7';
						$notarry['type'] = "INFORMATION";
						$notarry['id'] = $resbfsa_id;
					//notification
					$emailarray['user_email_id'] = $listdetails->email_id;
					$emailarray['user_display_name'] = $emp_data->display_name;
					$emailarray['to_company_id'] = $get['company_id'];
					$emailarray['session_company_id'] = $get['company_id'];
					$emailarray['default_module_id'] = 34;
					$emailarray['created_by'] = $post['user_id'];
					$emailarray['space_name'] = $building_res->cabin_name;
					$emailarray['start_date'] = date('Y-m-d');
					$emailarray['building_name'] = $building_res->building_name;
					$emailarray['building_admin'] = $application_users[$building_res->originator]->display_name;
					if($emp_asssign_status=="Y"){
						$notarry['notification_message'] = "I wanted to inform you that ".$emp_data->display_name." has been allocated to Cubicle Space name ".$building_res->cabin_name;
						$insertApplication['emails'] =$this->sendNotificationAndEmails($notarry,$emailarray,65);
					}elseif($emp_asssign_status=="N"){
						$notarry['notification_message'] = "I wanted to inform you that ".$emp_data->display_name." has been unallocated to Cubicle Space name ".$building_res->cabin_name;
						$insertApplication['emails'] =$this->sendNotificationAndEmails($notarry,$emailarray,66);
					}
					$return['notarry'][]=$notarry;
					$return['emails'][]=$insertApplication;
				}	
			}
		}
		return $return;
	}
	function approvedApaceAllocationsStatus($get){
		global $callConfig;
		$return['get'] =$get;
		$building_floor_space_allocation_id = $get['building_floor_space_allocation_id'];

		$ausql = "SELECT id as user_id, user_email as email_id, display_name FROM application_users WHERE company_id = ".$get['company_id']." AND is_active = 0"; 
		$ausres = $callConfig->getAllRows($ausql);
		foreach($ausres as $ausress){
			$application_users[$ausress->user_id] =  $ausress;
		}


		$sql = "SELECT * FROM `building_floor_space_allocations` WHERE company_id = '".$get['company_id']."' AND id = '".$building_floor_space_allocation_id."' AND is_active = 0";
		$bfsares = $callConfig->getRow($sql);
		$return['bfsares'] =$bfsares;


		$employeessql = "SELECT id, display_name, email_id, user_id  FROM employees WHERE company_id = ".$get['company_id']." AND id=".$bfsares->emp_id; 
		$employeesres = $callConfig->getRow($employeessql);
		$return['employees'] =$employeesres;

		$sql1 = "SELECT bfs.id, bfs.cabin_name, bf.floor_name, build.originator, build.building_name FROM building_floor_spaces bfs, building_floors bf, buildings build WHERE bfs.company_id = '".$get['company_id']."' AND bfs.company_id = bf.company_id AND bfs.company_id = build.company_id AND bfs.building_floor_id = bf.id AND bf.building_id = build.id AND bfs.id = '".$bfsares->building_floor_space_id."' AND bfs.is_active = bf.is_active AND bfs.is_active = build.is_active AND bfs.is_active = 0";
		$buildares = $callConfig->getRow($sql1);
		$return['buildares'] =$buildares;


		$return['building_floor_space_allocations']=$callConfig->updateRecord('building_floor_space_allocations',array('current_status'=>ST_APPROVED),'id',$building_floor_space_allocation_id);

		//notification
		$sendnotifid = array();
		if($buildares->originator !=""){
			$sendnotifid[] = $buildares->originator;
		}
		if($employeesres->user_id !=""){
			$sendnotifid[] = $employeesres->user_id;
		}
		if($get['user_id'] !=""){
			$sendnotifid[] =$get['user_id'];
		}
		$notarry['notification_users'] = $sendnotifid;
		$notarry['company_id']= $get['company_id'];
		$notarry['option'] = 'COM_VIEW_OFFICE_MANAGEMENT&TAB=7';
		$notarry['type'] = "INFORMATION";
		$notarry['id'] = $resbfsa_id;
		$notarry['notification_message'] = $employeesres->display_name." - ".$buildares->cabin_name." - (".$buildares->building_name.") Cubicle Space Allocation Successfully Approved";
		



		$emailarray['user_email_id'] = $employeesres->email_id;
		$emailarray['user_display_name'] = $employeesres->display_name;
		$emailarray['to_company_id'] = $get['company_id'];
		$emailarray['session_company_id'] = $get['company_id'];
		$emailarray['default_module_id'] = 34;
		$emailarray['created_by'] = $get['user_id'];
		$emailarray['space_name'] = $buildares->cabin_name;
		$emailarray['start_date'] = date('Y-m-d');
		$emailarray['building_name'] = $buildares->building_name;
		$emailarray['building_admin'] = $application_users[$buildares->originator]->display_name;
		$insertApplication['emails'] =$this->sendNotificationAndEmails($notarry,$emailarray,65);
		
		//$this->tosendnotifiations($notarry);
		$return['notarry'] =$notarry;
		//notification
		return $return;
	}
	function apiaccountbalancesupdate($get){
		global $callConfig;
		$return['get'] =$get;


		// $sql = "SELECT * FROM goods_receipt_notes WHERE company_id = '".$get['company_id']."' AND current_status = 199 AND is_active = 0 and id = 2";
		// $transaction=$callConfig->getRow($sql);
		// $get['transaction_table']= 'goods_receipt_notes'; 
		// $return['supplier_bills'][] =  $this->updateTransactionItemsQty($get,$transaction,'+');
		
/**

		$transaction_table = "goods_receipt_notes";
		$sql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get['company_id']."' AND current_status = 199 AND is_active = 0";
		$building_res=$callConfig->getAllRows($sql);
		$return['building_res'] = $building_res;
		$return['building_id_sql'] = $sql;
		foreach($building_res as $detail){
			$return['goods_receipt_notes'][] = $this->tocreatejounalvoucherbaseallcases($transaction_table,$detail->id);
		}

		$transaction_table = "purchase_invoices";
		$sql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get['company_id']."' AND current_status = 199 AND is_active = 0";
		$building_res=$callConfig->getAllRows($sql);
		$return['supplier_bills_res'] = $building_res;
		$return['supplier_bills_sql'] = $sql;
		foreach($building_res as $detail){
			$return['purchase_invoices'][] = $this->tocreatejounalvoucherbaseallcases($transaction_table,$detail->id);
		}


		$transaction_table = "delivery_orders";
		$sql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get['company_id']."' AND current_status = 199 AND is_active = 0";
		$building_res=$callConfig->getAllRows($sql);
		$return['building_res'] = $building_res;
		$return['building_id_sql'] = $sql;
		foreach($building_res as $detail){
			$return['delivery_orders'][] = $this->tocreatejounalvoucherbaseallcases($transaction_table,$detail->id);
		}

		$transaction_table = "sales_invoices";
		$sql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get['company_id']."' AND current_status = 199 AND is_active = 0";
		$building_res=$callConfig->getAllRows($sql);
		$return['supplier_bills_res'] = $building_res;
		$return['supplier_bills_sql'] = $sql;
		foreach($building_res as $detail){
			$return['sales_invoices'][] = $this->tocreatejounalvoucherbaseallcases($transaction_table,$detail->id);
		}
**/
		


	// $sql = "SELECT * FROM `sales_invoices` WHERE is_active = 0";
	// $dsfsdcpartyinvoice = $callConfig->getAllRows($sql);	
	// foreach($dsfsdcpartyinvoice as $partyinvoice){ 
	// 	$payment_term_history = json_decode($partyinvoice->payment_term_history,1);
	// 	if(count((array)$payment_term_history) != "0"){
	// 		$pth_cnt = count((array)$payment_term_history);
	// 		$mk = 1;
	// 		foreach($payment_term_history as $key=>$pterh){ 
	// 			$insertdata = array('company_id'=>1,'transaction_table'=>'sales_invoices','transaction_id'=>$partyinvoice->id,'transaction_number'=>$partyinvoice->transaction_number,'party_id'=>$partyinvoice->party_id,'due_date'=>$this->dateconvertdbformat($pterh['due_date']),'due_amount'=>$pterh['amount'],'advance_offer_date'=>$this->dateconvertdbformat($pterh['extra_date']),'advance_offer_amount'=>$pterh['extra_amt'],'pending_amount'=>$pterh['amount'],'is_active'=>'0','current_status'=>'0','created_date'=>date('Y-m-d H:i:s'),'created_by'=>$get['user_id'],'amount'=>$pterh['without_tax_amount'],'tax_amount'=>$pterh['tax_amount'],'percentage'=>$pterh['percentage'],'without_tax_pending_amount'=>$pterh['without_tax_amount'],'tax_pending_amount'=>$pterh['tax_amount']);
	// 			$callConfig->insertRecord('payment_term_transactions',$insertdata);
	// 			$mk++;
	// 		}	
	// 	}
	// }

		echo "<pre>"; print_r($return);
	}
}
?>