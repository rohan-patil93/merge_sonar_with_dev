<?php 
class UserTokenClass {

	function tocheckusertoken($get){
		global $callConfig;
		/*$company_id =$get['company_id'];
		$user_token =$get['token'];
		$sql = "SELECT * FROM appliccation_user_login_tokens WHERE is_active = 0 AND company_id = ? AND user_token = '?'";*/
		$login_type = $get['platform'] !="" ? $get['platform'] : 'Web';
		$whrs2 = "company_id= ".$get['company_id']." AND user_token='".$get['token']."' AND user_id = ".$get['user_id']." AND login_type = '".$login_type."' AND is_active=0";
		//$query = $callConfig->selectQueryNew('appliccation_user_login_tokens','*',$whrs2,'','','');
		//print_r($query); exit;
    //$return = $callConfig->getRowNew($query);
		$query = $callConfig->selectQuery('appliccation_user_login_tokens','*',$whrs2,'','','');
		//echo $query;
		$return = $callConfig->getRow($query);
		//print_r($get); print_r($return); exit;
		$res = array('status'=>'Fail', 'msg'=>'Token is invalid');
		if($return->id!=""){
				$res = array('status'=>'Success', 'msg'=>'Token is invalid');
		}
		return $res;
	}

}
?>