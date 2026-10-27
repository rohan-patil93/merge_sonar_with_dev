<?php
class phpMysqlClass{ 
	/**
	 * Server-side RBAC gate for the raw SQL endpoints. Must be called by every
	 * controller/route BEFORE any method of this class is dispatched.
	 * Allows only an authenticated session whose role list contains SUPERADMIN_ROLE;
	 * anything else gets HTTP 403 and the request is terminated.
	 */
	static function requireSuperAdmin(){
		if(session_status() !== PHP_SESSION_ACTIVE && !headers_sent()){
			session_start();
		}
		$allowed = false;
		if(isset($_SESSION) && !empty($_SESSION['userid'])){
			$allowed = is_array($_SESSION['role_ids'] ?? null) && in_array(SUPERADMIN_ROLE, $_SESSION['role_ids'], true);
		}
		if(!$allowed){
			http_response_code(403);
			header('Content-Type: application/json');
			echo json_encode(array('status'=>'Fail','code'=>'FORBIDDEN','msg'=>'Access denied'));
			exit;
		}
	}
	function getPHPMYSQLDetails($post){
		global $callConfig;
		$return = array();
		$table_list = array();
    	$return['dbname'] = $callConfig->dbname;;
    	$row=$callConfig->getAllRows("SHOW TABLES");
		$rowdb = 'Tables_in_'.$callConfig->dbname;
		foreach($row as $table){
			$table_list[] = $table->$rowdb;
		}
		$return['table_list'] = $table_list;
		return $return; 
	}
	function postPHPMYSQLDetails($get,$post){
		global $callConfig;
		$return['post'] = $post;
    	$sql_textarea = $post['sql_textarea'];
    	$return['requist_data_sql'] =$sql_textarea;
    	$post['limit_select'] = max(0, (int)($post['limit_select'] ?? 0));
    	if($post['limit_select'] !=0){
    		$removellimtsql = $sql_textarea;
	    	if(strpos($sql_textarea,'LIMIT')){
	    		$removellimt = explode("LIMIT",$sql_textarea);
				$removellimtsql = $removellimt[0];
				$removellimtres=$callConfig->getAllRowsError($removellimtsql);
				//$return['error_status'] = $removellimtres['status'];
				$return['removellimtsql'] =$removellimtsql;
				if($removellimtres['status'] == '200'){
					$respons_data_cnt = count((array)$removellimtres['respons']);
		    		$return['total_record_cnt'] = $respons_data_cnt;
		    	}else{
	    			$respons_data_cnt = 0;
		    		$return['total_record_cnt'] = $respons_data_cnt;
	    		}
	    	}else{
	    		
	    		$sql_rewsult=$callConfig->getAllRowsError($sql_textarea);
	    		$return['else_removellimtsql'] =$sql_textarea;
	    		if($sql_rewsult['status'] == '200'){
	    			//$return['error_status'] = $sql_rewsult['status'];
		    		$respons_data_cnt = count((array)$sql_rewsult['respons']);
		    		$return['total_record_cnt'] = $respons_data_cnt;
	    		}else{
	    			//$return['error_status'] = $sql_rewsult['status'];
	    			$respons_data_cnt = 0;
		    		$return['total_record_cnt'] = $respons_data_cnt;
	    		}
			}
			if($respons_data_cnt !="0"){
		    	$divide_limit = $respons_data_cnt/$post['limit_select'];
				$total_pages = ceil($respons_data_cnt / $post['limit_select']);
				$current_page = isset($post['current_page']) ? (int)$post['current_page'] : 1;
				$current_page = max(1, min($current_page, $total_pages));
				$start_from = ($current_page - 1) * $post['limit_select'];
				$sql_textarea = $removellimtsql." LIMIT ".$start_from.", ".$post['limit_select'];
				$return['total_pages'] = $total_pages;
			}
			$sql_rewsult=$callConfig->getAllRowsError($sql_textarea);
		}else{
			$sql_rewsult=$callConfig->getAllRowsError($sql_textarea);
		}
		if($sql_rewsult['status'] == '200'){
			$return['error_status'] = $sql_rewsult['status'];
			$return['respons_data_cnt'] = count((array)$sql_rewsult['respons']);
	    	$return['respons_data'] = $sql_rewsult['respons'];
    	}else{
    		$return['respons_data_cnt'] = 0;
    		$return['error_status'] = $sql_rewsult['status'];
	    	$return['respons_data'] = $sql_rewsult['respons'];
    	}
		$return['final_data_sql'] =$sql_textarea;
    	
		return $return; 
	}
	function getfieldlist($get){
		global $callConfig;
		$return = array();
		if($get['table_name'] !=""){
			$getfiledsql = "SHOW COLUMNS FROM ".$get['table_name'];
			$colums_list=$callConfig->getAllRows($getfiledsql);
			$return['colums_list'] = $colums_list;
		}
		return $return; 
	}
	function exicutePHPMYSQL(){
		global $callConfig;
		$return = array();
		$exicute_sql_textarea = $_POST['exicute_sql_textarea'] ?? '';
		if($exicute_sql_textarea !=""){
			$sqlresult=$callConfig->executeQuery($exicute_sql_textarea);
			$return['exicute_sql_res'] = $sqlresult;
		}else{
			return false; // previously `$return['error'] == 404` (always false, undefined-index warning)	
		}
		return $return; 	
	}


}
