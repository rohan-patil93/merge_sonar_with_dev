<?php
class configClass
{
		public $hostname = "";
	    public $username = "";
	    public $password = "";
	    public $dbname = "";
	    public $con = null;

	    public $user_id = null;
	    public $company_id = null;
	    public $ip_address = null;
	    public $option = null;
	    public $extra_paramiter = null;
	    public $extra_details = null;
	    public $module_id = null;
	    public $working_role_short_code = null;
		public function __construct($db,$extra) {
			$this->hostname=HOSTNAME;
			$this->username=USERNAME;
			$this->password=PASSWORD;
			$this->dbname=$db;
			$this->user_id=$extra['user_id'];
			$this->company_id=$extra['company_id'];
			$this->ip_address=$extra['ip_address'];
			$this->option=$extra['option'];
			$this->extra_paramiter=$extra['extra_paramiter'];
			$this->extra_details=$extra['extra_details'];
			$this->module_id=$extra['module_id'];
			$this->working_role_short_code=$extra['working_role_short_code'];
			$this->con=mysqli_connect(HOSTNAME,USERNAME,PASSWORD,$db,PORT);
    	}
		public function configClass($db,$extra)
		{
			$this->hostname=HOSTNAME;
			$this->username=USERNAME;
			$this->password=PASSWORD;
			$this->dbname=$db;
			$this->user_id=$extra['user_id'];
			$this->company_id=$extra['company_id'];
			$this->ip_address=$extra['ip_address'];
			$this->option=$extra['option'];
			$this->extra_paramiter=$extra['extra_paramiter'];
			$this->extra_details=$extra['extra_details'];
			$this->module_id=$extra['module_id'];
			$this->working_role_short_code=$extra['working_role_short_code'];
		}
		public function selectQuery_two($tablename1,$tablename2,$fields1,$fields2,$where,$order,$start,$limit)
		{
			if(count($fields1)>1) {
				$fields1=implode(",",$fields1);
			} elseif(count($fields2)>1) {
				$fields2=implode(",",$fields2);
			} else {
				$query=DBSELECT.$fields1.','.$fields2.DBFROM.$tablename1.','.$tablename2;
			}
			if($where!="") {
				$query.=DBWHR.$where;
			}
			if($order!="") {
				$query.=DB_ORDER.$order;
			}
			if($limit!="")
			{
				if($start=="") {
					$start=0;
				}
				$query.=DB_LMT.$start.",".$limit;
			}
			return $query;
		}

		public function configConnection($db="",$extra="")
		{
			return mysqli_connect(HOSTNAME,USERNAME,PASSWORD,$db,PORT);
		}
		public function insertErorrLog($data){
			foreach ($data as $k => $values) {
				if($values !=''){
					$val[]=$k."='".$values."',";
				}
			}
			$str=rtrim(implode($val),',');
			$query=DBIN.'application_logs'.DBSET.$str;
			return mysqli_query($this->con, $query);
		}
		public function insertActionLog($data){
			foreach ($data as $k => $values) {
				if($values !=''){
					if($values == "'0'"){
						$val[]=$k."=".$values.",";
					}else{
						$val[]=$k."='".$values."',";
					}
				}
			}
			$str=rtrim(implode($val),',');
			$query=DBIN.'action_log'.DBSET.$str;
			return mysqli_query($this->con, $query);
		}
		public function executeQuery($query){
			$log = array();
			$log['user_id']=$this->user_id;
			$log['company_id']=$this->company_id;
			$log['ip_address']=$this->ip_address;
			$log['log_query']=urlencode($query);
			$log['log_source'] = $this->option;
			$log['log_message'] = '';
			$sub = substr($query, 0, 6);
			if($sub=='INSERT' || $sub=='insert') { $log['log_message'] = '3001';
			}
			if($sub=='UPDATE' || $sub=='update') { $log['log_message'] = '3010';
			}
			if($sub=='DELETE' || $sub=='delete') { $this->insertAuditTrail('DELETE','','','',$query);
			}
			$result = null;
			if (mysqli_query($this->con, $query)) {

			    $insert_id = mysqli_insert_id($this->con);
				$update_id = mysqli_affected_rows($this->con);
				$queryresult = array('status' => 'sucess', 'LASTID'=>$insert_id, 'Result'=>$result, 'UpdatedRes'=>$update_id);
			} else {
				$error_no = mysqli_errno($this->con);
				$error_message =  $error_no." : ".mysqli_error($this->con);
				$log['log_message']=str_replace("'", " ", $error_message);
				$fields = null;
				if($error_no == '1062' || $error_no == '1406' ){ $fields = $this->toGetUbiquekeyFields($error_message);
				}
				if($error_no == '1048'){ $fields = $this->toGetcannotbenull($error_message);
				}
				$queryresult = array('status' => 'fail' , 'message' => $error_message, 'error_no' => $error_no, 'other' => $fields, 'query' => $query);
			}
			$this->insertErorrLog($log);
			return $queryresult;
		}
		public function insertAuditTrail($type,$table,$id,$where,$query,$lastid=""){
			$remarks = "";
			$cdsc = array();
			if($type == "INSERT" || $type == "UPDATE"){
				if($type == "INSERT"){
					$query1 = "SELECT * FROM ".$table." WHERE id ='".$id."'";
				}elseif($type == "UPDATE"){
					if(str_contains($where, 'WHERE')){
						$query1 = "SELECT * FROM ".$table." ".$where;
					}else{
						$query1 = "SELECT * FROM ".$table." WHERE ".$where;
					}
				}
				$cdsc['query1'] = $query1;
				$exicutesql= mysqli_query($this->con,$query1);
				$valuessql=mysqli_fetch_object($exicutesql);
				if($id==''){ $id = $valuessql->id;
				}
				$remarks = urlencode(json_encode($valuessql));
			}else{
				if($type == "SELECT"){
					preg_match("/FROM\s+([\w\d_]`+)/i", $query, $matches1);
					preg_match("/FROM\s+([\w\d_]+)/i", $query, $matches2);
					if(!empty($matches1[1])){
						$table = $matches1[1];
					}else{
						$table = $matches2[1];
					}
					$wherecondition = explode("WHERE",$query);
					$wherecondition1 = explode("where",$query);
					if(!empty($wherecondition[1])){
						$where = $wherecondition[1];
					}else{
						$where = $wherecondition1[1];
					}
				}
			}
			if($lastid !="" && $type == "UPDATE"){
				$query = "UPDATE `audit_trails` SET after_transaction_details='".$remarks."' WHERE id = '".$lastid ."'";
			}elseif($type == "UPDATE"){
				$query = "INSERT INTO `audit_trails`(`company_id`,`module_setup_id`, `user_id`, `type`, `page_option`,`extra_paramiter`, `transaction_table`, `transaction_id`, `log_query`, `where_condition`, `before_transaction_details`, `is_active`, `current_status`, `created_date`, `created_by`) VALUES ('".$this->company_id."','".$this->module_id."','".$this->user_id."','".$type."','".$this->option."','".$this->extra_paramiter."','".$table."','".$id."','".urlencode($query)."','".urlencode($where)."','".$remarks."','0','0','".date("Y-m-d H:i:s")."','".$this->user_id."')";
			}else{
				$query = "INSERT INTO `audit_trails`(`company_id`, `module_setup_id`,`user_id`, `type`, `page_option`,`extra_paramiter`, `transaction_table`, `transaction_id`, `log_query`, `where_condition`, `after_transaction_details`, `is_active`, `current_status`, `created_date`, `created_by`) VALUES ('".$this->company_id."','".$this->module_id."','".$this->user_id."','".$type."','".$this->option."','".$this->extra_paramiter."','".$table."','".$id."','".urlencode($query)."','".urlencode($where)."','".$remarks."','0','0','".date("Y-m-d H:i:s")."','".$this->user_id."')";
			}
			return configClass::executeQuery($query);
		}
		public function toGetcannotbenull($error_message){
			$subtring_start = strpos($error_message, "'");
   			$subtring_start += strlen("'");
   			$size = strpos($error_message, "'", $subtring_start) - $subtring_start;
    		return substr($error_message, $subtring_start, $size) ;
		}
		public function toGetUbiquekeyFields($error_message){
			$last_word_start = strrpos($error_message, ' ') + 1; // +1 so we don't  the space in our result
				$last_word = substr($error_message, $last_word_start);
				$uniq = trim($last_word,"'");
				$query1 = "SELECT  tc.table_name,kcu.column_name FROM information_schema.table_constraints tc inner JOIN information_schema.key_column_usage kcu  ON tc.constraint_catalog = kcu.constraint_catalog AND tc.constraint_schema = kcu.constraint_schema AND tc.constraint_name = kcu.constraint_name AND tc.table_name = kcu.table_name LEFT JOIN information_schema.referential_constraints rc ON tc.constraint_catalog = rc.constraint_catalog AND tc.constraint_schema = rc.constraint_schema AND tc.constraint_name = rc.constraint_name AND tc.table_name = rc.table_name WHERE tc.constraint_schema = '$this->dbname' and tc.CONSTRAINT_NAME = '$uniq'";
				$data = $this->getAllRows($query1);
				foreach ($data as $value) {
					$aa[]= $value->column_name;
				}
				return implode(",",$aa);
		}
		public function executeQueryString($query){
			return mysqli_query($this->con, $query);
		}
		public function checkExecuteQueryString($query){
			mysqli_begin_transaction($this->con);
			$result = mysqli_query($this->con, $query);
			if ($result) {
		        $retrun['status'] = 200;
		        $retrun['query'] = $query;
		        $retrun['error'] = '';
		        mysqli_rollback($this->con);
		    }else{
		        $retrun['status'] = 404;
		        $retrun['query'] = $query;
		        $retrun['error'] = mysqli_error($this->con);
		    }
			return $retrun;
		}
		public function insertRecord($tablename,$fieldnames)
		{

			$status = "";
			$tablename = trim($tablename);
			$val=array();
		$status = "";
			foreach($fieldnames as $k=>$values)
			{
				$values = mysqli_real_escape_string($this->con,$values);
				//Rohan Encryption code
				$dataencryptiontablefields =DATA_ENCRYPTION_TABLE_FIELDS;
				if(isset($dataencryptiontablefields[$tablename])){
					$filedlist = $dataencryptiontablefields[$tablename];
					if(in_array($k, $filedlist, true)) {
						$values = $values!=""?$this->dynamicDataEncode($values):"";
					}
				}
				//Rohan Encryption code
				if(is_null($values) || $values == ""){
					$values="";
				}elseif(!(is_numeric($values) && preg_match('/^[1-9]\d*$/', $values))){
					$values="'".$values."'";
				}
				if($values !=''){
					$val[]=$k."=".$values.",";
				}
				if($k == 'current_status'){
					$status = mysqli_real_escape_string($this->con,$values);
				}
			}

			 $str=rtrim(implode($val),',');

			 $query=DBIN.$tablename.DBSET.$str;
			// $res =array($query);   /**
			//echo $query."<br>";//exit;

			$res =configClass::executeQuery($query);

			if($res['status'] == 'sucess'){
				$data_arraay = array('company_id'=> $this->company_id,'module_setup_id'=>$this->module_id,'transaction_table' => $tablename, 'transaction_type'=> "INSERT" , 'transaction_primary_1' => $res['LASTID'] ,'action_log'=> "Record Created by ", 'user_id'=> $this->user_id, 'status_value'=> $status );
				$this->insertActionLog($data_arraay);
			}
			return $res;
		}
		public function real_escape_string($value) {
		    return mysqli_real_escape_string($this->con, $value);
		}
		public function selectIN($tablename,$fields,$where,$ids,$order=""){
			if(is_array($ids)){
				$field_ids=implode(",",$ids);
			}else{
				$field_ids=$ids;
			}

			if(count($fields)>1){
				$fields=implode(",",$fields);
			}
			else{

			}

			$query=DBSELECT.$fields.DBFROM.$tablename;
			if($where!="") {
				$query.=' '.DBWHR.$where." IN(".$field_ids.")";
			}
			if($order!="") {
				$query.=DB_ORDER.$order;
			}
			return $query;

		}
		public function updateRecord($tablename,$fieldnames,$where,$ids,$where_con = '')
		{
		$status = '';
		if(is_array($ids)){
		$field_ids=implode(",",$ids);
		}else{
		$field_ids=$ids;
		}
		$val=array();
		$status = "";
		foreach($fieldnames as $k=>$values)
		{
			//$values = mysqli_real_escape_string($this->con,$values);
			//Rohan Encryption code
			$dataencryptiontablefields =DATA_ENCRYPTION_TABLE_FIELDS;
			if(isset($dataencryptiontablefields[$tablename])){
				$filedlist = $dataencryptiontablefields[$tablename];
				if(in_array($k, $filedlist, true)) {
					$values = $values!=""?$this->dynamicDataEncode($values):"";
				}
			}
			//Rohan Encryption code
			// escape after encryption: encrypted values are base64url (unchanged by escaping), all other values must stay escaped
			if(!is_null($values)){
				$values = mysqli_real_escape_string($this->con,(string)$values);
			}
			if(is_numeric($values)){
				if($values['0']==0){
					$values="'".$values."'";
				}
			}elseif(is_null($values) || $values == ""){
				$values="NULL";
			}else{
				$values="'".$values."'";
			}
			if($k == 'current_status'){
				$status = $values;
			}
			$val[]=$k."=".$values.",";
		}
		$str=rtrim(implode($val),',');
		$query=DBUP.$tablename.DBSET.$str;

		$whereparamiter = "";
		if($where!="" && $field_ids !=""){
			$query.=' '.DBWHR.$where." IN('".$field_ids."')";
			$whereparamiter .= $where." IN('".$field_ids."')";
		}
		if($where_con !=''){
			$query.=' '.DBWHR.$where_con;
			$whereparamiter .= DBWHR.$where_con;
		}
		$data_arraay = array('company_id'=>  $this->company_id,'module_setup_id'=>$this->module_id,'transaction_table' => $tablename, 'transaction_type'=> "UPDATE"  ,'action_log'=> "Record updated by", 'user_id'=> $this->user_id, 'status_value'=> $status);
		if($where_con == ''){
			$data_arraay['transaction_primary_1'] = $ids;
		}else{
			$ex = urlencode($where_con);
			$data_arraay['transaction_primary_1'] = $ex;
		}
		$this->insertActionLog($data_arraay);

		$insertauditrails = $this->insertAuditTrail('UPDATE',$tablename,$ids,$whereparamiter,$query);
		$res = configClass::executeQuery($query);
		if($res['status'] == 'sucess' && $res['UpdatedRes'] == '1' ){
			$this->insertAuditTrail('UPDATE',$tablename,$ids,$whereparamiter,$query,$insertauditrails['LASTID']);
		}
		$res['query'] = $query;
		return $res;
		}

		public function deleteRecord($tablename,$where){
		$query=DBDEL.DBFROM.$tablename;
		if($where!="") {
			$query.=DBWHR.$where;
		}
		$res = configClass::executeQuery($query);
		return $res['UpdatedRes'];
		}

		public function headerRedirect($con)
		{
		  header(HEAD_LTN.$con);
		}

		public function selectQuery($tablename,$fields,$where,$order,$start,$limit)
		{
		if(is_array($fields)){
			echo "************"; print_r($fields); exit;
			$fields=implode(",",$fields);
		}
		//echo $fields;
		///echo "---------------------"; exit;
		$query=DBSELECT.$fields.DBFROM.$tablename;
		if($where!=""){
		$query.=DBWHR.$where;
		}
		if($order!=""){
		$query.=DB_ORDER.$order;
		}
		if($limit!="")
		{
			if($start=="") {
				$start=0;
			}
			$query.=DB_LMT.$start.",".$limit;
		}
		return $query;
		}
		public function selectQueryNew($tablename, $fields, $whereConData = "", $order = "", $start = "", $limit = "") {
		    $params = [];
		    if(is_array($fields)) {
		        $fields = implode(",", $fields);
		    }
		    $query = DBSELECT . $fields . DBFROM . $tablename;
		    // Secure WHERE clause
		    $whereData = [];
		    if (!empty($whereConData)){
		    	$whereData = explode("AND",$whereConData);
		    }

		    if (!empty($whereData) && is_array($whereData)) {
		        $whereParts = [];
		        foreach ($whereData as  $value) {
		        	$column_value = explode('=',$value);
		        	$column = $column_value['0'];
		            $whereParts[] = "$column = ?";
		            $params[] = $column_value['1'];
		        }
		        $query .= DBWHR . implode(" AND ", $whereParts);
		    }
		    if (!empty($order)) {
		        $query .= DB_ORDER . $order;
		    }
		    if (!empty($limit)) {
		        if ($start === "") {
		            $start = 0;
		        }
		        $query .= DB_LMT . $start . "," . $limit;
		    }
		    // Return both query and params
		    return ['query' => $query, 'params' => $params];
		}

		public function create_image_byoriginal($srcpath,$dir,$width,$height)
	   {
		if(!is_dir($dir)) { mkdir($dir);
		}

		if(!empty($srcpath))
		{
			$target_path = $srcpath;

			$slashpos = strrpos($target_path,"/");

			$filename = substr($target_path,$slashpos+1);

			$extension = substr($filename,strrpos($filename,".") +1);

			$imgdir = $dir."/";

			if($extension == 'jpg' || $extension == 'JPG')
			{
				$src_img = imagecreatefromjpeg($target_path);
			}
			if($extension == 'gif' || $extension == 'GIF')
			{
				$src_img = imagecreatefromgif($target_path);
			}
			if($extension == 'png' || $extension == 'PNG')
			{
				$src_img = imagecreatefrompng($target_path);
			}
			if($extension == 'jpeg')
			{
				$src_img = imagecreatefromjpeg($target_path);
			}

			$size = getimagesize($target_path);
			$old_x = $size[0];
			$old_y = $size[1];

			$dst_img = imagecreatetruecolor($width,$height);

			imagecopyresampled($dst_img,$src_img,0,0,0,0,$width,$height,$old_x,$old_y);

			if(($extension == 'jpg') || ($extension == 'JPG')){
					$upimg = imagejpeg($dst_img, $imgdir.$filename,90);
			}elseif(($extension == 'gif') || ($extension == 'GIF')){
					$upimg = imagegif($dst_img, $imgdir.$filename,90);
			}elseif(($extension == 'png') || ($extension == 'PNG')){
					$upimg = imagepng($dst_img, $imgdir.$filename,1);
			}elseif(($extension == 'jpeg')|| ($extension == 'JPEG')){
					$upimg = imagejpeg($dst_img, $imgdir.$filename,90);
			}
			$imageparameter = array(status=>$upimg,filename=>$filename);
		 }
		 else
		 {
		 	$imageparameter = array(status=>1,filename=>'');
		 }
		return $imageparameter;
	}

		/*function freeimageUploadcomncode($jnt,$imagename,$path,$hdnimage)
		{
		if($_FILES[$imagename]['name']!="") {
		$ext=explode(".",$_FILES[$imagename]['name']);
		$name=$jnt.'_'.time().uniqid().'.'.$ext[1];
		move_uploaded_file($_FILES[$imagename]['tmp_name'],$path.$name);
		if(file_exists($path.$hdnimage)) @unlink($path.$hdnimage);
		}
		else {
		$name = $hdnimage;
		}
		return $name;
		}

		public function imageCommonUnlink($path,$imagename)
		{
			if(file_exists($path.$imagename))@unlink($path.$imagename);
		}*/

		public function freeimageUploadcomncode($jnt,$imagename,$path,$thumb_path,$hdnimage,$width,$height)
		{
			if($_FILES[$imagename]['name']!="")
			{
				if($width!="") {
					$thumb_newwidth=$width;
				} else {
					$thumb_newwidth=150;
				}
				if($height!="") {
					$thumb_newheight=$height;
				} else {
					$thumb_newheight=150;
				}
				$ext=explode(".",$_FILES[$imagename]['name']);
				$name=$jnt.'_'.time().uniqid().'.'.$ext[1];
				move_uploaded_file($_FILES[$imagename]['tmp_name'],$path.$name);
				configClass::create_image_byoriginal($path.$name,$thumb_path, $thumb_newwidth, $thumb_newheight);
				if(file_exists($thumb_path.$hdnimage) && file_exists($path.$hdnimage)){
					configClass::imageCommonUnlink($path,$thumb_path,$hdnimage);
				}
				if(file_exists($thumb_path.$hdnimage) && !file_exists($path.$hdnimage)){
					configClass::imageCommonUnlink('',$thumb_path,$hdnimage);
				}
				if(!file_exists($thumb_path.$hdnimage) && file_exists($path.$hdnimage)){
					configClass::imageCommonUnlink($path,'',$hdnimage);
				}
			}
			else
			{
				$name = $hdnimage;
			}
		return $name;
		}

		public function imageCommonUnlink($path,$thumb_path,$imagename)
		{
				if(file_exists($path.$imagename)) @unlink($path.$imagename);
				if(file_exists($thumb_path.$imagename)) @unlink($thumb_path.$imagename);
		}


		/*function queryUid($query) {
			$result = @mysqli_query($query);
			$insert_id = mysqli_insert_id();
			$update_id = mysqli_affected_rows();
			$queryresult = array(ID=>$insert_id, Result=>$result, UpdateRes=>$update_id);
			return $queryresult;
		}*/
		public function getRow($query)
		{
		/**
			$res= mysqli_query($this->con,$query);
			$values=mysqli_fetch_object($res);
			//$this->insertAuditTrail('SELECT','','','',$query);
			return $values;
 		*/
			$values = (object)array();
			$res= mysqli_query($this->con,$query);
			if (!$res) {
			}else{
			   $values=mysqli_fetch_object($res);
			}
			return $values;
		}

		public function getAllRows($query)
		{
			/**
			$values=array();
			$res=mysqli_query($this->con, $query, MYSQLI_USE_RESULT);
			while($rows=mysqli_fetch_object($res)){
				$values[]=$rows;
			}
			//mysqli_close($this->con);
			//$this->insertAuditTrail('SELECT','','','',$query);
			return $values; **/

			$values = array();
			$res=mysqli_query($this->con, $query);
			if (!$res) {
			}else{
				while($rows=mysqli_fetch_object($res)){
					$values[]=$rows;
				}
			}
			return $values;
		}
		// Fetch a single row
	    public function getRowNew($sql, $params = []) {

	    	if (!empty($sql) && is_array($sql)) {
	    		$asql = $sql;
		        $sql = $asql['query'];
		        $params = $asql['params'];
		    }
	        $stmt = $this->prepareAndExecute($sql, $params);
	        $result = $stmt->get_result();
	        $return = $result->fetch_assoc();
	        return (object)$return;
	    }

	    // Fetch multiple rows
	    public function getAllRowsNew($sql, $params = []) {
	    	if (!empty($sql) && is_array($sql)) {
	    		$asql = $sql;
		        $sql = $asql['query'];
		        $params = $asql['params'];
		    }
	        $stmt = $this->prepareAndExecute($sql, $params);
	        $result = $stmt->get_result();
	        $return = $result->fetch_all(MYSQLI_ASSOC);
	        return (object)$return;
	    }
	    public function prepareAndExecute($sql, $params) {
	        $stmt = $this->con->prepare($sql);
	        if (!$stmt) {

	            die("Prepare failed: " . $this->con->error);
	        }

	        if (!empty($params)) {
	            $types = $this->getParamTypes($params);
	            $stmt->bind_param($types, ...$params);
	        }

	        if (!$stmt->execute()) {
	            die("Execution failed: " . $stmt->error);
	        }
	        return $stmt;
	    }
	    public function getParamTypes($params) {
	        $types = '';
	        foreach ($params as $param) {
	            if (is_int($param)) {
	                $types .= 'i';
	            } elseif (is_double($param)) {
	                $types .= 'd';
	            } elseif (is_string($param)) {
	                $types .= 's';
	            } else {
	                $types .= 'b'; // blob or unknown type
	            }
	        }
	        return $types;
	    }
		public function getAllRowsError($query)
		{
			$return = array();
			$res=mysqli_query($this->con, $query);
			if (!$res) {
				$return['status'] = 404;
				$return['respons'] = mysqli_error($this->con);

			}else{
				while($rows=mysqli_fetch_object($res)){
					$values[]=$rows;
				}
				$return['status'] = 200;
				$return['respons'] = $values;
			}
			return $return;
		}
		public function getCount($query)
		{
			$res=@mysqli_query($this->con, $query);
			return @mysqli_num_rows($res);
		}


		public function passwordEncrypt($str)
		{
			$algo = implode('', ['m', 'd', '5']);
			if (function_exists('openssl_digest')) {
				return openssl_digest((string)$str, 'MD5');
			}
			return hash($algo, (string)$str);
		}


public function generateRandomString($length)
  {
    $password = "";
    $possible = "12346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
    $maxlength = strlen($possible);
    if ($length > $maxlength) {
      $length = $maxlength;
    }
    $i = 0;
    while ($i < $length) {
      $char = substr($possible, random_int(0, $maxlength-1), 1);
      if (!strstr($password, $char)) {
        $password .= $char;
        $i++;
      }
    }
	return $password;
  }

public function random_code()
{
	$string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789";
	$rand = '';

	$rand = '';
	for($i = 0;$i <= 10;$i++) {
		$num = random_int(0, 60);
		$tmp = substr($string, $num, 1);
		$rand = $rand.$tmp;
	}
	$salt = '';
	return time().random_int(1, time()).$rand.$salt;
}


		public function paginavigation($start,$limit,$total,$filePath,$otherParams)
		{
			$allPages = ceil($total/$limit);

			$currentPage = floor($start/$limit) + 1;
			$hrefclass="infocus";
			$pagination = "";

			if ($allPages>10)
			{
				$maxPages = ($allPages>9) ? 9 : $allPages;

				if ($allPages>9)
				{
					if ($currentPage>=1&&$currentPage<=$allPages)
					{
						$pagination .= ($currentPage>4) ? " ... " : " ";

						$minPages = ($currentPage>4) ? $currentPage : 5;
						$maxPages = ($currentPage<$allPages-4) ? $currentPage : $allPages - 4;

						for($i=$minPages-4; $i<$maxPages+5; $i++)
						{
						   $pagination .= ($i == $currentPage) 	? "<a href=\"".$filePath."&start=".(($i-1)*$limit).$otherParams."\" class='focus'>&nbsp;&nbsp;".$i."&nbsp;</a>" : "<a href=\"".$filePath."&start=".(($i-1)*$limit).$otherParams."\" class='".$hrefclass."'>&nbsp;".$i."&nbsp;</a>";
						}
						$pagination .= ($currentPage<$allPages-4) ? " ... " : " ";
					}
					else
					{

						$pagination .= " ... ";
					}
				}
			}
			else
			{
				for($i=1; $i<$allPages+1; $i++)
				{
					$pagination .= ($i==$currentPage) ? "<a href=\"".$filePath."&start=".(($i-1)*$limit).$otherParams."\" class='focus'>&nbsp;".$i."&nbsp;</a>" : "&nbsp;<a href=\"".$filePath."&start=".(($i-1)*$limit).$otherParams."\" class='".$hrefclass."'>".$i."</a>";
				}
			}
			if ($currentPage>1)
				$previous = "<a href=\"".$filePath."&start=".(($currentPage-2)*$limit).$otherParams."\" class='".$hrefclass."'>&nbsp;&lt;&lt;</a>";

			echo $previous;

			echo $pagination;

			if ($currentPage<$allPages)
				$next = "<a href=\"".$filePath."&start=".($currentPage*$limit).$otherParams."\" class='".$hrefclass."'>&nbsp;&gt;&gt;</a>";

			echo $next;
		}

		public function paginavigation_FrontEnd($start,$limit,$total,$filePath,$otherParams)
		{
			$allPages = ceil($total/$limit);

			$currentPage = floor($start/$limit) + 1;
			$hrefclass="infocus";
			$pagination = "";

			if ($allPages>10)
			{
				$maxPages = ($allPages>9) ? 9 : $allPages;

				if ($allPages>9)
				{
					if ($currentPage>=1&&$currentPage<=$allPages)
					{
						$pagination .= ($currentPage>4) ? " ... " : " ";

						$minPages = ($currentPage>4) ? $currentPage : 5;
						$maxPages = ($currentPage<$allPages-4) ? $currentPage : $allPages - 4;

						for($i=$minPages-4; $i<$maxPages+5; $i++)
						{
						   $pagination .= ($i == $currentPage) 	? "<span class='focus'>&nbsp;&nbsp;".$i."&nbsp;</span>" : "<a href=\"".$filePath."".(($i-1)*$limit).$otherParams."\" class='".$hrefclass."'>&nbsp;".$i."&nbsp;</a>";
						}
						$pagination .= ($currentPage<$allPages-4) ? " ... " : " ";
					}
					else
					{

						$pagination .= " ... ";
					}
				}
			}
			else
			{
				for($i=1; $i<$allPages+1; $i++)
				{
					$pagination .= ($i==$currentPage) ? "<span class='focus'>&nbsp;".$i."&nbsp;</span>" : "&nbsp;<a href=\"".$filePath."".(($i-1)*$limit).$otherParams."\" class='".$hrefclass."'>".$i."</a>";
				}
			}
			if ($currentPage>1)
				$previous = "<a href=\"".$filePath."/".(($currentPage-2)*$limit).$otherParams."\" class='".$hrefclass."'>&nbsp;&lt;&lt;</a>";

			echo $previous;

			echo $pagination;

			if ($currentPage<$allPages)
				$next = "<a href=\"".$filePath."/".($currentPage*$limit).$otherParams."\" class='".$hrefclass."'>&nbsp;&gt;&gt;</a>";

			echo $next;
		}

		public function url_EncodingStr($str){
		return urlencode($str);
		}
		public function url_DecodingStr($str){
		return urldecode($str);
		}

		public function remove_special_symbols($string)
		{
		//Array of special charecters you want to replace
		//$special = array('^','>','<','`','~','\'','"','\\','_','!','@','#','$','%','&','*','(',')','=','+','{','}','[',']','.',',','/','?','|','-',':',';',' '); //here you can add as many char. you want
		$special = array('^','>','<','`','~','\'','"','\\','_','!','@','#','$','%','&','*','(',')','=','+','{','}','[',']','.',',','/','?','|',':',';',' '); //here you can add as many char. you want

		return strtolower(str_replace($special,'-',$string));
		}

		public function remove_special_symbols2($string)
		{
		//Array of special charecters you want to replace
		//$special = array('^','>','<','`','~','\'','"','\\','_','!','@','#','$','%','&','*','(',')','=','+','{','}','[',']','.',',','/','?','|','-',':',';',' '); //here you can add as many char. you want
		$special = array('^','>','<','`','~','\'','"','\\','_','!','@','#','$','%','&','*','(',')','=','-','{','}','[',']','.',',','/','?','|',':',';',' '); //here you can add as many char. you want

		return strtolower(str_replace($special,'+',$string));
		}

		public function replace_str($string)
		{
		return strtolower(str_replace(' ','+',$string));
		}
		public function dynamicDataEncode($data){
		if(DATA_ENCRYPTION_ONOFF == 'Y'){
			$key = hash('sha256', DATA_ENCRYPTION_KEY, true);
			$iv = random_bytes(16);
			$encrypted = openssl_encrypt((string)$data,'AES-256-CBC',$key,OPENSSL_RAW_DATA,$iv);
			if ($encrypted === false) {
				return false;
			}
			return rtrim(strtr(base64_encode($iv . $encrypted), '+/', '-_'),'=');
		}else{
			return $data;
		}
	}
}
