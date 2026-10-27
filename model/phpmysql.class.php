<?php
class phpMysqlClass{ 
	function getPHPMYSQLDetails($post){
		global $callConfig;
		$return = array();
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
    	if($post['limit_select'] !=0){
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
		$exicute_sql_textarea = $_POST['exicute_sql_textarea'];
		if($exicute_sql_textarea !=""){
			$sqlresult=$callConfig->executeQuery($exicute_sql_textarea);
			$return['exicute_sql_res'] = $sqlresult;
		}else{
			return $return['error'] == 404; 	
		}
		return $return; 	
	}


}
?>  