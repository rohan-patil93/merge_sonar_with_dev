<?php
class ComplaintsClass{
	function getAllComplaintsData($get){
		global $callConfig;
		$return = array();

		$buildings_details = "SELECT id, building_name FROM  buildings WHERE  company_id='" . $get['company_id'] . "' AND  is_active =0 ";
		$buildings_details = $callConfig->getAllRows($buildings_details);
		$buildings = array();
		$i = 1;
		foreach ($buildings_details as $row) {
			$buildings[$i] = $row;
			$i++;
		}
		$return['buildings'] = $buildings;

		$items_details = "SELECT id, item_name, item_type FROM  items WHERE company_id='" . $get['company_id'] . "' AND is_active =0";
		$items_details = $callConfig->getAllRows($items_details);
		$items = array();
		$i = 1;
		foreach ($items_details as $row){
			$items[$i] = $row;
			$i++;
		}
		$return['items'] = $items;

		$assets_details = "SELECT id, asset_code, asset_description  FROM assets WHERE  company_id='" . $get['company_id'] . "' AND is_active = 0 ";
		$assets_details = $callConfig->getAllRows($assets_details);
		$assets = array();
		$i = 1;
		foreach ($assets_details as $row) {
			$assets[$i] = $row;
			$i++;
		}
		$return['assets'] = $assets;

		return $return;
	}
	function getTableData($get){
		global $callConfig;
		$return = array();
		$table_name = $get['table'] ?? '';
		$id = $get['id'] ?? '';
		$column = $get['column'] ?? '';
		$whr = '';
		if($id && $column){
			$whr = $column.'='.$id.' AND ';
		}
		$table_details = "SELECT * FROM  $table_name 
		WHERE $whr company_id={$get['company_id']} AND  is_active='0' ";
		$table_details = $callConfig->getAllRows($table_details);
		$table = array();
		$i = 1;
		foreach ($table_details as $row) {
			$table[$i] = $row;
			$i++;
		}
		$return['table'] = $table;
		return $return;
	}
	function getComplaintsData($get){
		global $callConfig;
		$return = array();
		if($get['rid'] && $get['rid'] != ''){
			$table_details = "SELECT c.id as id, c.user_id as user_id, c.complaint_name, c.complaint_type, c.complaint_priority, c.transaction_date, i.item_name, a.asset_code, a.asset_description, s.name, s.background_color, s.color, b.building_name,bf.floor_name, bfs.cabin_name , au.display_name , c.created_date
			FROM complaints c
			LEFT JOIN items i ON i.id = c.item_id
			LEFT JOIN assets a ON a.id = c.asset_id
			LEFT JOIN status s ON s.name_value = c.current_status
			LEFT JOIN buildings b ON b.id = c.building_id
            LEFT JOIN building_floors bf ON bf.id = c.building_floor_id
            LEFT JOIN building_floor_spaces bfs ON bfs.id = c.building_floor_space_id
            LEFT JOIN application_users au ON au.id = c.created_by
			WHERE c.id='{$get['rid']}' AND c.company_id='{$get['company_id']}' AND c.is_active='0'";
			$table_details = $callConfig->getRow($table_details);
			$return['table'] = $table_details;
		return $return;
		}
		$data = urldecode($get['data']);
		$data = json_decode($data);
		$complaint_priority = $data->complaint_priority;
		$complaint_type = $data->complaint_type;
		$status = $data->status;
		$transaction_date = $data->transaction_date;
		$whr = '';
		if($complaint_priority && $complaint_priority != 0){
			$whr .= "c.complaint_priority = '$complaint_priority'  AND ";
		}
		if($complaint_type && $complaint_type != 0){
			$whr .= "c.complaint_type = '$complaint_type'  AND ";
		}
		if($status != ''){
			$whr .= "c.current_status = '$status'  AND ";
		}
		if($transaction_date && $transaction_date != ''){
			$whr .= "c.transaction_date = '$transaction_date'  AND ";
		}
		$table_details = "SELECT c.id as id, c.user_id as user_id, c.complaint_name, c.complaint_type, c.complaint_priority, c.transaction_date, i.item_name, a.asset_code, a.asset_description, s.name, s.background_color, s.color FROM complaints c
		LEFT JOIN items i ON i.id = c.item_id
		LEFT JOIN assets a ON a.id = c.asset_id
		LEFT JOIN status s ON s.name_value = c.current_status
		WHERE $whr c.company_id={$get['company_id']} AND  c.is_active='0'";
		$table_details = $callConfig->getAllRows($table_details);
		$table = array();
		$i = 1;
		foreach ($table_details as $row) {
			$table[$i] = $row;
			$i++;
		}
		$return['table'] = $table;
		return $return;
	}
	function getImagesData($get){
		global $callConfig;
		$return = array();
		$table_name = $get['table'] ?? '';
		$id = $get['id'] ?? '';
		$column = $get['column'] ?? '';
		$whr = '';
		if(!empty($id)){
			$whr .= "transaction_id = '$id' AND ";
		}
		if(!empty($column)){
			$whr .= "temp_id = '$column' AND ";
		}
		if(!empty($table_name)){
			$whr .= "application_table_name = '$table_name' AND ";
		}

		$table_details = "SELECT * FROM  application_attachments 
		WHERE $whr company_id='{$get['company_id']}' AND  is_active='0' ";
		$table_details = $callConfig->getAllRows($table_details);
		// echo $table_details;
		$table = array();
		$i = 1;
		foreach ($table_details as $row) {
			$table[$i] = $row;
			$i++;
		}
		$return['table'] = $table;
		return $return;
	}
	function getMobileUsersData($get){
		global $callConfig;
		$return = array();
		$data = json_decode($get['data']);
		$whr = '';
		$user_id = $data->user_id;
		if ($user_id && $user_id != ''){
			$whr .= "(user_name LIKE '%$user_id%' 
		     OR user_email LIKE '%$user_id%' 
		     OR user_mobile LIKE '%$user_id%') AND";
		}
		$from_date = $data->from_date;
		$to_date = $data->to_date;
		$table_details = "SELECT `id`, `user_name`, `user_email`, `user_mobile`, `created_date` FROM  ".MAINDB."."."
		    mobile_user_registrations
		WHERE 
		   $whr DATE(created_date) BETWEEN '$from_date' AND '$to_date' AND is_active='0' ";

		$table_details = $callConfig->getAllRows($table_details);
		$table = array();
		$i = 1;
		foreach ($table_details as $row) {
			$table[$i] = $row;
			$i++;
		}
		$return['table'] = $table;
		return $return;
	}
	function getMobileRegistredUsersData($get){
		global $callConfig;
		$return = array();
		$data = json_decode($get['data']);
		$whr = '';
		$user_id = $data->user_id;
		if ($user_id && $user_id != ''){
			$whr .= "(au.display_name LIKE '%$user_id%' 
		     OR au.user_email LIKE '%$user_id%' 
		     OR uoa.mobile LIKE '%$user_id%') AND";
		}
		$from_date = $data->from_date;
		$to_date = $data->to_date;
		$table_details = "SELECT uoa.id, au.display_name,au.user_email, uoa.mobile,au.created_date 
		FROM  user_otp_authentication uoa
		LEFT JOIN application_users au ON au.id = uoa.user_id
		WHERE $whr DATE(au.created_date) BETWEEN '$from_date' AND '$to_date' AND uoa.company_id='{$get['company_id']}' AND  uoa.is_active='0' AND uoa.current_status='199' ";
		$table_details = $callConfig->getAllRows($table_details);
		// print_r($table_details);
		$table = array();
		$i = 1;
		foreach ($table_details as $row) {
			$table[$i] = $row;
			$i++;
		}
		$return['table'] = $table;
		return $return;
	}

		function getComplaintsResponseData($get){
		global $callConfig;
		$return = array();
		$table_name = $get['table'] ?? '';
		$id = $get['id'] ?? '';
		$column = $get['column'] ?? '';
		$whr = '';
		if($id && $column){
			$whr = $column.'='.$id.' AND ';
		}
		$table_details = "SELECT t1.*, t2.display_name 
			FROM  $table_name t1
			LEFT JOIN application_users t2 ON t2.id = t1.user_id
			WHERE {$whr} t1.company_id='{$get['company_id']}' AND  t1.is_active='0' ";
		$table_details = $callConfig->getAllRows($table_details);
		// print_r($table_details);
		$table = array();
		$i = 1;
		foreach ($table_details as $row) {
			$table[$i] = $row;
			$i++;
		}
		$return['table'] = $table;
		return $return;
	}

	function getAllLocationsData($get){
		global $callConfig;
		$return = array();

		$locations_details = "SELECT id, location_name FROM  locations WHERE  company_id='" . $get['company_id'] . "' AND  is_active =0 ";
		$locations_details = $callConfig->getAllRows($locations_details);
		$locations = array();
		$i = 1;
		foreach ($locations_details as $row) {
			$locations[$i] = $row;
			$i++;
		}
		$return['locations'] = $locations;

		$application_users_details = "SELECT id, display_name FROM  application_users WHERE company_id='" . $get['company_id'] . "' AND is_active =0";
		$application_users_details = $callConfig->getAllRows($application_users_details);
		$application_users = array();
		$i = 1;
		foreach ($application_users_details as $row){
			$application_users[$i] = $row;
			$i++;
		}
		$return['application_users'] = $application_users;

		$countries_details = "SELECT id, country_name, other_code  FROM countries WHERE  company_id='" . $get['company_id'] . "' AND is_active = 0 ";
		$countries_details = $callConfig->getAllRows($countries_details);
		$countries = array();
		$i = 1;
		foreach ($countries_details as $row) {
			$countries[$i] = $row;
			$i++;
		}
		$return['countries'] = $countries;

		return $return;
	}
}
