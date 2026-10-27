
<?php

class userClass

{ 
	/* start */
	 function checkAdmin($post) {
	    global $callConfig;
	   //print_r($post); echo "kkkkk"; //exit;
	   /*$where="user_email='".$post['chrUserName']."' and user_password='".$callConfig->passwordEncrypt($post['chrPassword'])."' and is_active ='0'";
		$query=$callConfig->selectQuery(MAINDB.'.'.TBL_USER_REGISTRATIONS,'*',$where,'','','');*/
		$where="user_email='".$post['chrUserName']."'  and is_active ='0'";
		$query=$callConfig->selectQuery(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,'*',$where,'','','');
		//echo $query; exit;
		$row=$callConfig->getRow($query);
		//print_r($row); exit;
		if($row->id!="" && $row->email_active==1) {
			$whr=" id='".$row->zqr_connect."' and  is_active='0'";
			$query_db=$callConfig->selectQuery(MAINDB.'.'.TBL_DATABASE_ALLOCATIONS,'*',$whr,'','','');
			//echo $query_db; exit;
			$row_db=$callConfig->getRow($query_db);
			//print_r($row_db);
			//echo "<pre>"; 
			$row->db = $row_db->db_name;
			$wheres ="user_email='".$post['chrUserName']."' and user_password='".$callConfig->passwordEncrypt($post['chrPassword'])."' and is_active ='0'";
			$query_user=$callConfig->selectQuery($row_db->db_name.'.application_users','*',$wheres,'','','');
			//echo $query_user;
			$user = $callConfig->getRow($query_user);
			if($user->id != ''){
				$user->db = $row_db->db_name;
				//print_r($post); exit;
				$timm =date("Y-m-d H:i:s");
				//echo $timm;
				$fieldnames=array('user_id'=>$user->id,'login_from'=>$timm,'login_ip'=>$post['ip'],'login_browser'=>$post['browser']);
				//print_r($fieldnames); exit;
				$query_last = "SELECT * FROM ".$row_db->db_name.".application_user_logins where user_id =".$user->id." ORDER BY id DESC LIMIT 0,1";
				$last = $callConfig->getRow($query_last);
				$user->lastlogin =$last->login_from;
				$user->reset_password = $row->reset_password;
				$res=$callConfig->insertRecord($row_db->db_name.'.application_user_logins',$fieldnames);
				//print_r($row); exit;
				unset($user->user_password);
				//unset($user->remember_token);
				$return = $user;
			}else{
				$return ['error'] = '6002';
			}
		}else{
			
			if($row->email_active === '0') {
				//echo "ravi".$row->email_active."fa";
				$return ['error'] = '6004';
			}else{
				//echo "ravi".$row->email_active."er";
				$return ['error'] = '6005';
			}
		}

		//print_r($row);exit;
		return $return;		
		
	 } 	
	 function confrimbutton($id){
	 	global $callConfig;
	 	$time =date("Y-m-d H:i:s");
		$fied = array('email_active' =>1,'email_active_date' => $time);
		$res=$callConfig->updateRecord(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,$fied,'id',$id);
		return $res;
	 }
	 function logout($data){
	 		global $callConfig;
	 		$timm =date("Y-m-d H:i:s");
			$fieldnames=array('login_to'=>$timm);
			//print_r($data); exit;
			$res=$callConfig->updateRecord('application_user_logins',$fieldnames,'user_id',$data);
			return $res;
	 }
	 function genrateRandomString($n) { 
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
	    $randomString = ''; 
	  
	    for ($i = 0; $i < $n; $i++) { 
	        $index = rand(0, strlen($characters) - 1); 
	        $randomString .= $characters[$index]; 
	    } 
	  
	    return $randomString; 
	} 
	 function forgotpassword($email){
	 	global $callConfig;
	 	$whrs="user_email= '".$email."'";
		$querys=$callConfig->selectQuery(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,'id,zqr_connect',$whrs,'','','');
		//echo $querys; exit;
		$rowg=$callConfig->getRow($querys); 
		if($rowg->id != ''){
	 	$whr = "id = '".$rowg->zqr_connect."'";
		$query=$callConfig->selectQuery(MAINDB.'.'.TBL_DATABASE_ALLOCATIONS,'*',$whr,'','','');
		$row=$callConfig->getRow($query);
		$db_name = $row->db_name;
		//print_r($row); exit;
		$gen_password = $this->genrateRandomString(7);
		$password = $callConfig->passwordEncrypt($gen_password);
		$db_id = $row->id;
		//echo $db_id; exit;
		$fied = array('reset_password' => '0');
		$res1 = $callConfig->updateRecord(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,$fied,'id',$rowg->id);
		$fied = array('user_password' =>$password);
		$whrcon = "user_email='".$email."'";
		//echo $whrcon; exit;
		$res1 = $callConfig->updateRecord($db_name.'.application_users'	,$fied,'','',$whrcon);
		//echo $res1; exit;
		$data['email_template'] = 2;
		$data['email_content_id'] = 3;
		$data['email_id'] = $email;
		$data['password'] = $gen_password;
		$data['user_id'] =  $rowg->id;
		$data['data_name'] = $db_name;
		$email_array = $this->emailtemplate($data);
		$file_name = 'email'.'_'.time().uniqid().'.'.'html';
		$email_array ['content'] = $file_name;
		$email_array['revicer'] = array($email);
		$email_array['email_type'] = 1;
		$email = $email_array['mesg'];
		
		//print_r($email_array); exit;
		$file_path='uploads/email/'.$file_name;
		file_put_contents($file_path, $email);
		fclose($file_path);
		$lenn = $this->sendEmailslist($email_array);
		//print_r($lenn); exit;
		}else{
			$lenn['error'] = '6005';
		}
		return $lenn;
	 }
	
	 function NewUserformate($post,$db_name){
	 	global $callConfig;
	 	//echo "ravi"; exit;
	 	$whr="db_name = '".$db_name."'";
		$query=$callConfig->selectQuery(MAINDB.'.'.TBL_DATABASE_ALLOCATIONS,'*',$whr,'','','');
		$row=$callConfig->getRow($query);
		//print_r($row); exit;
		$gen_password = $this->genrateRandomString(7);
		$password = $callConfig->passwordEncrypt($gen_password);
		$db_id = $row->id;
		$whrs="zqr_connect= '".$db_id."'";
		$querys=$callConfig->selectQuery(MAINDB.'.'.TBL_REGISTIRED_GROUPS,'*',$whrs,'','','');
		$rowg=$callConfig->getRow($querys);
		$user_registired_groups = array('group_id'=>$rowg->id,'user_email'=>$post['user_email'],'zqr_connect'=>$db_id,'current_status'=>'0','is_active' =>'0');
		//print_r($user_registired_groups); exit;

		$res_rg_group_user = $callConfig->insertRecord(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,$user_registired_groups);
		$user_id = base64_encode($res_rg_group_user['LASTID']);
		$fied = array('user_password' =>$password);
		$whrcon = "user_email='".$post['user_email']."'";
		$res1 = $callConfig->updateRecord($db_name.'.application_users'	,$fied,'','',$whrcon);
		$data['email_template'] = 2;
		$data['email_id'] = $post['user_email'];
		$data['password'] = $gen_password;
		$data['user_id'] =  $user_id;
		$data['email_content_id'] = 2;
		$email_array = $this->emailtemplate($data);
		$file_name = 'email'.'_'.time().uniqid().'.'.'html';
		$email_array ['content'] = $file_name;
		$email_array['revicer'] = array($post['user_email']);
		$email_array['email_type'] = 1;
		$email = $email_array['mesg'];
		
		//print_r($email_array); exit;
		$file_path='uploads/email/'.$file_name;
		file_put_contents($file_path, $email);
		fclose($file_path);
		$lenn = $this->sendEmailslist($email_array);
		//print_r($lenn); exit;
		return $lenn;
	 }
	 function insertAdminUser($post){
		global $callConfig;
		//print_r($post); echo "lkil"; exit;
		$whr="allcoated_date  IS NULL";
		//echo $whr; exit;
		$query=$callConfig->selectQuery(MAINDB.'.'.TBL_DATABASE_ALLOCATIONS,'*',$whr,'','','');
		//echo $query;
		$row=$callConfig->getRow($query);
		$db_id = $row->id;
		$db_name = $row->db_name;
		//print_r($row);
		//echo $db_id."&&".$db_name; exit;
		//	$db_id = ;
		$fieldnames=array('first_name'=>$post['name'],'`user_password`'=>$callConfig->passwordEncrypt($post['password']),'user_email'=>$post['email'],'current_status'=>'0','is_active' =>'0');
		//print_r($fieldnames); exit;
		//$callConfig->headerRedirect("index.php");
		$res=$callConfig->insertRecord($db_name.'.application_users',$fieldnames);
		$res_id = $res['LASTID'];
		$fieldnames['zqr_connect'] = $db_id;
		$res=$callConfig->insertRecord(MAINDB.'.'.TBL_USER_REGISTRATIONS,$fieldnames);
		$time = date("Y-m-d H:i:s");
		$fied = array('allcoated_date' =>$time);
		$res1 = $callConfig->updateRecord(MAINDB.'.'.TBL_DATABASE_ALLOCATIONS,$fied,'id',$db_id);
		$res_id = $res['LASTID'];
		$registired_groups_array = array('group_name'=>$post['name'].'_'.$res_id,'primary_email'=>$post['email'],'no_of_lisc'=>'1','no_of_used_lisc'=>'1','zqr_connect'=>$db_id,'current_status'=>'0','is_active' =>'0');
		$res_rg_group = $callConfig->insertRecord(MAINDB.'.'.TBL_REGISTIRED_GROUPS,$registired_groups_array);

		$user_registired_groups = array('group_id'=>$res_rg_group['LASTID'],'user_email'=>$post['email'],'zqr_connect'=>$db_id,'current_status'=>'0','is_active' =>'0');
		$res_rg_group_user = $callConfig->insertRecord(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,$user_registired_groups);
		$user_id = base64_encode($res_rg_group_user['LASTID']);
		$url = SITEURL."/login.php?confirm=".$user_id;


		$message = '
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 
<head>
  <style type="text/css">
  html {
  font-family: sans-serif;
  line-height: 1.15;
  -webkit-text-size-adjust: 100%;
  -webkit-tap-highlight-color: rgba(0, 0, 0, 0); }
  body {margin: 0; padding: 0; min-width: 100%!important;}
  img {height: auto;}
  .content {width: 100%; max-width: 70%;}
  .header {padding: 40px 30px 20px 30px;}
  .innerpadding {padding: 15px 30px;}
  .innerpaddingone {padding: 0px 30px;}
  .borderbottom {border-bottom: 1px solid #f2eeed;}
  .subhead {font-size: 15px; color: #ffffff; font-family: sans-serif; letter-spacing: 10px;}
  .h1, .h2, .bodycopy {color: #153643; font-family: sans-serif;}
  .h1 {font-size: 33px; line-height: 38px; font-weight: bold;}
  .h2 {padding: 0 0 15px 0; font-size: 18px; line-height: 28px; font-weight: bold;}
  .bodycopy {font-size: 16px; line-height: 22px;}
  .button {
  background-color: #00abfb;
    border-radius: 0.25rem;
    text-align: center;
    font-size: 18px;
    font-family: sans-serif;
    font-weight: bold;
    padding: 0 30px 0 30px;
    height: 45px;
    color: #FFFFFF;
    border: 1px solid transparent;
	}
  .button a {color: #ffffff; text-decoration: none;}
  .footer {padding: 20px 30px 15px 30px;}
  .footercopy {font-family: sans-serif; font-size: 14px; color: #ffffff;}
  .footercopy a {color: #ffffff; text-decoration: underline;}

  @media only screen and (max-width: 550px), screen and (max-device-width: 550px) {
  body[yahoo] .hide {display: none!important;}
  body[yahoo] .buttonwrapper {background-color: transparent!important;}
  body[yahoo] .button {padding: 0px!important;}
  body[yahoo] .button a {background-color: #f0f0f0; padding: 15px 15px 13px!important;}
  body[yahoo] .unsubscribe {display: block; margin-top: 20px; padding: 10px 50px; background: #2f3942; border-radius: 5px; text-decoration: none!important; font-weight: bold;}
  }

  /*@media only screen and (min-device-width: 601px) {
    .content {width: 600px !important;}
    .col425 {width: 425px!important;}
    .col380 {width: 380px!important;}
    }*/

  </style>
</head>

<body yahoo bgcolor="#f6f8f1">
<table width="100%" bgcolor="#f6f8f1" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td>
    <!--[if (gte mso 9)|(IE)]>
      <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td>
    <![endif]-->     
    <table bgcolor="#ffffff" class="content" align="center" cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td bgcolor="#fofofo" class="header">
          <table width="70" align="left" border="0" cellpadding="0" cellspacing="0">  
            <tr>
              <td height="70" style="padding: 0 20px 20px 0;">
                <img class="fix" src="'.SITEURL.'/assets/media/logos/logo-6-sm.png" width="70" height="70" border="0" alt="" />
              </td>
            </tr>
          </table>
          <!--[if (gte mso 9)|(IE)]>
            <table width="425" align="left" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td>
          <![endif]-->
          <table class="col425" align="left" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 425px;">  
            <tr>
              <td height="70">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="h1" style="padding: 5px 0 0 0;">
                      Infisuite
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <!--[if (gte mso 9)|(IE)]>
                </td>
              </tr>
          </table>
          <![endif]-->
        </td>
      </tr>
	  <tr>
        <td class="innerpadding borderbottom">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="h2" style="text-align:center">
                Welcome!
              </td>
            </tr>
          </table>
        </td>
      </tr>
	<tr >
		<td class="innerpadding bodycopy">
			Dear '.ucwords($post['name']).'. 
			
		</td>
	</tr>
	<tr>
		<td class="innerpadding bodycopy">
			We`re excited to have you get started. First, you need to confirm your account. Just press the button below.
		</td>
	</tr>
	<tr>
		<td class="innerpaddingone bodycopy">
		 <button class=" buttonwrapper button"><a href="'.$url.'">Confirm </a></button>
		</td>
	</tr>
      <tr>
        <td class="innerpadding bodycopy">
				If that doesn`t work, copy and paste the following link in your browser:</br></br>
		</td>
      </tr>
      <tr>
        <td class="innerpaddingone bodycopy">
				'.$url.'
		</td>
      </tr>
	  <tr>
        <td class="innerpadding bodycopy">
				If you have any questions, just reply to this email—we`re always happy to help out.
		</td>
      </tr>
      <tr>
        <td class="innerpadding bodycopy">
		Cheers,
		</td>
      </tr>
      <tr>
        <td class="innerpaddingone bodycopy">
		Team Infistaq
		</td>
      </tr>
      <tr>
        <td class="footer" bgcolor="#44525f">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="center" class="footercopy">
                © Infisuite 2020<br/>
               <!---  <a href="#" class="unsubscribe"><font color="#ffffff">Unsubscribe</font></a> 
                <span class="hide">from this newsletter instantly</span> -->
              </td>
            </tr>
            
          </table>
        </td>
      </tr>
    </table>
    <!--[if (gte mso 9)|(IE)]>
          </td>
        </tr>
    </table>
    <![endif]-->
    </td>
  </tr>
</table>
</body>
</html>';

		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		// More headers
		$headers .= 'From:infisuite@dev.mylejar.com'. "\r\n";
		//$headers .= 'Cc: myboss@example.com' . "\r\n";
		$subject = "Welcome";
		mail($post['email'],$subject,$message,$headers);
		return $res;
	}
	 /* End */

	  function getCountAllTablesDataCount($tablename,$fieldname,$where){
		global $callConfig;
		if($where!="")
		$whr=$where;
		$query=$callConfig->selectQuery($tablename,$fieldname,$whr,'','','');
		return $callConfig->getCount($query);
	  }  
	  function getAllTablesData($tablename,$fieldnames='',$where='',$order='',$limit1='',$limit2=''){
		global $callConfig;
		$fieldname = ($fieldnames == '') ? '*' : $fieldnames;

		$whr = ($where == '') ? '' : $where;
		//echo $where;
		$query=$callConfig->selectQuery($tablename,$fieldname,$whr,$order,$limit1,$limit2);
		//echo $tablename."//".$query;exit;
		return $callConfig->getAllRows($query);
		//echo $callConfig->getAllRows($query); 
	 }
	 function getrowTablesData($tablename,$fieldnames='',$where='',$order='',$limit1='',$limit2=''){
		global $callConfig;
		$fieldname = ($fieldnames == '') ? '*' : $fieldnames;
		$whr = ($where == '') ? '' : $where;
		$query=$callConfig->selectQuery($tablename,$fieldname,$whr,$order,$limit1,$limit2);
		//echo $fieldname."//".$query;
		//$data = $callConfig->getRow($query);
		//print_r($data);
		return $callConfig->getRow($query);
	 }
	 function insertTablesdata($fieldnames, $table)	{
		global $callConfig;
		//echo $table."//"; print_r($fieldnames);// exit;
		$res=$callConfig->insertRecord($table,$fieldnames);
		return $res;
	}
	function updateTablesdata($fieldnames, $table, $whr, $value){		
		//echo "jjs"; exit;
		global $callConfig;
		//print_r($fieldnames);  echo $table."//".$whr."//".$value;
		// exit;
		$res=$callConfig->updateRecord($table,$fieldnames,$whr,$value);
		return $res;
	}
	  function getAdminUserData($id) {
	  //	echo "good";
		global $callConfig;
		$query=$callConfig->selectQuery(TBL_USERS,'*','id='.$id,'','','');
		//echo $query;
		$data = $callConfig->getRow($query);
		/*$query=$callConfig->selectQuery(TBL_USER_COMPINES,'company_id','user_id='.$id,'','','');
		$data1 = $callConfig->getRow($query);
		$data->company_id = $data1->company_id;*/
		return $data;
	  }

	
    function userAuthentication(){
		if($_SESSION['userid']!="")	{
			header("location:index.php?option=com_dashboard");
		}
	}
	
	function updateAdminUser($post)
	{

	global $callConfig;
	//print_r($post);
	 $fieldnames=array('name'=>$post['name'],'email'=>$post['email'],'phone'=>$post['phone'],'user_photo'=>$post['image_name']);
	 //print_r($fieldnames); echo "dsf"; exit;
	$res=$callConfig->updateRecord(TPREFIX.TBL_USERS,$fieldnames,'id',$post['hdn_user_id']);
	return $res;
	}
	function addrole($post) {
		global $callConfig;
		$option = array();
		$option ['add'] = isset($post['add']) ? $post['add'] : "off";
		$option ['edit'] = isset($post['edit']) ? $post['add'] : "off";
		$option ['delete'] = isset($post['delete']) ? $post['add'] : "off";
		$data = json_encode($option);
		$fieldnames=array('name'=>$post['name'],'status'=>$post['status'],'permissions'=>$data);
		//print_r($fieldnames); exit;
		$res=$callConfig->insertRecord(TPREFIX.TBL_ROLE,$fieldnames);
		return $res;
	}
	function updateRole($post)
	{
		//echo "asdasd"; exit;
	global $callConfig;
	$option = array();
	$option ['add'] = isset($post['add']) ? $post['add'] : "off";
	$option ['edit'] = isset($post['edit']) ? $post['add'] : "off";
	$option ['delete'] = isset($post['delete']) ? $post['add'] : "off";
	$data = json_encode($option);
	$fieldnames=array('name'=>$post['name'],'status'=>$post['status'],'permissions'=>$data);
	
	 print_r($fieldnames); //exit;
	$res=$callConfig->updateRecord(TPREFIX.TBL_ROLE,$fieldnames,'role_id',$post['role_id']);
	
	return $res;
	}

	function getRoleData(){
		global $callConfig;
		$query=$callConfig->selectQuery(TPREFIX.TBL_ROLE,'*','','','','');
		return $callConfig->getAllRows($query);

	}
	function getoneRoleData($rid){
		global $callConfig;
		$whr="role_id = ".$rid;
		$query=$callConfig->selectQuery(TPREFIX.TBL_ROLE,'*',$whr,'','','');
		//echo $query; exit;
		return $callConfig->getRow($query);

	}
	function getSessions() {
		global $callConfig;
		$query=$callConfig->selectQuery(TPREFIX.TBL_SESSIONS,'*','','','','');
		return $callConfig->getAllRows($query);
	}
	function getBillHistoryData(){
		global $callConfig;
		$query=$callConfig->selectQuery(TPREFIX.TBL_BILLHISTORIES_VW,'*','','','','');
		return $callConfig->getAllRows($query);
	}
	function getModuleHistories(){
		global $callConfig;
		$query=$callConfig->selectQuery(TPREFIX.TBL_MODULEHISTORIES_VW,'*','','','','');
		return $callConfig->getAllRows($query);
	}
	function getRevenuesDetaills(){
		global $callConfig;
		$query=$callConfig->selectQuery(TPREFIX.TBL_REVENUES,'*','','','','');
		return $callConfig->getAllRows($query);
	}
	function insertCategories($post)	{
	global $callConfig;
	//print_r($post); echo "lkil";
	$fieldnames=array('company_id'=>$post['company_id'],'name'=>$post['name'],'type'=>$post['type'],'color'=>$post['color'],'enabled'=>$post['enable']);
	//print_r($fieldnames); exit;
	//$callConfig->headerRedirect("index.php");
	$res=$callConfig->insertRecord(TPREFIX.TBL_CATEGORIES,$fieldnames);
	return $res;
	}
	function updatecategories($post)
	{
	
	global $callConfig;
	$fieldnames=array('company_id'=>$post['company_id'],'name'=>$post['name'],'type'=>$post['type'],'color'=>$post['color'],'enabled'=>$post['enable']);
	//print_r($fieldnames); exit;
	$res=$callConfig->updateRecord(TPREFIX.TBL_CATEGORIES,$fieldnames,'id',$post['id']);
	return $res;
	}
	function insertCompanies($post)	{
	global $callConfig;
	//print_r($post); echo "lkil";
	$fieldnames=array('domain'=>$post['domain'],'enabled'=>$post['enabled']);
	//print_r($fieldnames); exit;
	//$callConfig->headerRedirect("index.php");
	$res=$callConfig->insertRecord(TPREFIX.TBL_COMPANIES,$fieldnames);
	return $res;
	}
	function updateCompanies($post)
	{
	
	global $callConfig;
	$fieldnames=array('domain'=>$post['domain'],'enabled'=>$post['enabled']);
	//print_r($fieldnames); exit;
	$res=$callConfig->updateRecord(TPREFIX.TBL_COMPANIES,$fieldnames,'id',$post['id']);
	return $res;
	}
	function insertConfig($post)	{
	global $callConfig;
	//print_r($post); echo "lkil";
	$fieldnames=array('name'=>$post['name'],'liscense_number'=>$post['liscense_number'],'admin_email'=>$post['admin_email'],'tax_number'=>$post['tax_number'],'phone_number'=>$post['phone_number'],'billing_address'=>$post['billing_address'],'contact_address'=>$post['contact_address'],'enabled'=>$post['enabled'],'display_logo'=>$post['display_logo'],'financial_start'=>$post['financial_start'],'time_zone'=>$post['time_zone'],'date_format'=>$post['date_format'],'percentage_position'=>$post['percentage_position'],'number_format'=>$post['number_format'],'default_currency_id'=>$post['default_currency_id'],'default_language_id'=>$post['default_language_id'],'default_payment_mode'=>$post['default_payment_mode'],'default_pettycash_acct'=>$post['default_pettycash_acct'],'default_cash_acct'=>$post['default_cash_acct']);
	//print_r($fieldnames); exit;
	$res=$callConfig->insertRecord(TPREFIX.TBL_CONFIG,$fieldnames);
	return $res;
	}
	function updateConfig($post)
	{
	global $callConfig;
	$fieldnames=array('name'=>$post['name'],'liscense_number'=>$post['liscense_number'],'admin_email'=>$post['admin_email'],'tax_number'=>$post['tax_number'],'phone_number'=>$post['phone_number'],'billing_address'=>$post['billing_address'],'contact_address'=>$post['contact_address'],'enabled'=>$post['enabled'],'display_logo'=>$post['display_logo'],'financial_start'=>$post['financial_start'],'time_zone'=>$post['time_zone'],'date_format'=>$post['date_format'],'percentage_position'=>$post['percentage_position'],'number_format'=>$post['number_format'],'default_currency_id'=>$post['default_currency_id'],'default_language_id'=>$post['default_language_id'],'default_payment_mode'=>$post['default_payment_mode'],'default_pettycash_acct'=>$post['default_pettycash_acct'],'default_cash_acct'=>$post['default_cash_acct']);
	//print_r($fieldnames); exit;
	$res=$callConfig->updateRecord(TPREFIX.TBL_CONFIG,$fieldnames,'id',$post['id']);
	return $res;
	}
	function insertCurrencies($post){
	global $callConfig;
	//print_r($post); exit;
	$fieldnames=array('name'=>$post['name'],'code'=>$post['code'],'rate'=>$post['rate'],'enabled'=>$post['enabled'],'precisions'=>$post['precision'],'symbol'=>$post['symbol'],'symbol_first'=>$post['symbol_first'],'decimal_mark'=>$post['decimal_mark'],'thousands_separator'=>$post['thousands_separator']);
	//print_r($fieldnames); exit;
	//$callConfig->headerRedirect("index.php");
	$res=$callConfig->insertRecord(TPREFIX.TBL_CURRENCIES,$fieldnames);
	return $res;
	}
	function updateCurrencies($post){
	global $callConfig;
	$fieldnames=array('name'=>$post['name'],'code'=>$post['code'],'rate'=>$post['rate'],'enabled'=>$post['enabled'],'precisions'=>$post['precision'],'symbol'=>$post['symbol'],'symbol_first'=>$post['symbol_first'],'decimal_mark'=>$post['decimal_mark'],'thousands_separator'=>$post['thousands_separator']);
	//print_r($fieldnames); exit;
	$res=$callConfig->updateRecord(TPREFIX.TBL_CURRENCIES,$fieldnames,'id',$post['id']);
	return $res;
	}
	//Array ( [company_id] => 1 [user_id] => 2 [name] => ravi T [email] => ravishankar.tangudu@gmail.com [ tax_number] => 7878 [phone] => 08919889697 [address] => G3, SSD Heights [website] => www.locahost.com [currency_code] => INR [enabled] => 1 [submit] => Add )
	function insertCustomer($post){
	global $callConfig;
	//print_r($post); exit;
	$fieldnames=array('company_id'=>$post['company_id'],'name'=>$post['name'],'email'=>$post['email'],'tax_number'=>$post['tax_number'],'phone'=>$post['phone'],'address'=>$post['address'],'website'=>$post['website'],'currency_code'=>$post['currency_code'],'enabled'=>$post['enabled']);
	//print_r($fieldnames); exit;
	//Array ( [company_id] => 1 [user_id] => 2 [name] => ravi T [email] => ravishankar.tangudu@gmail.com [ tax_number] => 7878 [phone] => 08919889697 [address] => G3, SSD Heights [website] => www.locahost.com [currency_code] => INR [enabled] => 1 [submit] => Add )
	$res=$callConfig->insertRecord(TPREFIX.TBL_CUSTOMERS,$fieldnames);
	return $res;
	}
	function updateCustomer($post){
	global $callConfig;
	$fieldnames=array('company_id'=>$post['company_id'],'name'=>$post['name'],'email'=>$post['email'],'tax_number'=>$post['tax_number'],'phone'=>$post['phone'],'address'=>$post['address'],'website'=>$post['website'],'currency_code'=>$post['currency_code'],'enabled'=>$post['enabled']);
	//print_r($fieldnames); exit;
	$res=$callConfig->updateRecord(TPREFIX.TBL_CUSTOMERS,$fieldnames,'id',$post['id']);
	return $res;
	}
	function insertMediables($post){
	global $callConfig;
	//print_r($post); exit;
	$fieldnames=array('media_id'=>$post['media_id'],'mediable_type'=>$post['mediable_type'],'mediable_id'=>$post['mediable_id'],'tag'=>$post['tag'],'order'=>$post['order']);
	//print_r($fieldnames); exit;
	//Array ( [media_id] => 1 [mediable_type] => ffe [mediable_id] => 3 [tag] => www [order] => 3 [submit] => Add )
	$res=$callConfig->insertRecord(TPREFIX.TBL_MEDIABLES,$fieldnames);
	return $res;
	}
	function updateMediables($post){
	global $callConfig;
	$fieldnames=array('company_id'=>$post['company_id'],'name'=>$post['name'],'email'=>$post['email'],'tax_number'=>$post['tax_number'],'phone'=>$post['phone'],'address'=>$post['address'],'website'=>$post['website'],'currency_code'=>$post['currency_code'],'enabled'=>$post['enabled']);
	//print_r($fieldnames); exit;
	$res=$callConfig->updateRecord(TPREFIX.TBL_MEDIABLES,$fieldnames,'id',$post['id']);
	return $res;
	}
	function insertNotifications($post){
	global $callConfig;
	//print_r($post); exit;
	$fieldnames=array('type'=>$post['type'],'notifiable_id'=>$post['notifiable_id'],'notifiable_type'=>$post['notifiable_type'],'data'=>$post['data']);
	//print_r($fieldnames); exit;
	//Array ( [type] => type1 [notifiable_id] => 1 [notifiable_type] => Ntypee [data] => 2019-11-12 [submit] => Add )
	$res=$callConfig->insertRecord(TPREFIX.TBL_NOTIFICATIONS,$fieldnames);
	return $res;
	}
	function updateNotifications($post){
	global $callConfig;
	$fieldnames=array('type'=>$post['type'],'notifiable_id'=>$post['notifiable_id'],'notifiable_type'=>$post['notifiable_type'],'data'=>$post['data']);
	//print_r($fieldnames); exit;
	$res=$callConfig->updateRecord(TPREFIX.TBL_NOTIFICATIONS,$fieldnames,'id',$post['id']);
	return $res;
	}
	function insertItems($post){
	global $callConfig;
	//print_r($post); exit;
	$fieldnames=array('company_id'=>$post['company_id'],'name'=>$post['name'],'sku'=>$post['sku'],'description'=>$post['description'],'sale_price'=>$post['sale_price'],'purchase_price'=>$post['purchase_price'],'quantity'=>$post['quantity'],'category_id'=>$post['category_id'],'tax_id'=>$post['tax_id'],'item_image'=>$post['company_id'],'name'=>$post['name'],'sku'=>$post['sku'],'description'=>$post['description'],'sale_price'=>$post['sale_price'],'purchase_price'=>$post['item_image'],'item_image2'=>$post['item_image2'],'item_image3'=>$post['item_image3'],'enabled'=>$post['enabled']);
	//print_r($fieldnames); exit;
	//Array ( [type] => type1 [notifiable_id] => 1 [notifiable_type] => Ntypee [data] => 2019-11-12 [submit] => Add )
	$res=$callConfig->insertRecord(TPREFIX.TBL_ITEMS,$fieldnames);
	return $res;
	}
	function updateItems($post){
	global $callConfig;
	$fieldnames=array('company_id'=>$post['company_id'],'name'=>$post['name'],'sku'=>$post['sku'],'description'=>$post['description'],'sale_price'=>$post['sale_price'],'purchase_price'=>$post['purchase_price'],'quantity'=>$post['quantity'],'category_id'=>$post['category_id'],'tax_id'=>$post['tax_id'],'item_image'=>$post['company_id'],'name'=>$post['name'],'sku'=>$post['sku'],'description'=>$post['description'],'sale_price'=>$post['sale_price'],'purchase_price'=>$post['item_image'],'item_image2'=>$post['item_image2'],'item_image3'=>$post['item_image3'],'enabled'=>$post['enabled']);
	//print_r($fieldnames); exit;
	$res=$callConfig->updateRecord(TPREFIX.TBL_ITEMS,$fieldnames,'id',$post['id']);
	return $res;
	}
	function insertBillTables($post){
		global $callConfig;
		//print_r($post); exit;
		$fieldnames=array('company_id'=>$post['company_id'],'bill_number'=>$post['invoice_no'],'order_number'=>$post['invoice_no'],'bill_status_code'=>'SSS','billed_at'=>$post['invoice_date'],'due_at'=>$post['invoice_date'],'amount'=>$post['total'],'currency_code'=>'INR','vendor_name'=>$post['vendor_name'],'vendor_email'=>$post['vendor_email'],'vendor_tax_number'=>$post['vendor_tax_number'],'vendor_phone'=>$post['vendor_phone'],'paid_amount'=>$post['paid']);
		//print_r($fieldnames); echo "ijul";
		$bill_id=$callConfig->insertRecord(TPREFIX.TBL_BILLS,$fieldnames);
		 $bill_id = 2;  
		 foreach ($post['item_name'] as $key => $item) {
		 	$itemfieldnames=array('company_id'=>$post['company_id'],'bill_id'=>$bill_id,'item_id'=>$key+1,'name'=>$item,'sku'=>$key+1,'quantity'=>$post['item_qty'][$key],'price'=>$post['item_rate'][$key],'total'=>$post['item_qty'][$key]*$post['item_rate'][$key],'tax'=>'0','description'=>$post['item_description'][$key]);
		 		//print_r($itemfieldnames); exit;
		 	$billitem_id=$callConfig->insertRecord(TPREFIX.TBL_BILLITEMS,$itemfieldnames);
		 }
		 return $bill_id;
	}
	function insertBillTables11($post){
		global $callConfig;
		print_r($post); exit;
		$fieldnames=array('company_id'=>$post['company_id'],'invoice_number'=>$post['invoice_number'],'invoice_serial_number'=>$post['invoice_serial_number'],'order_at'=>$post['order_at'],'order_number'=>$post['order_number'],'invoiced_at'=>$post['invoiced_at'],'customer_id'=>$post['customer_id'],'amount'=>$post['grandtotaol'],'currency_code'=>'INR','customer_name'=>$post['customer_name'],'customer_email'=>$post['customer_email'],'customer_tax_number'=>$post['customer_tax_number'],'customer_phone'=>$post['customer_phone'],'customer_address'=>$post['customer_address'],'customer_ship_adderss'=>$post['customer_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'notes'=>$post['notes']);
		//print_r($fieldnames); echo "ijul";productCode
		 $bill_id=$callConfig->insertRecord(TPREFIX.TBL_BILLS,$fieldnames);
		// $bill_id = 2;  
		 foreach ($post['productCode'] as $key => $item) {
		 	$itemfieldnames=array('company_id'=>$post['company_id'],'invoice_id'=>$bill_id,'item_id'=>$item['productCode'],'name'=>$item['productName'],'sku'=>$item['productCode'],'quantity'=>$item['quantity'],'price'=>$item['price'],'total'=>$item['price']+$item['tax_amount'],'tax'=>'0','description'=>$post['item_description']);
		 		//print_r($itemfieldnames); exit;
		 	$billitem_id=$callConfig->insertRecord(TPREFIX.TBL_BILLITEMS,$itemfieldnames);
		 }
		 return $bill_id;
	}
	/*function biillData($bill_id){
		global $callConfig;
		$whr="id = ".$bill_id;
		$query=$callConfig->selectQuery(TPREFIX.TBL_BILLS,'*',$whr,'','','');
		$bill = $callConfig->getRow($query);
		$where="bill_id = ".$bill_id;
		$query=$callConfig->selectQuery(TPREFIX.TBL_BILLITEMS,'*',$whr,'','','');
		$bill_item = $callConfig->getAllRows($query);
		$bill['items'] = $bill_item;
		return 4BI
	}*/
	function listofTables($db){
		global $callConfig;
		//$query="show TABLES";
		$query="SELECT table_name FROM information_schema.tables WHERE table_schema ='".$db."'";
		//echo $query; exit;
		$dataa = $callConfig->getAllRows($query);
		//print_r($dataa);
		$tables = array();
		foreach ($dataa as  $data) {
			$tables[] = $data->table_name ;
		}
		return$tables;
	}
	function listofTableFields($table, $content=''){
		global $callConfig;
		$additional_attributes = 0;
		$query="show COLUMNS from ".$table;
		//echo $query;
		$dataa = $callConfig->getAllRows($query);
		//print_r($dataa); exit;
		//echo "<pre>";print_r($content); exit;
		$fields = array(); $add=10;
		foreach ($dataa as  $data) {
			//echo $content[$data->Field]['display_postion'];
			//print_r($data); exit;
			$fields[$data->Field]['display_postion'] = isset($content[$data->Field]['display_postion']) ? $content[$data->Field]['display_postion'] : $add;
			$fields[$data->Field]['display_list'] = isset($content[$data->Field]['display_list'])? $content[$data->Field]['display_list'] : 1;
			$fields[$data->Field]['lable'] = isset($content[$data->Field]['lable'])? $content[$data->Field]['lable'] : ucwords(str_replace('_',' ',$data->Field));
			$fields[$data->Field]['mandatory'] = isset($content[$data->Field]['mandatory'])? $content[$data->Field]['mandatory'] : 2;
			$fields[$data->Field]['insert_display_list'] = isset($content[$data->Field]['insert_display_list'])? $content[$data->Field]['insert_display_list'] : 2;
			$fields[$data->Field]['type'] = isset($content[$data->Field]['type'])? $content[$data->Field]['type'] : 'text';
			$fields[$data->Field]['insert_view_width'] =  isset($content[$data->Field]['insert_view_width'])? $content[$data->Field]['insert_view_width'] : '6';
			$fields[$data->Field]['insert_view_wizard'] =  isset($content[$data->Field]['insert_view_wizard'])? $content[$data->Field]['insert_view_wizard'] : '1';
			$fields[$data->Field]['add_custom_field'] =  isset($content[$data->Field]['add_custom_field'])? $content[$data->Field]['add_custom_field'] : '2';
			$fields[$data->Field]['accordions'] =  isset($content[$data->Field]['accordions']) ? $content[$data->Field]['accordions'] : 'N';
			$add= $add+10;
			if($data->Field == "additional_attributes"){
				$additional_attributes =1;
			}
		}
		if($additional_attributes == 1 && $content != ""){
			foreach ($content as  $conkey=>$con) {
				if(isset($con['add_exter']) && $con['add_exter'] == 1 ){
					//echo "<pre>";print_r($con); exit;
					$fields[$conkey] = $con;
				}
			}
		}
		return json_encode($fields);	
	}
	function testTableInsert($data, $filename){
		global $callConfig;
		//echo "user"; exit;
		$query=$callConfig->selectQuery(TBL_EDIDUMP,'*','name="'.$filename.'"','','','');
		//echo $query; //exit;
		$edi_dump = $callConfig->getRow($query);
		$edi_dump = (array)$edi_dump;
		$query1=$callConfig->selectQuery(TBL_EDIMASTER,'*','EDI_Name="'.$filename.'"','','','');
		//echo $query; //exit;
		$edi_master = $callConfig->getRow($query1);
		$edi_master = (array)$edi_master;
		//print_r($edi_dump);// exit;
		$count = count($data[0]);
		$fstr = " (";
		for($i=1; $i<= $count; $i++){
			$fstr.= "field".$i.",";
		}
		$fstr = trim($fstr,",");
		$fstr.= ") ";
		$rstr = "";
		//print_r($data);
		 foreach ($data as  $feilddata) {
		 	$rstr.= "(";
		 	$i =1;
		 	foreach ($feilddata as  $value) {
		 		$key ='field'.$i.'_datatype';
		 		if ($edi_dump[$key] == 'date') { 
		 			$value = $this->dateformate($edi_master['EDI_Date_Formater'],$value);
		 			
		 		}
		 		$rstr.="'".$value."',";
		 		$i++;
		 	}
		 	$rstr = trim($rstr,",");
		 	$rstr.= "),";
		 }
		 $rstr =  trim($rstr,",");
		 //echo $fstr;
		 $query = "INSERT INTO test ".$fstr. "VALUES" . $rstr .";";
		 //echo $query; exit;
		 $dataa = $callConfig->executeQuery($query);
		 return $dataa;
	}
	function dateformate($str,$value){
		$sty= strpos($str,'yyyy');
		$std= strpos($str,'dd');
		$stm= strpos($str,'mm');
		$value = substr($value,$sty,4).'-'.substr($value,$stm,2).'-'.substr($value,$std,2);
		return $value;
	}
	function getINqueryData ($tablename,$fields,$where,$ids) {
		global $callConfig;
		//echo $tablename; exit;
		$fields = ($fields == '') ? '*' : $fields;
		//echo $fields; exit;
		$query=$callConfig->selectIN($tablename,$fields,$where,$ids);
		//echo $tablename."//".$query;  exit;
		return $callConfig->getAllRows($query);
		
	}
	function getStatusSeq($ids){
		global $callConfig;
		$order = "sequence ASC";
		$query=$callConfig->selectIN(TBL_STATUS,'*','name_value',$ids,$order);
		return $callConfig->getAllRows($query);
	}
	function maacInvoicenumber($company_id){
		global $callConfig;
		$query=	"SELECT max(`invoice_serial_number`) as number FROM `isac_invoices` where `company_id` =".$company_id;
		//echo $query; //exit;
		return $callConfig->getRow($query);
	}
	function reSetPassword($data){
		global $callConfig;
		//echo "raviw"; exit;
		
		$whr ="user_email='".$data['email']."' and user_password='".$callConfig->passwordEncrypt($data['old_password'])."' and is_active ='0'";
		$query=$callConfig->selectQuery(TBL_USERS,'*',$whr,'','','');
		//echo $query; //exit;
		$row=  $callConfig->getRow($query);
		//print_r($data); exit;
		if($row->id !=""){
			$data['hdn_user_id'] = $row->id;
			$res = $this->changepassword($data);
			$fieldnames=array('reset_password'=>1);
			//print_r($fieldnames); exit;
			$whrcon = "user_email ='".$data['email']."'";
			$res3=$callConfig->updateRecord(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,$fieldnames,'','',$whrcon);

		}else{
			$res['error'] = '6009';
		}
		return $res;
	}
	function changepassword($post){
		global $callConfig;
		//print_r($post);
		$fieldnames=array('`user_password`'=>$callConfig->passwordEncrypt($post['new_password']));
		//print_r($fieldnames); exit;
		$res=$callConfig->updateRecord(TBL_USERS,$fieldnames,'id',$post['hdn_user_id']);
		return $res;
	}
	function getDyanamicafterData($table_name, $filed_name){
		global $callConfig;
		//echo "data"; exit;
		$filed_name = $filed_name."_after";
		$whr = "table_name = '".$table_name."' AND field_name = '".$filed_name."'";
		$query=$callConfig->selectQuery(TBL_DISPLAYATTRIBUTES_TYPE,'*',$whr,'','','');
		$data = $callConfig->getRow($query);
		$array ['lable'] = $data->display;
		$array ['value'] = $data->content;
		$array ['width'] = $data->value;
		$type = explode('#', $data->type);
		$array ['type'] = $type[0];
		$array ['display'] = $type[1];
		//print_r($array);
		//echo $query; exit;
		return $array;
	}
	function getDyanamicReportSelectData($report_id, $filed_name){
		global $callConfig;
		//echo "data"; exit;
		$whr = "report_id= '".$report_id."' AND field_name = '".$filed_name."'";
		$query=$callConfig->selectQuery(TBL_REPORT_FILTERS,'*',$whr,'','','');
		//echo $query; exit;
		$data = $callConfig->getRow($query);
		$display = $data->display_field;
		$value = $data->value_field;
		//print_r($data); exit;
		$array['filter_type'] = $data->filter_type;
		if($data->filter_type != 'static'){
			//echo $data->content; exit;
			//echo urldecode($data->content); exit;
			$custom_sql = $callConfig->getAllRows(urldecode($data->content));
			// echo "<pre>"; print_r($custom_sql);
			foreach ($custom_sql as $custom){
				//print_r($custom);
				 // echo $custom->NULL."### ".$custom->ALL_Groups;echo "<br>";
				$array1[$custom->NULL] = $custom->ALL_Groups;
				//print_r($array1);
			}
			$array['res'] = $array1;
			//print_r($array);
		}else{
			$array['res'] = json_decode($data->content,1);
		}
		//print_r($array); exit;
		return $array;
	}
	function getDyanamicSelectData($table_name, $filed_name){
		global $callConfig;
		//echo "data"; exit;
		$whr = "application_table_name= '".$table_name."' AND field_name = '".$filed_name."'";
		$query=$callConfig->selectQuery(TBL_DISPLAYATTRIBUTES_TYPE,'*',$whr,'','','');
		//echo $query; exit;
		$data = $callConfig->getRow($query);
		$display = $data->display_field;
		$value = $data->value_field;
		//print_r($data); exit;
		$array['type'] = $data->attribute_type;
		if($data->attribute_type == 'custom_sql'){
			//echo $data->content; exit;
			$qquery = urldecode($data->content);
			if(strpos($qquery,'WHERE')){
				$cwhr = "AND company_id=".$data->company_id." ORDER BY";
			}else{
				$cwhr = "WHERE company_id=".$data->company_id." ORDER BY";
			}
			$qquery = str_replace('ORDER BY', $cwhr, $qquery);
			$custom_sql = $callConfig->getAllRows($qquery);
			//print_r($custom_sql);
			foreach ($custom_sql as $custom){
				//print_r($custom);
				$array1[$custom->$value] = $custom->$display;
			}
			$array['res'] = $array1;
			//print_r($array);
		}else{
			$array['res'] = json_decode($data->content,1);
		}
		//print_r($array); exit;
		return $array;
		
	}
	function listofTableFieldsByUsedTable($table,$db){
		global $callConfig;
		$query="SELECT COLUMN_NAME  FROM INFORMATION_SCHEMA.COLUMNS   WHERE TABLE_SCHEMA = '".$db."' AND TABLE_NAME = '".$table."'";
		//echo $query;
		$datas = $callConfig->getAllRows($query);
		foreach ($datas as $key => $da) {
			//print_r($da);
			$data[]=  $da->COLUMN_NAME;
		}
		return $data;	
	}
	function deleterows($table_name,$whr){
		global $callConfig;
		//echo "delete";
		$data = $callConfig->deleteRecord($table_name,$whr);
		return $data;
	}
	function toGetNotigficationList($user_id){
		global $callConfig;
		//echo $user_id;
		$whr = "user_id = '".$user_id."' AND read_status = '0'";
		$query=$callConfig->selectQuery(TBL_NOTIFICATON_USER,'*',$whr,'','','');
		$data = $callConfig->getAllRows($query);
		//print_r($data);
		foreach ($data as $value) {
			$where = "id = '".$value->notification_id."'";
			$query1=$callConfig->selectQuery(TBL_NOTIFICATIONS,'*',$where,'','','');
			$notificatondata = $callConfig->getRow($query1);	
			$return = array();
			$return['id'] = $value->id;
			$return['Notification_Type'] = $value->notification_type;
			$return['Notification_Message'] = $notificatondata->notification_message;
			$return['Notification_DateTime'] = $notificatondata->notification_datetime;
			$notidetails = json_decode($notificatondata->notification_details,1);
			//print_r($notidetails);
			$return['option'] = $notidetails['option'];
			$return['option_id'] = $notidetails['id'];
			$res[] = $return;
		}
		return  $res;
	}
	function displayrolepermission($role_id){
		global $callConfig;
		$whr = "role_id = '".$role_id."'";
		$query=$callConfig->selectQuery(TBL_ROLE_PERMISSIONS,'*',$whr,'','','');
		$pemissions = $callConfig->getAllRows($query);
		foreach($pemissions as $pemission){
			$table_name = $pemission->table_name;
			$data[$table_name]['permission_view'] = $pemission->permission_view;
			$data[$table_name]['permission_insert'] = $pemission->permission_insert;
			$data[$table_name]['permission_update'] = $pemission->permission_update;
			$data[$table_name]['permission_delete'] = $pemission->permission_delete;
		}
		
		$tables = $this->listofTables();
		//print_r($tables);
		foreach ($tables as $table) {
			$data1[$table]['permission_view'] = isset($data[$table]['permission_view']) ? $data[$table]['permission_view'] : 0;
			$data1[$table]['permission_insert'] = isset($data[$table]['permission_insert']) ? $data[$table]['permission_insert'] : 0;
			$data1[$table]['permission_delete'] = isset($data[$table]['permission_delete']) ? $data[$table]['permission_delete'] : 0;
		}
		//print_r($data1);
		return $data1;
	}
	function toGetRolePermissions($table){
		global $callConfig;
		$whr = "table_name = '".$table."'";
		$query=$callConfig->selectQuery(TBL_ROLE_PERMISSIONS,'*',$whr,'','','');
		$pemissions = $callConfig->getAllRows($query);
		foreach($pemissions as $pemission){
			$role_id = $pemission->role_id;
			$data[$role_id]['permission_view'] = $pemission->permission_view;
			$data[$role_id]['permission_insert'] = $pemission->permission_insert;
			$data[$role_id]['permission_update'] = $pemission->permission_update;
			$data[$role_id]['permission_delete'] = $pemission->permission_delete;
		}
		$query=$callConfig->selectQuery(TBL_ROLES,'*','','','','');
		$roles = $callConfig->getAllRows($query);
		//print_r($roles);
		//print_r($tables);
		foreach ($roles as $role) {
			//print_r($role);
			$roleid = $role->id;
			$data1[$roleid]['permission_view'] = isset($data[$roleid]['permission_view']) ? $data[$roleid]['permission_view'] : 0;
			$data1[$roleid]['permission_insert'] = isset($data[$roleid]['permission_insert']) ? $data[$roleid]['permission_insert'] : 0;
			$data1[$roleid]['permission_update'] = isset($data[$roleid]['permission_update']) ? $data[$roleid]['permission_update'] : 0;
			$data1[$roleid]['permission_delete'] = isset($data[$roleid]['permission_delete']) ? $data[$roleid]['permission_delete'] : 0;
			$data1[$roleid]['role_name'] = $role->display_name;
		}
		//echo "<pre>"; print_r($data1);
		return $data1;
	}
	function toGetRoledata($user_id){
		global $callConfig;
		//echo "data"; exit;
		$whr = "id = '".$user_id."'";
		$query=$callConfig->selectQuery(TBL_USERS,'*',$whr,'','','');
		//echo $query; exit;
		$data = $callConfig->getRow($query);
		//echo "<pre>";print_r($data);
		$role_id = $data->role_id;
		$role_id = json_decode($role_id,1);
		$role_id = implode(',', $role_id);

		$query = "SELECT `table_name`, MIN(permission_view) as permission_list,MIN(permission_insert) as permission_insert ,MIN(permission_update) as permission_update,MIN(permission_delete) as permission_delete FROM role_permissions where role_id IN(".$role_id.") GROUP BY table_name";
		//echo $query; exit;
		$rolesdata = $callConfig->getAllRows($query);
		///echo "<pre>"; print_r($rolesdata); exit;
		foreach ($rolesdata as  $value) {
			$roles[$value->table_name]['permission_list'] = $value->permission_list;
			$roles[$value->table_name]['permission_insert'] = $value->permission_insert;
			$roles[$value->table_name]['permission_update'] = $value->permission_update;
			$roles[$value->table_name]['permission_delete'] = $value->permission_delete;
		}
		return $roles;
	}
	function toGetblanceInvoices($customer_id){
		global $callConfig;
		$query = "SELECT * FROM invoices WHERE due_amount != 0 AND customer_id = ".$customer_id;
		$data = $callConfig->getAllRows($query);
		return $data;
	}
	function toGetblancebill($customer_id){
		global $callConfig;
		$query = "SELECT * FROM ".TBL_BILLS." WHERE due_amount != 0 AND vendor_id = ".$customer_id;
		//echo $query; 
		$data = $callConfig->getAllRows($query);
		return $data;
	}
	
	function toInsertandupateinvoice($post){
		global $callConfig;
		//echo "hai";
		//print_r($post); //exit;
		$fieldnames=array('company_id'=>$post['company_id'],'amount'=>$post['total_amount'],'payment_method'=>$post['paytype'],'reference'=>$post['ref_number']);
		//print_r($fieldnames); exit;
		$receiptid = $callConfig->insertRecord(TBL_INVOICE_PAYMENTS,$fieldnames);
		//echo  $receiptid = 1; //exit;
		 foreach ($post['details'] as $key => $item) {
		 	$invoice_number = $item['invoice_number']== 'unallocated'? 0 : $item['invoice_number'];
		 	$itemfieldnames=array('receipt_id'=> $receiptid,'invoice_id'=> $invoice_number,'paid_amount'=>$item['paid']);
		 		//print_r($itemfieldnames); exit;
		 	$billitem_id=$callConfig->insertRecord(TBL_INVOICE_ALLOCATED,$itemfieldnames);
		 	if($item['invoice_number'] != 'unallocated'){
		 		$field = array('paid_amount'=>$item['paid'],'due_amount'=>$item['due']);
		 		$res=$callConfig->updateRecord(TBL_INVOICE,$field,'invoice_number',$item['invoice_number']);
		 	}
		 }	

	}
	function insertEstimatesTables($post){
		global $callConfig;
			//echo "<pre>";print_r($post); exit;
		$fieldnames=array('company_id'=>$post['company_id'],'estimate_number'=>$post['invoice_number'],'estimated_at'=>$post['invoiced_at'],'amount'=>$post['invoiced_at'],'amount'=>$post['grandtotaol'],'currency_code'=>'INR','customer_id'=>$post['customer_id'],'customer_name'=>$post['customer_name'],'customer_email'=>$post['customer_email'],'customer_tax_number'=>$post['customer_tax_number'],'customer_phone'=>$post['customer_phone'],'customer_address'=>$post['customer_address'],'customer_ship_adderss'=>$post['customer_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'sub_total'=>$post['subTotal'],'tax_amount'=>$post['taxRate']);
		//print_r($fieldnames); echo "ijul"; exit;
		//$es_id = 1;
		$es_id=$callConfig->insertRecord(TBL_ESTIMATES,$fieldnames);  
		 foreach ($post['productCode'] as $key => $item) {
		 	//echo $item."##".$key;// exit;
		 	$itemfieldnames=array('estimate_id'=>$es_id,'item_id'=>$item,'name'=>$item,'quantity'=>$post['quantity'][$key],'price'=>$post['price'][$key],'total'=>$post['amount'][$key],'tax'=>$post['ptax_amount'][$key],'description'=>$post['productName'][$key]);
		 	//print_r($itemfieldnames); exit;
		 	$item_id=$callConfig->insertRecord(TBL_ESTIMATE_ITEMS,$itemfieldnames);
		 	//$item_id = 5;
		 	$taxfield=array('estimate_id'=>$es_id,'estimate_item_id'=>$item_id,'tax_id'=>1,'value'=>$post['tax'][$key],'amount'=>$post['ptax_amount'][$key]);
		 	//print_r($taxfield); exit;
		 	$itax_id=$callConfig->insertRecord(TBL_ESTIMATE_TAXES,$taxfield);
		 }
		 return $es_id;
	}
	function insertdebitnoteTables($post){
		global $callConfig;
			//echo "<pre>";print_r($post); exit;
		$fieldnames=array('company_id'=>$post['company_id'],'estimate_number'=>$post['invoice_number'],'estimated_at'=>$post['invoiced_at'],'amount'=>$post['invoiced_at'],'amount'=>$post['grandtotaol'],'currency_code'=>'INR','customer_id'=>$post['customer_id'],'customer_name'=>$post['customer_name'],'customer_email'=>$post['customer_email'],'customer_tax_number'=>$post['customer_tax_number'],'customer_phone'=>$post['customer_phone'],'customer_address'=>$post['customer_address'],'customer_ship_adderss'=>$post['customer_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'sub_total'=>$post['subTotal'],'tax_amount'=>$post['taxRate']);
		//print_r($fieldnames); echo "ijul"; exit;
		//$es_id = 1;
		$es_id=$callConfig->insertRecord(TBL_DEBIT_NOTE,$fieldnames);  
		 foreach ($post['productCode'] as $key => $item) {
		 	//echo $item."##".$key;// exit;
		 	$itemfieldnames=array('estimate_id'=>$es_id,'item_id'=>$item,'name'=>$item,'quantity'=>$post['quantity'][$key],'price'=>$post['price'][$key],'total'=>$post['amount'][$key],'tax'=>$post['ptax_amount'][$key],'description'=>$post['productName'][$key]);
		 	//print_r($itemfieldnames); exit;
		 	$item_id=$callConfig->insertRecord(TBL_DEBIT_NOTE_ITEMS,$itemfieldnames);
		 	//$item_id = 5;
		 	$taxfield=array('estimate_id'=>$es_id,'estimate_item_id'=>$item_id,'tax_id'=>1,'value'=>$post['tax'][$key],'amount'=>$post['ptax_amount'][$key]);
		 	//print_r($taxfield); exit;
		 	$itax_id=$callConfig->insertRecord(TBL_DEBIT_NOTE_ITEM_TAXES,$taxfield);
		 }
		 return $es_id;
	}
	function insertcreaditnoteTables($post){
		global $callConfig;
			//echo "<pre>";print_r($post); exit;
		$fieldnames=array('company_id'=>$post['company_id'],'estimate_number'=>$post['invoice_number'],'estimated_at'=>$post['invoiced_at'],'amount'=>$post['invoiced_at'],'amount'=>$post['grandtotaol'],'currency_code'=>'INR','customer_id'=>$post['customer_id'],'customer_name'=>$post['customer_name'],'customer_email'=>$post['customer_email'],'customer_tax_number'=>$post['customer_tax_number'],'customer_phone'=>$post['customer_phone'],'customer_address'=>$post['customer_address'],'customer_ship_adderss'=>$post['customer_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'sub_total'=>$post['subTotal'],'tax_amount'=>$post['taxRate']);
		//print_r($fieldnames); echo "ijul"; exit;
		//$es_id = 1;
		$es_id=$callConfig->insertRecord(TBL_DEBIT_NOTE,$fieldnames);  
		 foreach ($post['productCode'] as $key => $item) {
		 	//echo $item."##".$key;// exit;
		 	$itemfieldnames=array('estimate_id'=>$es_id,'item_id'=>$item,'name'=>$item,'quantity'=>$post['quantity'][$key],'price'=>$post['price'][$key],'total'=>$post['amount'][$key],'tax'=>$post['ptax_amount'][$key],'description'=>$post['productName'][$key]);
		 	//print_r($itemfieldnames); exit;
		 	$item_id=$callConfig->insertRecord(TBL_DEBIT_NOTE_ITEMS,$itemfieldnames);
		 	//$item_id = 5;
		 	$taxfield=array('estimate_id'=>$es_id,'estimate_item_id'=>$item_id,'tax_id'=>1,'value'=>$post['tax'][$key],'amount'=>$post['ptax_amount'][$key]);
		 	//print_r($taxfield); exit;
		 	$itax_id=$callConfig->insertRecord(TBL_DEBIT_NOTE_ITEM_TAXES,$taxfield);
		 }
		 return $es_id;
	}
	function insertquotationTables($post){
		global $callConfig;
			//echo "<pre>";print_r($post); exit;
		$fieldnames=array('company_id'=>$post['company_id'],'estimate_number'=>$post['invoice_number'],'estimated_at'=>$post['invoiced_at'],'amount'=>$post['invoiced_at'],'amount'=>$post['grandtotaol'],'currency_code'=>'INR','customer_id'=>$post['customer_id'],'customer_name'=>$post['customer_name'],'customer_email'=>$post['customer_email'],'customer_tax_number'=>$post['customer_tax_number'],'customer_phone'=>$post['customer_phone'],'customer_address'=>$post['customer_address'],'customer_ship_adderss'=>$post['customer_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'sub_total'=>$post['subTotal'],'tax_amount'=>$post['taxRate']);
		//print_r($fieldnames); echo "ijul"; exit;
		//$es_id = 1;
		$es_id=$callConfig->insertRecord(TBL_QUOTATIONS,$fieldnames);  
		 foreach ($post['productCode'] as $key => $item) {
		 	//echo $item."##".$key;// exit;
		 	$itemfieldnames=array('estimate_id'=>$es_id,'item_id'=>$item,'name'=>$item,'quantity'=>$post['quantity'][$key],'price'=>$post['price'][$key],'total'=>$post['amount'][$key],'tax'=>$post['ptax_amount'][$key],'description'=>$post['productName'][$key]);
		 	//print_r($itemfieldnames); exit;
		 	$item_id=$callConfig->insertRecord(TBL_QUOTATIONS_ITEMS,$itemfieldnames);
		 	//$item_id = 5;
		 	$taxfield=array('estimate_id'=>$es_id,'estimate_item_id'=>$item_id,'tax_id'=>1,'value'=>$post['tax'][$key],'amount'=>$post['ptax_amount'][$key]);
		 	//print_r($taxfield); exit;
		 	$itax_id=$callConfig->insertRecord(TBL_QUOTATIONS_ITEM_TAXES,$taxfield);
		 }
		 return $es_id;
	}
	function insertpurchaseTables($post){
		global $callConfig;
			//echo "<pre>";print_r($post); exit;
		$fieldnames=array('company_id'=>$post['company_id'],'estimate_number'=>$post['invoice_number'],'estimated_at'=>$post['invoiced_at'],'amount'=>$post['invoiced_at'],'amount'=>$post['grandtotaol'],'currency_code'=>'INR','customer_id'=>$post['customer_id'],'customer_name'=>$post['customer_name'],'customer_email'=>$post['customer_email'],'customer_tax_number'=>$post['customer_tax_number'],'customer_phone'=>$post['customer_phone'],'customer_address'=>$post['customer_address'],'customer_ship_adderss'=>$post['customer_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'sub_total'=>$post['subTotal'],'tax_amount'=>$post['taxRate']);
		//print_r($fieldnames); echo "ijul"; exit;
		//$es_id = 1;
		$es_id=$callConfig->insertRecord(TBL_QUOTATIONS,$fieldnames);  
		 foreach ($post['productCode'] as $key => $item) {
		 	//echo $item."##".$key;// exit;
		 	$itemfieldnames=array('estimate_id'=>$es_id,'item_id'=>$item,'name'=>$item,'quantity'=>$post['quantity'][$key],'price'=>$post['price'][$key],'total'=>$post['amount'][$key],'tax'=>$post['ptax_amount'][$key],'description'=>$post['productName'][$key]);
		 	//print_r($itemfieldnames); exit;
		 	$item_id=$callConfig->insertRecord(TBL_QUOTATIONS_ITEMS,$itemfieldnames);
		 	//$item_id = 5;
		 	$taxfield=array('estimate_id'=>$es_id,'estimate_item_id'=>$item_id,'tax_id'=>1,'value'=>$post['tax'][$key],'amount'=>$post['ptax_amount'][$key]);
		 	//print_r($taxfield); exit;
		 	$itax_id=$callConfig->insertRecord(TBL_QUOTATIONS_ITEM_TAXES,$taxfield);
		 }
		 return $es_id;
	}
	/***
		To Get Email group Details with number emails  with user pics
	***/
	function getemailgroupdetails(){
		global $callConfig;
		//$whr = "table_name = '".$table."'";
		$query=$callConfig->selectQuery(TBL_EMAILGROUPS,'*','','','','');
		$emailgroup = $callConfig->getAllRows($query);
		foreach ($emailgroup as  $value) {
			$group = array();
			$group['id'] = $value->id;
			$group['name'] = $value->name;
			$group['description'] = $value->description;
			$group['start_date'] = $value->status_date;
			$group['status'] = $value->current_status;
			$group['owner_id'] = $value->owner;
			$user_ids[] = $value->owner;
			$whr = "email_groups_id = '".$value->id."'";
			$query1=$callConfig->selectQuery(TBL_EMAILGROUPSEMAIL,'id',$whr,'','','');
			//echo $query1; exit;
			$email = $callConfig->getAllRows($query1);
			$group['count'] = count($email);
			$whr1  =  "id = '".$value->owner."'";
			$query2=$callConfig->selectQuery(TBL_USERS,'first_name',$whr1,'','','');
			//echo $query2;
			$owner = $callConfig->getRow($query2);
			$group['owner'] = $owner->first_name;
			$groups[] = $group;
		}
		//echo "<pre>"; print_r($groups);
		return $groups;

	}
	function insertorupdatepermissions($post){
		global $callConfig;
		//echo "hai";
		//echo "<pre>";print_r($post);
		$role_id = $post['role'];
		$whr =  "role_id = '".$role_id."'";
		$query=$callConfig->selectQuery(TBL_ROLE_PERMISSIONS,'id,table_name',$whr,'','','');
		//echo $query;
		$tables = $callConfig->getAllRows($query);
		foreach ($tables as  $table) {
			$role_tables[] = $table->table_name;
			$roledata[$table->table_name] = $table->id;
		}
		//print_r($role_tables); exit;
		foreach ($post['data'] as $key => $data) {
			$id = $roledata[$key];
			$field=array('table_name'=>$key,'permission_view'=>$data['view'],'permission_insert'=>$data['insert'],'permission_update'=>$data['update'],'permission_delete'=>$data['delete'],'role_id'=>$role_id,'Current_Status'=>0);
			if(in_array($key, $role_tables)){ 
				//echo "updat# ".$key."<br>";
		 		$res=$callConfig->updateRecord(TBL_ROLE_PERMISSIONS,$field,'id',$id);
			}else{
				//echo "insert # ".$key."<br>";
		 		$res=$callConfig->insertRecord(TBL_ROLE_PERMISSIONS,$field);
			}
			
		}	
		//exit;
		return $res;
	}
	/***
		To Get  group  Email Details with number groups  
	***/
	function getgroupemaildetails($group_id){
		global $callConfig;
		//$group_id = 1;
		$query=$callConfig->selectQuery(TBL_EMAILGROUPS,'id,name','','','','');
		$emailgroup = $callConfig->getAllRows($query);
		foreach ($emailgroup as  $value) {
			$group[$value->id] = $value->name;
		}
		//print_r($group);
		$whr = "email_groups_id = '".$group_id."'";
		$query1=$callConfig->selectQuery(TBL_EMAILGROUPSEMAIL,'*',$whr,'','','');
		$emails = $callConfig->getAllRows($query1);
		//print_r($emails);
		foreach ($emails as $key => $value) {
			$whrs = "id = '".$value->email_id."'";
			$query3=$callConfig->selectQuery(TBL_EMAIL,'*',$whrs,'','','');
			$email_data = $callConfig->getRow($query3);
			//foreach ($emailgroup as  $email_data) {
				$data['id'] = $email_data->id;
				$data['name'] = $email_data->name;
				$data['email'] = $email_data->email;
				$data['status'] = $email_data->Current_Status;
				$whre= "email_id = '". $email_data->id."'";
				$query11=$callConfig->selectQuery(TBL_EMAILGROUPSEMAIL,'email_groups_id',$whre,'','','');
				//echo $query11;
				$email_grops_list = $callConfig->getAllRows($query11);
				$list_group = array();
				foreach ($email_grops_list as $key => $lisst) {
					$list_group[$lisst->email_groups_id] = $group[$lisst->email_groups_id];
				}
				$data['list'] = $list_group;
				$return [] = $data;
			//}
			
		} 
		//echo "<pre>"; print_r($return); exit;
		return $return;
	}
	function checkAndInsertapplicationpage($table, $company_id){
		global $callConfig;
		$whr="application_table_name='".$table."' AND company_id= '".$company_id."'" ;
		//echo $whr; exit;
		$query=$callConfig->selectQuery(TBL_APPLICATION_PAGES,'*',$whr,'','','');
		//echo $query; exit;
		$data = $callConfig->getRow($query);
		$res = $data->id;
		if($data->id ==''){
			//echo "rr";
			$field=array('company_id'=>$company_id,'application_table_name'=>$table,'list_page_name'=>strtoupper("COM_LIST_".$table),'insert_page_name'=>strtoupper("COM_INSERT_".$table),'edit_page_name'=>strtoupper("COM_EDIT_".$table),'view_page_name'=>strtoupper("COM_view_".$table),'list_page_type'=>1,'insert_page_type'=>1,'edit_page_type'=>1,'view_page_type'=>1,'Current_Status'=>0,'is_active'=>'0');
			$res=$callConfig->insertRecord(TBL_APPLICATION_PAGES,$field);
		}
		//print_r($data);
		return $res;
	} 
	function viewdattributetype($table, $field, $db){
		global $callConfig;
		$whr="application_table_name='".$table."' AND field_name='".$field."'";
		//echo $whr; exit;
		$query=$callConfig->selectQuery(TBL_DISPLAYATTRIBUTES_TYPE,'*',$whr,'','','');
	//	echo $query; exit;
		$data = $callConfig->getRow($query);
		//echo $data->application_table_name;
		$table_str = urldecode($data->content);
		$table_str = trim($table_str);
		$table_name_data = explode('FROM', $table_str);
		$table_name_data = explode(' ',trim($table_name_data['1']));
		//print_r($table_name_data);
		$table_name_field = $table_name_data['0'];
		//print_r($table_name_data);
		$table_name_field = $table_name_data['0'];
		$data->list = $this->listofTableFieldsByUsedTable($table_name_field,$db);

		//echo "<pre>"; print_r($data); exit;
		return $data;
	}
	function togetprimarykey($table,$db){
		global $callConfig;
		$query = "SELECT k.column_name, k.table_schema, k.table_name, k.constraint_name FROM information_schema.table_constraints t JOIN information_schema.key_column_usage k where k.constraint_schema = t.constraint_schema and k.table_schema = t.table_schema and k.table_name = t.table_name and t.constraint_type='PRIMARY KEY' AND k.constraint_name = 'PRIMARY' AND t.table_schema='".$db."' AND t.table_name='".$table."'";
		$data = $callConfig->getAllRows($query);
		//print_r($data);
		//$return = array();
		foreach ($data as  $value) {
			$return [] = $value->column_name;
		}
		return $return;
	}
	function togetdatabasepaimairy($tablename, $db, $data){
		global $callConfig;
		$data =  explode(",", urldecode($data));
		$whr = "";
		foreach ($data as $value) {
			$key = explode(":", $value);
			$whr.= $key[0]."='".$key[1]."' AND ";
		}
		$whr = trim($whr, "AND ");
		//echo $whr; exit;
		$query=$callConfig->selectQuery($tablename,'*',$whr,'','','');
		return $callConfig->getRow($query);
	}
	function updaterowbasepimarydata($fieldnames,$table,$rid){
		global $callConfig;
		//echo "<br>".$rid;
		$data =  explode(",", urldecode($rid));
		$whr = "";
		foreach ($data as $value) {
			$key = explode(":", $value);
			$whr.= $key[0]."='".$key[1]."' AND ";
		}
		$whr = trim($whr, "AND ");
		$res=$callConfig->updateRecord($table,$fieldnames,'','',$whr);
		return $res;
	}
	function morefilednames($tables,$db){
		global $callConfig;
		$array = array();
		//print_r($tables);
		foreach ($tables as $table) {
			//echo $table. "||||".$db."<br>";
			$array[$table] = $this->listofTableFieldsByUsedTable($table,$db);
		}
		return $array;
	}
	function togetmastertablesdetais(){
		global $callConfig;
		$query=$callConfig->selectQuery(TBL_DISPLAYATTRIBUTES,'*','','application_table_name ASC','','');
		$lists = $callConfig->getAllRows($query);
		$query11=$callConfig->selectQuery(TBL_STATUS,'*','','','','');
		$statulists = $callConfig->getAllRows($query11);
		foreach ($statulists as  $statulist) {
			$stas[ $statulist->name_value] = $statulist->name;
			//$stasv[$statulist->id] = $statulist->name_value;
			$stasq[ $statulist->name_value] = $statulist->sequence;
		}
		//print_r($stasv);
		foreach($lists as $list){
			$array = array(); $arr1 = array();
			$array['application_table_name'] = $list->application_table_name;
			$array['help_text'] = $list->help_text;
			$whr =  "table_name = '".$list->application_table_name."'";
			$query1=$callConfig->selectQuery(TBL_STATUSPROGRES,'status',$whr,'','','');
			//echo $query1;
			$status =  $callConfig->getRow($query1);
			$status = explode(",", $status->status);

			//print_r($status);
			//SELECT count(*) as total , SUM(IF(`current_status` = 1 , 1,0)) as c1,SUM(IF(`current_status` = 2 , 1,0)) as ss FROM account_groups
			$str = 'SELECT ';
			foreach ($status as   $value) {
				$str.=" SUM(IF(`current_status` = ".$value." , 1,0)) as c".$value.", ";//.$value;
			}
			$str.="count(*) as total FROM ".$list->application_table_name;
			//echo $str;
			$val =  $callConfig->getRow($str);
			//print_r($val); exit;
			$arr3 ['status_name'] = "total";
			$arr3 ['count'] = $val->total;
			$arr1[] = $arr3;
			foreach ($status as   $value) {
				$arr ['status_name'] = $stas[$value];
				$vv = 'c'.$value;
				$arr ['count'] = $val->$vv;
				$arr1[$stasq[$value]] = $arr;
			}
			krsort($arr1);
			$array['status'] = $arr1;
			//echo "<pre>"; print_r($array); exit;
			$data[] = $array;
		}
		//echo "<pre>";print_r($data);
		return $data;
	}
	function togetreportdata($data){
		global $callConfig;
		//print_r($data);
		$whr =  "id = '".$data['report_id']."'";
		//echo $whr;
		$query=$callConfig->selectQuery(TBL_REPORT_ATTRIBUTES,'*',$whr,'','','');
		//echo $query; exit;
		$report_data =  $callConfig->getRow($query);
		//print_r($report_data);
		$report_query = urldecode($report_data->content);
		$attributes = json_decode($report_data->attributes,1);

		foreach ($attributes as $key => $att) {
			//print_r($value); exit;
			if($att['filter'] == 1){
				$filter['lable'] = $att['lable'];
				$filter['filter_type'] = $att['filter_type'];
				$filter['field'] =  $key;
				$filters[]=$filter;
			}
			if($att['display_list'] == 1){
				$dis[] =  $key." as ". str_replace(".", "_", $key);
			}
		}
		
		//$str = " WHERE ";
		foreach ($filters as  $filt) {
			//print_r($data);
			$field = str_replace(".", "_", $filt["field"]);
			//echo $data[$field];
			if ($data[$field]!="") {
				if($filt["filter_type"] =='text'){
					$str[]=$filt['field']."  LIKE '%".$data[$field]."%'";
				}
				if($filt["filter_type"] =='select'){
					$str[]=$filt['field']."  = '".$data[$field]."'";
				}
				if($filt["filter_type"] =='date_pick'){
					$str[]=$filt['field']."  < '". date("Y-m-d",strtotime($data[$field]))."'";
				}
			}
		}
		$strs = '';
		if(count($str)>0){
			$strs = " WHERE ".implode(' AND ', $str);
		}
		$cus_query =  $report_query.$strs;
		$dis_fields = implode(", ", $dis);
		//echo $dis_fields."<br>";
		$cus_query = str_replace("SELECT * FROM", "SELECT ".$dis_fields." FROM",$cus_query);
		//echo $cus_query;
		$lists = $callConfig->getAllRows($cus_query);
		return $lists;

	}
	function insertorupdatereportfilter($data){ 
		global $callConfig;
		//print_r($data); exit;

		$whr = "report_id='".$data['report_id']."' and field_name='".$data['field_name']."'";
		//echo $whr;
		$this->deleterows(TBL_REPORT_FILTERS,$whr);
		//exit;
		$res = $this->insertTablesdata($data, TBL_REPORT_FILTERS);

	}
	function getPurchaseRequisitionsData($id){
		global $callConfig;
		$where = "id = '".$id."'";	
		$query=$callConfig->selectQuery(TBL_PURCHASE_REQUISITIONS,'*',$where,'','','');
		$data = $callConfig->getRow($query);
		$pr['data'] = $data;
		//print_r($pr);
		//$pr = $pr->0;
		$whr = "pr_id = '".$id."'";	
		$pr['items'] = $this->getAllTablesData(TBL_PURCHASE_REQUISITIONS_ITEMS,'*',$whr,'');
		$pr['taxes'] = $this->getAllTablesData(TBL_PURCHASE_REQUISITIONS_ITEMS_TAXES,'*',$whr,'');
		return $pr;
	}
	function toGetInvoicetables(){ 
		global $callConfig;
		$configesponse = $this->getAllTablesData(TBL_DYNAMIC_MAPPING_TABLES,'*','','');
		foreach($configesponse AS $con){
			$table_list[$con->application_table_name] ['item']= $con->item_table;
			$table_list[$con->application_table_name] ['tax']= $con->tax_table;
			$table_list [$con->application_table_name] ['option'] = $con->option_url;
		}
	    //print_r($table_list); exit;
	 	return $table_list;
	}
	function togetitemvalues($item_d){
		global $callConfig;
		$whr="id='".$item_d."'" ;
		$query=$callConfig->selectQuery(TBL_ITEMS,'*',$whr,'','','');
		$data = $callConfig->getRow($query);
		return $data;
	}
	function togetitemwadayrate($item_d, $company_id){
		global $callConfig;
		$whrstp1="company_id = '".$company_id."'  AND item_id= '".$item_d."'";
		$query1=$callConfig->selectQuery('item_wa_balances','day_rate',$whrstp1,'balance_date DESC','0','1');
		//echo $query1;
		$data = $callConfig->getRow($query1);
	//print_r($data);
		return $data->day_rate;
	}
	function toGetInvoicetabledata($table){ 
		global $callConfig;

		$where = "application_table_name = '".$table."'";
		//echo $where; exit;	
		$query = $callConfig->selectQuery(TBL_DYNAMIC_MAPPING_TABLES,'*',$where,'','','');
		//echo $query; exit;
		$data = $callConfig->getRow($query);
		$table_list ['item']= $data->item_table;
		$table_list['tax']= $data->tax_table;
		$table_list['option'] = $data->option_url;
	    //print_r($table_list); exit;
	 	return $table_list;
	}
	function toGetApplicationSerialNumber($table_name){
		global $callConfig;
	    $whr="application_table_name = '".$table_name."'";
		$query=$callConfig->selectQuery(TBL_APPLICATION_SERIAL_NUMBERS,'*',$whr,'','','');
		$res = $callConfig->getRow($query);
		$le = $res->doc_no_length;
		$var = "%0".$le."d";
		$data = $res->pre_fix.sprintf($var,$res->serial_number+1).$res->post_fix;
		$update = $callConfig->updateRecord(TBL_APPLICATION_SERIAL_NUMBERS,array('serial_number' =>$res->serial_number+1),'id',$res->id);
		return trim($data);
	}
	function insertInvoiceType($post, $table, $type=''){
		global $callConfig;
		//echo "<pre>"; print_r($post); exit;
		$fieldarray  = $this->toGetInvoicetables();
		if($post['old_place'] == $post['place_of_supply']){ 
			$tax_flag = "S";
		}else{
			$tax_flag = "I";
		}
		$status = $post['status_values'] != '' ? $post['status_values'] : ST_DRAFT ;
		$status_main = $status;
		if($type == 'update' && $status > ST_DRAFT){
			$sarray  = array('comments' => $post['comments'], 'response_type' => $status, 'user_id' => $post['updated_by'], 'response_date' => date("Y-m-d"),'table' => $post['table_name'],'hid' => $post['hid'],'amount'=>$post['grandtotal'],'status_old' => $post['status_old_value'],'company_id'=>$post['company_id']);
			$status = $this->ToCheckStatusApprovalLog($sarray);
		}
		$invoiced_at = str_replace('/"', '-', $post['transaction_date']);
       	$invoiced_at =  date('Y-m-d', strtotime($invoiced_at));
       	$doc_ref_date = str_replace('/"', '-', $post['doc_ref_date']);
        $doc_ref_date =  date('Y-m-d', strtotime($doc_ref_date));
        $due_date = str_replace('/"', '-', $post['due_date']);
        $due_date =  date('Y-m-d', strtotime($due_date)); 
		$fieldnames=array('company_id'=>$post['company_id'],'transaction_number'=>$post['transaction_number'],'transaction_date'=>$invoiced_at,'currency_id'=>$post['currency_id'],'currency_rate'=>$post['currency_rate'],'customer_id'=>$post['customer_id'],'customer_name'=>$post['customer_name'],'customer_email'=>$post['customer_email'],'customer_tax_number'=>$post['customer_tax_number'],'customer_phone'=>$post['customer_phone'],'customer_address'=>$post['customer_address'],'customer_ship_adderss'=>$post['customer_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'notes'=>$post['notes'],'amount'=>$post['subTotal'],'tax_amount'=>$post['taxRate'], 'discount_type'=>$post['discount_type'],'discount_value'=>$post['discount_value'],'discount_amount'=>$post['discount_amount'], 'total_amount'=>$post['grandtotal'], 'current_status'=> $status, 'ref_id'=>$post['ref_id'], 'ref_no'=>$post['ref_no'], 'ref_date'=>date("Y-m-d",strtotime($post['ref_date'])),'ref_type'=>$post['ref_date'],'originator'=>$post['originator'],'printing_notes'=>$post['printing_notes'],'type'=>$post['type'],'tax_type'=>$post['tax_type'],'shipping_tax'=>$post['shipping_tax'],'shipping_amount'=>$post['shipping_amount'],'shipping_tax_amount'=>$post['shipping_tax_amount'],'printing_notes'=>$post['printing_notes'],'tax_state'=>$tax_flag,'opex_cpex'=>$post['opex_cpex'],'due_date'=>$due_date);
		//print_r($fieldnames); exit;
		//$email = $this->emailtemplate($email_data);
		$fieldnames['cost_center_id'] = $post['cost_center_id'];
		if(isset($post['additional_attributes'])){
			$fieldnames['additional_attributes'] = $post['additional_attributes'];
		}
		if($table == 'requisitions'){
			$fieldnames['originator'] = $post['customer_id'];		
		}
		if($table == 'stock_requests'){
			$fieldnames['location_id'] = $post['location_id'];
			$fieldnames['transfer_location_id'] = $post['transfer_location_id'];
		}
		/*else{
			$post['type'] = '';
			$post['opex_cpex'] = '';
		}*/
		if($table == 'bills'){
			$fieldnames['doc_ref_no'] = $post['doc_ref_no'];
			$fieldnames['doc_ref_date'] = $doc_ref_date;
		}
		//echo "<pre>"; print_r($fieldnames); //exit;		
		if($type == 'update'){
			$fieldnames['updated_date'] = $post['updated_date'];
			$fieldnames['updated_by'] = $post['updated_by'];
			$insert_data=$callConfig->updateRecord($table,$fieldnames,'id',$post['hid']);
			$itemids = explode(',', $post['hid_itemds']);
			$insert_id = $post['hid'];
			$fieldnames['transaction_type'] = $table;
			$fieldnames['transaction_id'] = $insert_id;
			$res = $this->addAndUpdateBulkApprovalTransaction($fieldnames,'update');
		 }else{
		 	$fieldnames['created_by'] = $post['created_by'];
			$fieldnames['created_date'] = $post['created_date'];
			//print_r($fieldnames); exit;
		 	$transation = $this->toGetApplicationSerialNumber($table);
		 	$fieldnames['transaction_number'] = $transation;
		 	//print_r($fieldnames); //exit;
		 	$insert_data=$callConfig->insertRecord($table,$fieldnames);
			$insert_id = $insert_data['LASTID'];
			$fieldnames['transaction_type'] = $table;
			$fieldnames['transaction_id'] = $insert_id;
			$res = $this->addAndUpdateBulkApprovalTransaction($fieldnames,'insert');
		 }
		 //$insert_id  = 17;
		//print_r($insert_data); echo $insert_id; exit;
		// echo "RAVi".count($post['file_add']) ;
		// print_r($post['file_add']); echo $insert_id; //exit;
		 if(count($post['file_add'])>0){
		 	 $file_upload = $this->multiplefileupload($post['file_add']);
		}//exit;
		$email_data = array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_type'=>$table, 'transaction_date'=>$invoiced_at, 'amount'=>$post['subTotal'], 'tax_amount'=>$post['taxRate'] , 'discount_amount'=>$post['discount_amount'] , 'total_amount'=>$post['grandtotal'] ,'action_label'=>'Submit for Approval', 'option'=>$fieldarray[$table]['option'] ,'Rid'=>$insert_id,'email_template'=>1,'status_old_value'=>$post['status_old_value'],'status'=>$status_main,'updated_by'=>$post['updated_by'],'originator'=>$fieldnames['originator'],'opex_cpex'=>$post['opex_cpex'],'category_type'=>$post['type'],'cost_center'=>$post['cost_center']);
		if($insert_id > 0){
			//echo "data"; exit;
		foreach ($post['productCode'] as $key => $item) {
			$litemids[] = $item;
			$item_doc_ref_date = $post['doc_ref_date'][$key];
			if($item_doc_ref_date !=''){
				$item_doc_ref_date = str_replace('/"', '-',$item_doc_ref_date);
    			$item_doc_ref_date =  date('Y-m-d', strtotime($item_doc_ref_date));
        	}
			$req_date = str_replace('/"', '-', $post['req_date'][$key]);
       		$req_date =  date('Y-m-d', strtotime($req_date));
       		$mainitem = $this->togetitemvalues($item);
       		$day_rate = $this->togetitemwadayrate($item, $post['company_id']);
		 	$itemfieldnames=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'item_id'=>$item,'item_name'=>$post['item_name'][$key],'description'=>$post['productName'][$key],'purchase_uom'=>$post['uom'][$key],'item_quantity'=>$post['quantity'][$key],'item_price'=>$post['price'][$key],'item_amount'=>$post['amount'][$key],'tax_amount'=>$post['ptax_amount'][$key],'total_amount'=>$post['amount'][$key],'discount_amount'=>$post['pdiscount_amount'][$key],'req_date'=>$req_date,'account_code'=>$post['purchase_account'][$key],'item_wa_cost'=>$day_rate,'item_st_cost'=>$mainitem->purchase_price);
		 	if($table == 'petty_cash' ){
		 		$itemfieldnames['doc_ref_no'] = $post['doc_ref_no'][$key];
				$itemfieldnames['doc_ref_date'] = $item_doc_ref_date;
		 	}
		 	$taxfield=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'transaction_item_id'=>$item,'tax_id'=>$post['tax'][$key],'amount'=>$post['p_amount'][$key],'tax_amount'=>$post['ptax_amount'][$key],'total_amount' =>$post['amount'][$key],'tax_type' =>$tax_flag, 'tax_value'=>$post['tax'][$key]);
		 	if($table == 'stock_adjustments'){
		 		$reason = explode('$', $post['respon'][$key]);
		 		$itemfieldnames['reason_id'] = $reason[0];
		 		$reason_value = $reason[1];
		 	}
		 	if($table == 'sales_returns' || $table == 'delivery_orders' ){

		 		$len = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'transaction_date' => $fieldnames['transaction_date'],'item_id' =>$item,'document_no' =>$insert_id,'document_type' =>$table,'document_ref_no' =>$fieldnames['transaction_number'] ,'in_qty' =>$post['quantity'][$key],'in_value' =>$post['amount'][$key],'item_in_value' =>$mainitem->purchase_price*$post['quantity'][$key],'reason_id'=>$reason_value,'out_qty' =>$post['quantity'][$key],'out_value' =>$post['amount'][$key],'sale_price'=>$post['price'][$key],'sale_value'=>$post['amount'][$key],'item_wa_cost'=>$day_rate,'item_st_cost'=>$mainitem->purchase_price);
		 	}else{
		 		$len = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'transaction_date' => $fieldnames['transaction_date'] , 'item_id' =>$item,'document_no' =>$insert_id,'document_type' =>$table,'document_ref_no' =>$fieldnames['transaction_number'] ,'in_qty' =>$post['quantity'][$key],'in_value' =>$post['amount'][$key],'item_in_value' =>$mainitem->purchase_price*$post['quantity'][$key],'reason_id'=>$reason_value,'out_qty' =>$post['quantity'][$key],'out_value' =>$post['amount'][$key],'sale_price'=>$post['price'][$key],'sale_value'=>$post['amount'][$key],'item_wa_cost'=>$day_rate,'item_st_cost'=>$mainitem->purchase_price);
		 	}
		 	if($type == 'update'){
			 	$where = "transaction_id = '".$insert_id."' AND item_id = '".$item."' AND is_active = 0";
			 	$itemquery=$callConfig->selectQuery($fieldarray[$table]['item'],'*',$where,'','','');
				$data = $callConfig->getRow($itemquery);
				$len['in_qty'] = $len['in_qty']-$data->item_quantity;
				$len['outy_qty'] = $len['outy_qty']-$data->item_quantity;
				$len['in_value'] = $len['in_value']-$data->item_amount;
				$len['out_value'] = $len['out_value']-$data->item_amount;
			}
		 	//$in_array = array('goods_receipt_notes','grn_returns','stock_requests','stock_transfers','stock_transfer_returns','delivery_orders','sales_returns','stock_adjustments','stock_take','stock_transfer_receipts','stock_issues');
		 	$out_array = array('grn_returns','stock_transfers','delivery_orders','stock_transfer_receipts','stock_issues');
		 	$in_array = array('goods_receipt_notes','stock_transfer_returns','sales_returns');
		 	$reason_array = array('stock_adjustments','stock_take');
		 	//echo "<pre>"; print_r($len); //exit;
		 	if(in_array($table, $in_array)){
		 		$len['out_qty'] = '0';
		 		$len['out_value'] = '0'; 
		 	}else if(in_array($table, $out_array)){
		 		$len['in_qty'] = '0';
		 		$len['in_value'] = '0'; 
		 	}else if(in_array($table, $reason_array)){
		 		if($reason_value == 0){
		 			$len['in_qty'] = '0';
		 			$len['in_value'] = '0'; 
		 		}else{
		 			$len['out_qty'] = '0';
		 			$len['out_value'] = '0'; 
		 		}
		 	}else{
		 		$len = array();
		 	}
            //echo "<pre>"; print_r($len); exit;
		 	if($type == 'update'){
				$itemfieldnames['approved_quantity'] = $post['quantity'][$key];
				$itemfieldnames['pending_qty'] = $post['quantity'][$key];
				if($data->description != $itemfieldnames['description'] || $data->purchase_uom != $itemfieldnames['purchase_uom']  || $data->item_quantity != $itemfieldnames['item_quantity'] || $data->item_price != $itemfieldnames['item_price'] || $data->approved_quantity != $itemfieldnames['approved_quantity'] ){
					if($data->id !=''){
						$res=$callConfig->updateRecord($fieldarray[$table]['item'],$itemfieldnames,'id',$data->id);
						if($data->item_quantity != $itemfieldnames['item_quantity'] || $data->item_price != $itemfieldnames['item_price'] ){
							
							$ddf = $this->invoiceUpdates($table,$len);

						}
						
					}else{
						$item_id=$callConfig->insertRecord($fieldarray[$table]['item'],$itemfieldnames);
						$ddf = $this->invoiceUpdates($table,$len);
					}
				}
				$where = "transaction_id = '".$insert_id."' AND transaction_item_id	= '".$item."' AND is_active = 0";
			 	$itemquery=$callConfig->selectQuery($fieldarray[$table]['tax'],'*',$where,'','','');
				$data_tax = $callConfig->getRow($itemquery);
				if(count($data_tax) > 0){
					if($data->tax_amount != $itemfieldnames['tax_amount']){
						$where = "transaction_id = '".$insert_id."' AND transaction_item_id = '".$item."' AND is_active = 0";
						$res=$callConfig->updateRecord($fieldarray[$table]['tax'],$taxfield,'','',$where);
					}
				}else{
						$itax_id=$callConfig->insertRecord($fieldarray[$table]['tax'],$taxfield);
				}
				if($post['ref_type']!=''){
					$wheredd = "transaction_id = '".$post['ref_id']."' AND item_id = '".$itemfieldnames['item_id']."' AND is_active = 0";
			 		$upquery=$callConfig->selectQuery($post['ref_type'],'*',$wheredd,'','','');
					$updata = $callConfig->getRow($upquery);
					$update_qty = $itemfieldnames['item_quantity'] - $post['quantity_old'][$key];
					$minsupdate = array('pending_qty' => $updata->pending_qty + $update_qty ,'transaction_type' => $updata->quotation_qty + $update_qty,'transaction_type' => $post['ref_type']);
					$res=$callConfig->updateRecord($fieldarray[$post['ref_type']]['item'],$minsupdate,'','',$wheredd);
				}
			}else{
			 	$item_id=$callConfig->insertRecord($fieldarray[$table]['item'],$itemfieldnames);
			 	if($post['ref_type']!=''){
					$minsupdate = array('pending_qty' => $post['quantity_old'][$key]-$itemfieldnames['item_quantity'],'transaction_qty' => $itemfieldnames['item_quantity'],'transaction_type' => $post['ref_type']);
					$wheredd = "transaction_id = '".$post['ref_id']."' AND item_id = '".$itemfieldnames['item_id']."' AND is_active = 0";
					$res=$callConfig->updateRecord($fieldarray[$post['ref_type']]['item'],$minsupdate,'','',$wheredd);
				}
				
				$ddf = $this->invoiceUpdates($table,$len);				
			 	//$item_id = 5;
			 	if($post['tax'][$key]!=0){
			 	//print_r($taxfield); exit;
			 		$itax_id=$callConfig->insertRecord($fieldarray[$table]['tax'],$taxfield);
			 	}
		 	}
		}
	//	print_r($itemids); exit; //echo ""
		 if($type == 'update' ){
		 	$diffid = array_diff($itemids,$litemids);
		 	/*foreach ($litemids as $lik) {
		 		if(!in_array($lik, $itemids)){
		 			$diffid[] = $lik;
		 		}
		 	}*/
		 	//print_r($diffid); exit;
		 	if(count($diffid) > 0){
			 	foreach ($diffid as  $value_id) {
			 		$taxfieldde=array('is_active'=>1);
			 		$wheredd = "transaction_id = '".$insert_id."' AND item_id = '".$value_id."' AND is_active = 0";
			 		$wheredd1 = "transaction_id = '".$insert_id."' AND transaction_item_id = '".$value_id."' AND is_active = 0";
			 		$res=$callConfig->updateRecord($fieldarray[$table]['item'],$taxfieldde,'','',$wheredd);
			 		$res=$callConfig->updateRecord($fieldarray[$table]['tax'],$taxfieldde,'','',$wheredd1);
			 		$len['in_qty'] = 0; $len['in_qty'] = 0;
			 		$ddf = $this->invoiceUpdates($table,$len);	
			 	}
		 	}
		 	
		 	//print_r($itemids); exit;
		 	if($status != ST_CANCEL && $status != ST_DRAFT){
		 		$iu = $this->insertandupdatedataapplicationtag($fieldnames,$insert_id,$fieldarray[$table]['option']);
		 	}
		 	if($status == ST_APPROVAL_PENDING){
		 		//ST_APPROVED
		 		$budget = array('table'=>$fieldarray[$table]['item'],'transaction_id'=>$insert_id,'cost_center'=>$fieldnames['cost_center_id'], 'company_id'=>$fieldnames['company_id'],'transaction_date' => $fieldnames['transaction_date']);
		 		$ireu = $this->UpdateBudgetBlance($budget);
		 	}
		 }
		}
		if($status > ST_DRAFT){
		 		//echo "data"; exit;
		 		$res =$this->toSendEmailNotificationsBaseDAO($email_data);
		 }
		 //print_r($insert_id); exit;
		
		 return $insert_data;
	}

	function invoiceUpdates($table,$len){
		global $callConfig;
		
		if(count($len)>1){
			//echo "ravi"; exit;
			/*if($table == 'stock_adjustments' && $len['reason_id'] == 0){
				$in_qty = "-".$len['in_qty'];
			}*/
			//$average_balance = $this->averagebalance($len);
			//echo "<pre>";print_r($len); exit;
			//print_r($len); //exit;
			//$len = array_merge($len,$average_balance);
			//print_r($len); exit;
			$GRN_loc_item_ledgerst = $this->insertorupdateitem_ledgerst($len);
			$GRN_loc_item_ledgerwa = $this->insertorupdateitem_ledgerwa($len);
			$GRN_loc_item_ledgerlifo = $this->insertorupdateitem_ledgerlifo($len);
			$GRN_loc_item_ledgerfifo = $this->insertorupdateitem_ledgerfifo($len);
			$GRN_loc_item_wa_balances = $this->insertoupdatelocitemwabalances($len);
			$GRN_loc_item_st_balances = $this->insertoupdatelocitemstbalances($len);
			$GRN_loc_item_wa_balances = $this->insertoupdateitemwabalances($len);
			$GRN_loc_item_st_balances = $this->insertoupdateitemstbalances($len);
			$GRN_loc_item_fifio = $this->insertorupdateitemfifo($len);
		}
	}
	function tocreatejounalvoucherbaseallcases($table_name,$ref_id,$type=''){
		global $callConfig;	
		$whr="id=".$ref_id;
		$query1=$callConfig->selectQuery($table_name,'*',$whr,'','','');
		//echo $query1;
		$row = $callConfig->getRow($query1);
		$row = (array)$row;
		$row['invoice_number'] = '';
		$row['invoice_at'] = $row['transaction_date'];
		$row['subtotal_debit'] = $row['amount'];
		$row['subtotal_credit'] = $row['amount'];
		$row['grandtotal_debit'] = $row['total_amount'];
		$row['grandtotal_credit'] = $row['total_amount'];
		$row['ref_type'] = $table_name;
		$row['ref_id'] = $ref_id;
		$row['ref_no'] = $row['transaction_number'];
		$row['ref_date'] = $row['transaction_date'];
		$row['transaction_number'] = "";
		$tables = $this->toGetInvoicetabledata($table_name);
		$whr2="transaction_id=".$ref_id." AND is_active = 0";
		$query2=$callConfig->selectQuery($tables['item'],'*',$whr2,'','','');
		
		$itemrows = $callConfig->getAllRows($query2);
		foreach ($itemrows as $key => $itemrow) {
			$accountcode[] = $itemrow->account_code;
			$accountcode[] = $itemrow->account_code;
			$whr3="account_code='".$itemrow->account_code."'";
			$query3=$callConfig->selectQuery('chart_of_accounts','*',$whr3,'','','');
			//echo $query3;
			$chartrow = $callConfig->getRow($query3);
			$account_name[]= $chartrow->account_name;
			$account_name[]= $chartrow->account_name;
			$description[]= $chartrow->description;
			$description[]= $chartrow->description;
			$location[]= $chartrow->description;
			$location[]= $chartrow->description;
			$debit[]= $itemrow->item_amount;
			$debit[]= '0';
			$credit[]= '0';
			$credit[]= $itemrow->item_amount;
			$name[] = $row['customer_id']."#C";
			$name[] = $row['customer_id']."#V";
		}
		$row['accountcode'] = $accountcode;
		$row['account_name'] = $account_name;
		$row['description'] = $description;
		$row['location'] = $location;
		$row['name'] = $name;
		$row['debit'] = $debit;
		$row['credit'] = $credit;
		//echo "<pre>"; print_r($row); exit;	
		$this->addjournalvouchers($row, $table_name, $type);
	}
	function addjournalvouchers($post, $table, $type=''){
		global $callConfig;
		//echo "hah";
		echo "<pre>"; print_r($post); exit;
		$fieldarray  = $this->toGetInvoicetables();
		if($post['old_place'] == $post['place_of_supply']){ 
			$tax_flag = "S";
		}else{
			$tax_flag = "I";
		}
		$status = $post['status_values'] != '' ? $post['status_values'] : ST_DRAFT ;
		$status_main = $status;
		if($type == 'update' && $status > ST_DRAFT){
			$sarray  = array('comments' => $post['comments'], 'response_type' => $status, 'user_id' => $post['updated_by'], 'response_date' => date("Y-m-d"),'table' => $post['table_name'],'hid' => $post['hid'],'amount'=>$post['grandtotal'],'status_old' => $post['status_old_value'],'company_id'=>$post['company_id']);
			$status = $this->ToCheckStatusApprovalLog($sarray);
		}

		$invoiced_at = str_replace('/"', '-', $post['invoiced_at']);
       	$invoiced_at =  date('Y-m-d', strtotime($invoiced_at));
       	$diff_amount = 0; $diff_credit = 0; $diff_debit=0;
       	if($post['grandtotal_credit'] != $post['grandtotal_debit']){
       		if($post['grandtotal_credit'] > $post['grandtotal_debit']){  
       			
       			$diff_amount = $post['grandtotal_credit'] - $post['grandtotal_debit'];
       			$post['grandtotal_debit'] = $post['grandtotal_credit'];
       			$diff_debit = $diff_amount;
       		}
       		if($post['grandtotal_credit'] < $post['grandtotal_debit']){  
       			
       			$diff_amount = $post['grandtotal_debit'] - $post['grandtotal_credit'];
       			$post['grandtotal_credit'] = $post['grandtotal_debit'];
       			//echo  $post['grandtotal_debit']."###".$post['grandtotal_credit']."&&&&".$diff_amount;
       			$diff_credit = $diff_amount;
       		}
       	}
       //	echo $diff_amount; exit;
		$fieldnames=array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_date'=>$invoiced_at,'currency_id'=>$post['currency_id'],'currency_rate'=>$post['currency_rate'],'customer_id'=>$post['customer_id'],'customer_name'=>$post['customer_name'],'customer_email'=>$post['customer_email'],'customer_tax_number'=>$post['customer_tax_number'],'customer_phone'=>$post['customer_phone'],'customer_address'=>$post['customer_address'],'customer_ship_adderss'=>$post['customer_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'notes'=>$post['notes'],'amount'=>$post['subtotal_debit'],'tax_amount'=>$post['grandtotal_debit']-$post['subtotal_debit'], 'discount_type'=>$post['discount_type'],'discount_value'=>$post['discount_value'],'discount_amount'=>$post['discount_amount'], 'total_amount'=>$post['grandtotal_debit'], 'current_status'=> $status, 'ref_id'=>$post['ref_id'], 'ref_no'=>$post['ref_no'], 'ref_date'=>date("Y-m-d",strtotime($post['ref_date'])),'ref_type'=>$post['ref_date'],'originator'=>$post['originator'],'printing_notes'=>$post['printing_notes'],'type'=>$post['type'],'tax_type'=>$post['tax_type'],'shipping_tax'=>$post['shipping_tax'],'shipping_amount'=>$post['shipping_amount'],'shipping_tax_amount'=>$post['shipping_tax_amount'],'printing_notes'=>$post['printing_notes'],'tax_state'=>$tax_flag,'opex_cpex'=>$post['opex_cpex'],'due_date'=>$due_date,'debit_amount'=>$post['subtotal_debit'], 'credit_amount'=>$post['subtotal_credit'],'credit_tax'=>$post['grandtotal_credit'] - $post['subtotal_credit'],'debit_tax'=>$post['grandtotal_debit']-$post['subtotal_debit']);
		$fieldnames['cost_center_id'] = $post['cost_center'];	
		if($type == 'update'){
			$fieldnames['updated_date'] = $post['updated_date'];
			$fieldnames['updated_by'] = $post['updated_by'];
			$insert_data=$callConfig->updateRecord($table,$fieldnames,'id',$post['hid']);
			$itemids = explode(',', $post['hid_itemds']);
			$insert_id = $post['hid'];
			$fieldnames['transaction_type'] = $table;
			$fieldnames['transaction_id'] = $insert_id;
			$res = $this->addAndUpdateBulkApprovalTransaction($fieldnames,'update');
		 }else{
		 	$fieldnames['created_by'] = $post['created_by'];
			$fieldnames['created_date'] = $post['created_date'];
			echo "<pre>";print_r($fieldnames); exit;
		 	$transation = $this->toGetApplicationSerialNumber($table);
		 	$fieldnames['transaction_number'] = $transation;
		 	//print_r($fieldnames); //exit;
		 	$insert_data=$callConfig->insertRecord($table,$fieldnames);
			$insert_id = $insert_data['LASTID'];
			$fieldnames['transaction_type'] = $table;
			$fieldnames['transaction_id'] = $insert_id;
			//$res = $this->addAndUpdateBulkApprovalTransaction($fieldnames,'insert');
		 }
		//print_r($post['file_add']); echo $insert_id; exit;
		 if(count($post['file_add'])>0){
		 	 $file_upload = $this->multiplefileupload($post['file_add']);
		}//exit;
		$email_data = array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_type'=>$table, 'transaction_date'=>$invoiced_at, 'amount'=>$post['subTotal'], 'tax_amount'=>$post['taxRate'] , 'discount_amount'=>$post['discount_amount'] , 'total_amount'=>$post['grandtotal'] ,'action_label'=>'Submit for Approval', 'option'=>$fieldarray[$table]['option'] ,'Rid'=>$insert_id,'email_template'=>1,'status_old_value'=>$post['status_old_value'],'status'=>$status_main,'updated_by'=>$post['updated_by'],'originator'=>$fieldnames['originator'],'opex_cpex'=>$post['opex_cpex'],'category_type'=>$post['type'],'cost_center'=>$post['cost_center']);
		if($insert_id > 0){
			//echo "data"; exit;
		foreach ($post['accountcode'] as $key => $item) {
			///echo $item; exit;
			if($post['debit'][$key] !=0 ){
				$item_price = $post['debit'][$key];
			}else{
				$item_price = $post['credit'][$key];
			}
			$litemids[] = $item; 
			$names = explode('#',$post['name'][$key] );
		 	$itemfieldnames=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'item_id'=>$item,'item_name'=>$post['account_name'][$key],'description'=>$post['description'][$key],'purchase_uom'=>'','item_quantity'=>'1','item_price'=>$item_price,'item_amount'=>$item_price,'tax_amount'=>$post['taxes_amount'][$key],'total_amount'=>$item_price,'account_code'=>$post['account_code'][$key],'debit_amount'=>$post['debit'][$key],'credit_amount'=>$post['credit'][$key],'location'=>$post['location'][$key],'name_id'=>$names['0'],'name_type'=>$names['1']);

		 $genralledger[] = array('company_id'=>$post['company_id'],'account_code'=>$post['account_code'][$key],'account_type'=>$post['account_type'][$key],'voucher_type'=>$post['description'][$key],'voucher_id'=>$insert_id,'voucher_date'=>$invoiced_at,'cost_center_id'=>$post['cost_center'],'location_id'=>$post['location'][$key],'department_id'=>$post['department_id'],'debit_amount'=>$post['debit'][$key],'credit_amount'=>$post['credit'][$key],'balance_amount'=>'0','transaction_type'=>$fieldnames['ref_type'],'transaction_id'=>$fieldnames['ref_id'],'transaction_ref_no'=>$fieldnames['ref_no'],'transaction_date'=>$fieldnames['ref_date']);
		 	//echo "<pre>"; print_r($genralledger); exit;
		 	$taxfield=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'transaction_item_id'=>$item,'tax_id'=>$post['taxes'][$key],'amount'=>$item_price,'tax_amount'=>$post['taxes_amount'][$key],'total_amount' =>$item_price);
		 	//echo "<pre>"; print_r($itemfieldnames);print_r($taxfield); exit;
		 	if($type == 'update'){
			 	$where = "transaction_id = '".$insert_id."' AND item_id = '".$item."' AND is_active = 0";
			 	$itemquery=$callConfig->selectQuery($fieldarray[$table]['item'],'*',$where,'','','');
				$data = $callConfig->getRow($itemquery);
				//if($table == 'requisitions'){
					$itemfieldnames['approved_quantity'] = $post['quantity'][$key];
					$itemfieldnames['pending_qty'] = $post['quantity'][$key];
				//}
				//print_r($itemfieldnames); exit;
				if($data->description != $itemfieldnames['description'] || $data->purchase_uom != $itemfieldnames['purchase_uom']  || $data->item_quantity != $itemfieldnames['item_quantity'] || $data->item_price != $itemfieldnames['item_price'] || $data->approved_quantity != $itemfieldnames['approved_quantity'] ){
					if($data->id !=''){
						$res=$callConfig->updateRecord($fieldarray[$table]['item'],$itemfieldnames,'id',$data->id);
						
					}else{
						$item_id=$callConfig->insertRecord($fieldarray[$table]['item'],$itemfieldnames);
					}
				}
				$where = "transaction_id = '".$insert_id."' AND transaction_item_id	= '".$item."' AND is_active = 0";
			 	$itemquery=$callConfig->selectQuery($fieldarray[$table]['tax'],'*',$where,'','','');
				$data_tax = $callConfig->getRow($itemquery);
				if(count($data_tax) > 0){
					if($data->tax_amount != $itemfieldnames['tax_amount']){
						$where = "transaction_id = '".$insert_id."' AND transaction_item_id = '".$item."' AND is_active = 0";
						$res=$callConfig->updateRecord($fieldarray[$table]['tax'],$taxfield,'','',$where);
					}
				}else{
						$itax_id=$callConfig->insertRecord($fieldarray[$table]['tax'],$taxfield);
				}
				if($post['ref_type']!=''){
					$wheredd = "transaction_id = '".$post['ref_id']."' AND item_id = '".$itemfieldnames['item_id']."' AND is_active = 0";
			 		$upquery=$callConfig->selectQuery($post['ref_type'],'*',$wheredd,'','','');
					$updata = $callConfig->getRow($upquery);
					$update_qty = $itemfieldnames['item_quantity'] - $post['quantity_old'][$key];
					$minsupdate = array('pending_qty' => $updata->pending_qty + $update_qty ,'transaction_type' => $updata->quotation_qty + $update_qty,'transaction_type' => $post['ref_type']);
					$res=$callConfig->updateRecord($fieldarray[$post['ref_type']]['item'],$minsupdate,'','',$wheredd);
				}
			}else{
			 	$item_id=$callConfig->insertRecord($fieldarray[$table]['item'],$itemfieldnames);
			 	if($post['ref_type']!=''){
					$minsupdate = array('pending_qty' => $post['quantity_old'][$key]-$itemfieldnames['item_quantity'],'transaction_qty' => $itemfieldnames['item_quantity'],'transaction_type' => $post['ref_type']);
					$wheredd = "transaction_id = '".$post['ref_id']."' AND item_id = '".$itemfieldnames['item_id']."' AND is_active = 0";
					$res=$callConfig->updateRecord($fieldarray[$post['ref_type']]['item'],$minsupdate,'','',$wheredd);
				}
			 	if($post['taxes'][$key]!=0){
			 		$itax_id=$callConfig->insertRecord($fieldarray[$table]['tax'],$taxfield);
			 	}
		 	}
		 }
		 if($type == 'update' ){
		 	$diffid = array_diff($itemids,$litemids);
		 	if(count($diffid) > 0){
			 	foreach ($diffid as  $value_id) {
			 		$taxfieldde=array('is_active'=>1);
			 		$wheredd = "transaction_id = '".$insert_id."' AND item_id = '".$value_id."' AND is_active = 0";
			 		$wheredd1 = "transaction_id = '".$insert_id."' AND transaction_item_id = '".$value_id."' AND is_active = 0";
			 		$res=$callConfig->updateRecord($fieldarray[$table]['item'],$taxfieldde,'','',$wheredd);
			 		$res=$callConfig->updateRecord($fieldarray[$table]['tax'],$taxfieldde,'','',$wheredd1);
			 	}
		 	}
		 }
		}
		/*if($diff_amount !=0){
			$itemfieldnames=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'item_id'=>$post['diff_acount'],'item_name'=>'','description'=>'','purchase_uom'=>'','item_quantity'=>'1','item_price'=>$diff_amount,'item_amount'=>$diff_amount,'tax_amount'=>0,'total_amount'=>$diff_amount,'account_code'=>$post['diif_account_code'],'debit_amount'=>$diff_debit,'credit_amount'=>$diff_credit,'location'=>$post['location'][$key],'name_id'=>$names['0'],'name_type'=>$names['1']);
			$item_id=$callConfig->insertRecord($fieldarray[$table]['item'],$itemfieldnames);
		}
		if($status > ST_DRAFT){
		 		//echo "data"; exit;
		 		$res =$this->toSendEmailNotificationsBaseDAO($email_data);
		 }
		
		$ledger = $this->toinsertgenralledger();*/
		 return $insert_data;
	}


	function insertorupdateitem_ledgerst($post){
		global $callConfig;
		//echo "<pre>"; print_r($post);
		$whr="location_id='".$post['location_id']."' AND item_id = '".$post['item_id']."' AND document_type='".$post['document_type']."' AND document_no='".$post['document_no']."'";
		$query1=$callConfig->selectQuery('item_ledger_st','*',$whr,'','','');
		//echo $query1;
		$row = $callConfig->getRow($query1);
		//print_r($row); //exit;
		if($row->id !=''){
		   // echo "ravi for"; //exit;
		    $insert = array('document_date' =>$post['transaction_date'],'in_qty' =>$row->in_qty+$post['in_qty'] ,'in_value' =>$row->in_value+$post['in_value'],'out_qty' => $row->out_qty+$post['out_qty'], 'out_value' => $row->out_value+$post['out_value'],'total_cost'=> $row->item_cost *( ($row->in_qty+$post['in_qty']) + ($row->out_qty+$post['out_qty'])));
			$insert_data=$callConfig->updateRecord('item_ledger_st',$insert,'id',$row->id); 
		}else{ //exit;
    		if($post['out_qty']!=0 || $post['in_qty'] !=0){
    		  // echo "ravi2"; //exit; 
    		$insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'] ,'document_ref_no' =>$post['document_ref_no'] ,'document_date' =>$post['transaction_date'],'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'item_cost' => $post['item_st_cost'],'total_cost'=> $post['item_st_cost']*($post['in_qty']+$post['out_qty']),'out_qty' => $post['out_qty'], 'out_value' => $post['out_value']);
    		$res=$callConfig->insertRecord('item_ledger_st',$insert);
    		}
		}
		return $res;
	}
	function insertorupdateitem_ledgerwa($post){
		global $callConfig;
		//echo "<pre>"; print_r($post);
		$whr="location_id='".$post['location_id']."' AND item_id = '".$post['item_id']."' AND document_type='".$post['document_type']."' AND document_no='".$post['document_no']."'";
		$query1=$callConfig->selectQuery('item_ledger_wa','*',$whr,'','','');
		//echo $query1;
		$row = $callConfig->getRow($query1);
		//print_r($row); //exit;
		if($row->id !=''){
		   // echo "ravi for"; //exit;
		    $insert = array('document_date' =>$post['transaction_date'],'in_qty' =>$row->in_qty+$post['in_qty'] ,'in_value' =>$row->in_value+$post['in_value'], 'out_qty' => $row->out_qty+$post['out_qty'], 'out_value' => $row->out_value+$post['out_value'] ,'total_cost'=> $row->item_cost *( ($row->in_qty+$post['in_qty']) + ($row->out_qty+$post['out_qty'])));
			$insert_data=$callConfig->updateRecord('item_ledger_wa',$insert,'id',$row->id); 
		}else{ //exit;
    		if($post['out_qty']!=0 || $post['in_qty'] !=0){
    		  // echo "ravi2"; //exit; 
    		$insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'] ,'document_ref_no' =>$post['document_ref_no'] ,'document_date' =>$post['transaction_date'],'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'], 'item_cost' => $post['item_wa_cost'],'total_cost'=> $post['item_wa_cost']*($post['in_qty']+$post['out_qty']),'out_qty' => $post['out_qty'], 'out_value' => $post['out_value']);
    		$res=$callConfig->insertRecord('item_ledger_wa',$insert);
    		}
		}
		return $res;
	}
	function itemledgerlifochange($post){
	    global $callConfig;
		$whr= "item_id = '".$post['item_id']."' AND document_type='".$post['document_type']."' AND document_no='".$post['document_no']."'";
		$query1=$callConfig->selectQuery('item_ledger_lifo','*',$whr,'','','');
		//echo $query1;
		$row = $callConfig->getRow($query1);
		if($row->id !=''){
			if($post['out_qty']!=0 ){
    		   $this->itemlifoupdates($post);
    		}else{
    		   $whr21="item_id = '".$post['item_id']."' AND document_type='".$post['document_type']."' AND document_no='".$post['document_no']."'";
		       $query21=$callConfig->selectQuery('item_lifo_balances','*',$whr21,'','',''); 
		       $row21 = $callConfig->getRow($query21);
		       $updatequery =  "UPDATE `item_lifo_balances` SET in_qty = ".$row->in_qty+$post['in_qty'].",closing_qty= ".$row->closing_qty+$post['out_qty'].", in_value = ".$row->in_value+$post['in_value']." WHERE id = '".$row->id."'";
	            $callConfig->executeQuery($updatequery);
    	    }
		}else{
		    if($post['out_qty']!=0 || $post['in_qty'] !=0){
    		  if($post['out_qty']!=0 ){
    		     $this->itemlifoupdateledge($post);
    		  }else{
    	    	$insert = array('company_id' =>$post['company_id'] , 'item_id' =>$post['item_id'] ,'balance_date' =>$post['transaction_date'] ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['in_value'],'day_rate' => $post['in_value']/$post['in_qty'],'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'],'closing_qty'=>$post['in_qty']);
    	        $res=$callConfig->insertRecord('item_lifo_balances',$insert);
    	        
    		  }
    		}
		}
		
	}
	function insertorupdateitem_ledgerlifo($post){
		global $callConfig;
		//echo "<pre>"; print_r($post);
		$whr="location_id='".$post['location_id']."' AND item_id = '".$post['item_id']."' AND document_type='".$post['document_type']."' AND document_no='".$post['document_no']."'";
		$query1=$callConfig->selectQuery('loc_item_ledger_lifo','*',$whr,'','','');
		//echo $query1;
		$row = $callConfig->getRow($query1);
		//print_r($row); //exit;
		if($row->id !=''){
		   // echo "ravi for"; //exit;
		    $insert = array('document_date' =>$post['transaction_date'],'in_qty' =>$row->in_qty+$post['in_qty'] ,'in_value' =>$row->in_value+$post['in_value'], 'out_qty' => $row->out_qty+$post['out_qty'], 'out_value' => $row->out_value+$post['out_value'],'total_cost'=> $row->item_cost *( ($row->in_qty+$post['in_qty']) + ($row->out_qty+$post['out_qty'])));
			$insert_data=$callConfig->updateRecord('loc_item_ledger_lifo',$insert,'id',$row->id);
			if($post['out_qty']!=0 ){
    		   $this->itemlifoupdateledge($post);
    		}else{
    		   $whr21="item_id = '".$post['item_id']."' AND document_type='".$post['document_type']."' AND document_no='".$post['document_no']."'";
		       $query21=$callConfig->selectQuery('item_lifo_balances','*',$whr21,'','',''); 
		       $row21 = $callConfig->getRow($query21);
		       $updatequery =  "UPDATE `loc_item_lifo_balances` SET in_qty = ".$row->in_qty+$post['in_qty'].",closing_qty= ".$row->closing_qty+$post['out_qty'].", in_value = ".$row->in_value+$post['in_value']." WHERE id = '".$row->id."'";
	            $callConfig->executeQuery($updatequery);
    		}
		}else{ //exit;
    		if($post['out_qty']!=0 || $post['in_qty'] !=0){
    		  if($post['out_qty']!=0 ){
    		     $this->itemlifoupdateledge($post);
    		  }else{
    		    $insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'] ,'document_ref_no' =>$post['document_ref_no'] ,'document_date' =>$post['transaction_date'],'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'item_cost' => $post['in_value']/$post['in_qty'],'total_cost'=> $post['in_value'],'out_qty' => $post['out_qty'], 'out_value' => $post['out_value']);
    	    	$res=$callConfig->insertRecord('item_ledger_lifo',$insert);
    	    	$insert = array('company_id' =>$post['company_id'] , 'item_id' =>$post['item_id'] ,'balance_date' =>$post['transaction_date'] ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['in_value'],'day_rate' => $post['in_value']/$post['in_qty'],'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'],'closing_qty'=>$post['in_qty']);
    	        $res=$callConfig->insertRecord('item_lifo_balances',$insert);
    	        $insert['location_id'] = $post['location_id'];
    	        $res=$callConfig->insertRecord('loc_item_lifo_balances',$insert);
    		  }
    		}
		}
		return $res;
	}
	function  itemlifoupdateledge($post){
	    global $callConfig;
	    $count = $post['out_qty'];
	    $itemcount = $post['out_qty'];
	    if($count > 0){
	        $locquery = "SELECT * FROM `loc_item_lifo_balances` WHERE item_id =".$post['item_id']." AND company_id=".$post['company_id']." AND location_id =".$post['location_id']." AND closing_qty >0 ORDER By balance_date DESC,id DESC";   
	        $query = "SELECT * FROM `item_lifo_balances` WHERE item_id =".$post['item_id']." AND company_id=".$post['company_id']." AND closing_qty >0 ORDER By balance_date DESC,id DESC";   
	    }else{
	         $locquery = "SELECT * FROM `loc_item_lifo_balances` WHERE item_id =".$post['item_id']." AND location_id =".$post['location_id']." AND company_id=".$post['company_id']." AND AND (closing_qty = 0 OR closing_qty!=in_qty) ORDER By balance_date,id DESC";
	         $query = "SELECT * FROM `item_lifo_balances` WHERE item_id =".$post['item_id']." AND company_id=".$post['company_id']." AND  (closing_qty = 0 OR closing_qty!=in_qty) ORDER By balance_date DESC,id DESC";
	    }
	   // echo "<br>"; echo $query;
	   // echo "<br>"; echo $locquery;
	    $rows = $callConfig->getAllRows($locquery);
	    $itemrows = $callConfig->getAllRows($query);
	    //echo "<pre>"; print_r($rows);
	    //echo "<pre>"; print_r($itemrows);
	    foreach($itemrows as $itemdata){
	       if($itemcount > 0){        
    	       if($itemdata->closing_qty < $itemcount){
    	           $itemremaning_count = $itemcount- $itemdata->closing_qty;
    	           $count = $itemdata->closing_qty;
    	       }else{
    	           $itemremaning_count =0;
    	       }
	       }else{
	           if($itemdata->closing_qty < -(-$itemcount)){
    	           $remaning_count = $itemcount+ $itemdata->closing_qty;
    	           $itemcount = "-".$itemdata->closing_qty;
    	       }else{
    	           $itemremaning_count =0;
    	       }
	       }
	       $cc = $itemdata->out_qty+$itemcount;
	       $ccc = $itemdata->closing_qty-$itemcount;
	       $updatequery121 =  "UPDATE `item_lifo_balances` SET out_qty = ".$cc.",closing_qty= ".$ccc." WHERE id = '".$itemdata->id."'";
	      // echo $updatequery121;
	       $callConfig->executeQuery($updatequery121);//exit
	       if($itemremaning_count == 0){ 
	           break;
	       }
	       $itemcount = $itemremaning_count;
	    }
	    foreach($rows as $row){
	        //echo $count;
	       if($count > 0){        
    	       if($row->closing_qty < $count){
    	           $remaning_count = $count- $row->closing_qty;
    	           $count = $row->closing_qty;
    	       }else{
    	           $remaning_count =0;
    	       }
	       }else{
	           if($row->closing_qty < -(-$count)){
    	           $remaning_count = $count+ $row->closing_qty;
    	           $count = "-".$row->closing_qty;
    	       }else{
    	           $remaning_count =0;
    	       }
	       }
	      // echo "<pre>"; print_r($post);
	       $cc = $row->out_qty+$count;
	       $ccc = $row->closing_qty-$count;
	       $updatequery =  "UPDATE loc_item_lifo_balances SET out_qty = ".$cc.",closing_qty= ".$ccc." WHERE id = '".$row->id."'";
	      // echo "<br>"; echo $updatequery;
	       $callConfig->executeQuery($updatequery);
	       $bdate = $post['transaction_date'];
	       $insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'] ,'document_ref_no' =>$post['document_ref_no'] ,'document_date' =>$post['transaction_date'],'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'item_cost' => $row->day_rate,'total_cost'=>$row->day_rate*$count ,'out_qty' => $count, 'out_value' => ($post['out_value']/$post['out_qty'])*$count);
    	   $res=$callConfig->insertRecord('item_ledger_lifo',$insert);
	       if($remaning_count == 0){ 
	           break;
	       }
	       $count = $remaning_count;
	    }//exit;
	    return $res;
	}
    function insertorupdateitem_ledgerfifo($post){
		global $callConfig;
		//echo "<pre>"; print_r($post);
		$whr="location_id='".$post['location_id']."' AND item_id = '".$post['item_id']."' AND document_type='".$post['document_type']."' AND document_no='".$post['document_no']."'";
		$query1=$callConfig->selectQuery('loc_item_ledger_fifo','*',$whr,'','','');
		//echo $query1;
		$row = $callConfig->getRow($query1);
		//print_r($row); //exit;
		if($row->id !=''){
		   // echo "ravi for"; //exit;
		    $insert = array('document_date' =>$post['transaction_date'],'in_qty' =>$row->in_qty+$post['in_qty'] ,'in_value' =>$row->in_value+$post['in_value'], 'out_qty' => $row->out_qty+$post['out_qty'], 'out_value' => $row->out_value+$post['out_value'],'total_cost'=> $row->item_cost *( ($row->in_qty+$post['in_qty']) + ($row->out_qty+$post['out_qty'])));
			$insert_data=$callConfig->updateRecord('item_ledger_fifo',$insert,'id',$row->id);
			if($post['out_qty']!=0 ){
    		   $this->itemlifoupdateledge($post);
    		}else{
    		   $whr21="item_id = '".$post['item_id']."' AND document_type='".$post['document_type']."' AND document_no='".$post['document_no']."'";
		       $query21=$callConfig->selectQuery('item_fifo_balances','*',$whr21,'','',''); 
		       $row21 = $callConfig->getRow($query21);
		       $updatequery =  "UPDATE `item_fifo_balances` SET in_qty = ".$row->in_qty+$post['in_qty'].",closing_qty= ".$row->closing_qty+$post['out_qty'].", in_value = ".$row->in_value+$post['in_value']." WHERE id = '".$row->id."'";
	            $callConfig->executeQuery($updatequery);
    		}
		}else{ //exit;
    		if($post['out_qty']!=0 || $post['in_qty'] !=0){
    		  if($post['out_qty']!=0 ){
    		     $this->itemfifoupdateledge($post);
    		  }else{
    		    $insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'] ,'document_ref_no' =>$post['document_ref_no'] ,'document_date' =>$post['transaction_date'],'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'item_cost' => $post['in_value']/$post['in_qty'],'total_cost'=> $post['in_value'],'out_qty' => $post['out_qty'], 'out_value' => $post['out_value']);
    	    	$res=$callConfig->insertRecord('item_ledger_fifo',$insert);
    	    	$insert = array('company_id' =>$post['company_id'] , 'item_id' =>$post['item_id'] ,'balance_date' =>$post['transaction_date'] ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['in_value'],'day_rate' => $post['in_value']/$post['in_qty'],'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'],'closing_qty'=>$post['in_qty']);
    	        $res=$callConfig->insertRecord('item_fifo_balances',$insert);
    	        $insert['location_id'] = $post['location_id'];
    	        $res=$callConfig->insertRecord('loc_item_fifo_balances',$insert);
    		  }
    		}
		}
		return $res;
	}
	function  itemfifoupdateledge($post){
	    global $callConfig;
	    $count = $post['out_qty'];
	    $itemcount = $post['out_qty'];
	    if($count > 0){
	    $locquery = "SELECT * FROM `loc_item_fifo_balances` WHERE item_id =".$post['item_id']." AND location_id =".$post['location_id']." AND company_id=".$post['company_id']." AND closing_qty >0 ORDER By balance_date,id ASC";   
	    $query = "SELECT * FROM `item_fifo_balances` WHERE item_id =".$post['item_id']." AND company_id=".$post['company_id']." AND closing_qty >0 ORDER By balance_date,id ASC";   
	   
	    }else{
	      $locquery = "SELECT * FROM `loc_item_fifo_balances` WHERE item_id =".$post['item_id']." AND location_id =".$post['location_id']." AND company_id=".$post['company_id']." AND (closing_qty = 0 OR closing_qty!=in_qty)  ORDER By balance_date,id ASC";   
	      $query = "SELECT * FROM `item_fifo_balances` WHERE item_id =".$post['item_id']." AND company_id=".$post['company_id']." AND (closing_qty = 0 OR closing_qty!=in_qty)  ORDER By balance_date,id ASC";   
	    }
	    $rows = $callConfig->getAllRows($locquery);
	    $itemrows = $callConfig->getAllRows($query);
	    //echo $query;
	    //print_r($rows); echo "ravi";
	    foreach($itemrows as $itemdata){
	       if($itemcount > 0){        
    	       if($itemdata->closing_qty < $itemcount){
    	           $itemremaning_count = $itemcount- $itemdata->closing_qty;
    	           $count = $itemdata->closing_qty;
    	       }else{
    	           $itemremaning_count =0;
    	       }
	       }else{
	           if($itemdata->closing_qty < -(-$itemcount)){
    	           $remaning_count = $itemcount+ $itemdata->closing_qty;
    	           $itemcount = "-".$itemdata->closing_qty;
    	       }else{
    	           $itemremaning_count =0;
    	       }
	       }
	       $cc = $itemdata->out_qty+$itemcount;
	       $ccc = $itemdata->closing_qty-$itemcount;
	       $updatequery121 =  "UPDATE `item_fifo_balances` SET out_qty = ".$cc.",closing_qty= ".$ccc." WHERE id = '".$itemdata->id."'";
	       //echo $updatequery121;
	       $callConfig->executeQuery($updatequery121);//exit
	       if($itemremaning_count == 0){ 
	           break;
	       }
	       $itemcount = $itemremaning_count;
	    }
	    
	    foreach($rows as $row){
	       if($count > 0){        
    	       if($row->closing_qty < $count){
    	           $remaning_count = $count- $row->closing_qty;
    	           $count = $row->closing_qty;
    	       }else{
    	           $remaning_count =0;
    	       }
	       }else{
	           if($row->closing_qty < -(-$count)){
    	           $remaning_count = $count+ $row->closing_qty;
    	           $count = "-".$row->closing_qty;
    	       }else{
    	           $remaning_count =0;
    	       }
	       }
	       $cc  =$row->out_qty+$count;
	       $ccc = $row->closing_qty-$count;
	       $updatequery1 =  "UPDATE `loc_item_fifo_balances` SET out_qty = ".$cc.",closing_qty= ".$ccc." WHERE id = '".$row->id."'";
	       //echo $updatequery;
	       $callConfig->executeQuery($updatequery1);//exit;
	       $bdate = $post['transaction_date'];
	       $insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'] ,'document_ref_no' =>$post['document_ref_no'] ,'document_date' =>$post['transaction_date'],'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'item_cost' => $row->day_rate,'total_cost'=>$row->day_rate*$count ,'out_qty' => $count, 'out_value' => ($post['out_value']/$post['out_qty'])*$count);
    	   $res=$callConfig->insertRecord('item_ledger_fifo',$insert);
	       if($remaning_count == 0){
	           break;
	       }
	       $count = $remaning_count;
	    }
	    
	    return $res; 
	}

	function insertorupdateitemfifo($post){
		global $callConfig;
		$bdate = date('Y-m-d');
		$insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'balance_date' =>$bdate ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'day_value' => $post['day_value'],'day_rate' => $post['day_rate']);
		$res=$callConfig->insertRecord('loc_item_fifo_lifo_balances',$insert);
		return $res;
	}	
	function averagebalance($post){
		//print_r($post);
		global $callConfig;
		$bdate = date('Y-m-d');
		$whrstp="company_id = '".$post['company_id']."' AND location_id='".$post['location_id']."' AND item_id= '".$post['item_id']."' AND balance_date= '".$bdate."'";
		$query=$callConfig->selectQuery('loc_item_wa_balances','*',$whrstp,'','','');
		$data = $callConfig->getRow($query);
		if($data->id ==''){
			$whrstp1="company_id = '".$post['company_id']."' AND location_id='".$post['location_id']."' AND item_id= '".$post['item_id']."";
			$query1=$callConfig->selectQuery('loc_item_wa_balances','*',$whrstp1,'id DESC','0','1');
			$data = $callConfig->getRow($query1);
		}
		$old_qty = $data->opening_qty-$data->out_qty+$data->in_qty;
		$old_rate = $data->day_rate;
		$return['avrage'] = (($old_rate*$old_qty)+($post['in_qty']*$post['in_value']))/($old_qty+$post['in_qty']);
		$return['running_qty'] = $old_qty+$post['in_qty'];
		$return['day_value'] = $return['avrage'] *  $return['running_qty'];
		$return['day_rate'] = $return['avrage'];
		$return['opening_qty'] = '0';
		$return['out_qty'] = '0';
		$return['closing_qty'] = '0';
		$return['opening_value'] = '0';
		$return['opening_rate'] = '0'; 
		$return['out_value'] = '0';

		return $return;
	}
	function insertoupdatelocitemwabalances($post){
		global $callConfig;
		$bdate = $post['transaction_date'];
		$insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'balance_date' =>$bdate ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['day_value'],'day_rate' => $post['day_rate']);
		//echo "<pre>"; print_r($insert);
		$whrstp="company_id = '".$post['company_id']."' AND location_id='".$post['location_id']."' AND item_id= '".$post['item_id']."' AND balance_date = '".$bdate."'";
		$query=$callConfig->selectQuery('loc_item_wa_balances','*',$whrstp,'','','');
		$data = $callConfig->getRow($query);
		$query2 = "SELECT * FROM loc_item_wa_balances WHERE company_id = '".$post['company_id']."' AND location_id='".$post['location_id']."' AND item_id= '".$post['item_id']."'  AND balance_date <= '".$bdate."' ORDER BY balance_date DESC LIMIT 0,1";
		//echo $query2;
		$data2 = $callConfig->getRow($query2);
		//print_r($data2);// exit;
		if($data->id !=''){
			$insert['in_qty'] = $insert['in_qty'] + $data->in_qty;
			$insert['in_value'] = $insert['in_value'] + $data->in_value;
			$insert['out_qty'] = $insert['out_qty'] + $data->out_qty;
			$insert['out_value'] = $insert['out_value'] + $data->out_value;
			$insert['closing_qty'] = $data->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['day_value'] = $data->day_value+$post['in_value']-$post['out_value'];
			$insert['day_rate'] = $insert['closing_qty'] != 0 ? $insert['day_value']/$insert['closing_qty'] : 0;	
			//echo "<pre>"; print_r($insert); //exit;
			$res=$callConfig->updateRecord('loc_item_wa_balances',$insert,'id',$data->id); 
		}else{
			$insert['opening_qty'] = $data2->closing_qty;
			$insert['closing_qty'] = $data2->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['opening_value'] = $data2->day_value;
			$insert['day_value'] = $data2->day_value+$post['in_value']-$post['out_value'];
			$insert['opening_rate'] = $data2->day_rate;
			$insert['day_rate'] = $insert['closing_qty'] != 0 ? $insert['day_value']/$insert['closing_qty'] : 0;
			//print_r($insert); //exit;
			$res=$callConfig->insertRecord('loc_item_wa_balances',$insert);
		}

		$updatequery = "update loc_item_wa_balances SET opening_qty = opening_qty+".$insert['in_qty']."-".$insert['out_qty'].", closing_qty = closing_qty+".$insert['in_qty']."-".$insert['out_qty'].", opening_value = opening_value+".$insert['in_value']."-".$insert['out_value'].", opening_rate = opening_value/opening_qty, day_value = day_value+".$insert['in_value']."-".$insert['out_value'].", day_rate = day_value/closing_qty where item_id = ".$insert['item_id']." AND location_id='".$post['location_id']."' AND balance_date > '".$bdate."'";
		$callConfig->executeQuery($updatequery);
		//print_r($res); exit;
		return $res;
	}
	function insertoupdatelocitemstbalances($post){
		global $callConfig;
		$post['in_value'] = $post['in_qty']*$post['item_st_cost'];
		$post['out_value'] = $post['out_qty']*$post['item_st_cost'];
		$bdate = $post['transaction_date'];
		$insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'balance_date' =>$bdate ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['day_value'],'day_rate' => $post['day_rate']);
		//echo "<pre>"; print_r($insert);
		$whrstp="company_id = '".$post['company_id']."' AND location_id='".$post['location_id']."' AND item_id= '".$post['item_id']."' AND balance_date = '".$bdate."'";
		$query=$callConfig->selectQuery('loc_item_st_balances','*',$whrstp,'','','');
		$data = $callConfig->getRow($query);
		$query2 = "SELECT * FROM loc_item_st_balances WHERE company_id = '".$post['company_id']."' AND location_id='".$post['location_id']."' AND item_id= '".$post['item_id']."'  AND balance_date <= '".$bdate."' ORDER BY balance_date DESC LIMIT 0,1";
		//echo $query2;
		$data2 = $callConfig->getRow($query2);
		//print_r($data2);// exit;
		if($data->id !=''){
			$insert['in_qty'] = $insert['in_qty'] + $data->in_qty;
			$insert['in_value'] = $insert['in_value'] + $data->in_value;
			$insert['out_qty'] = $insert['out_qty'] + $data->out_qty;
			$insert['out_value'] = $insert['out_value'] + $data->out_value;
			$insert['closing_qty'] = $data->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['day_value'] = $data->day_value+$post['in_value']-$post['out_value'];
			$insert['day_rate'] = $insert['closing_qty'] != 0 ? $insert['day_value']/$insert['closing_qty'] : 0;	
			//echo "<pre>"; print_r($insert); //exit;
			$res=$callConfig->updateRecord('loc_item_st_balances',$insert,'id',$data->id); 
		}else{
			$insert['opening_qty'] = $data2->closing_qty;
			$insert['closing_qty'] = $data2->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['opening_value'] = $data2->day_value;
			$insert['day_value'] = $data2->day_value+$post['in_value']-$post['out_value'];
			$insert['opening_rate'] = $data2->day_rate;
			$insert['day_rate'] = $insert['closing_qty'] != 0 ? $insert['day_value']/$insert['closing_qty'] : 0;
			//print_r($insert); //exit;
			$res=$callConfig->insertRecord('loc_item_st_balances',$insert);
		}

		$updatequery = "update loc_item_st_balances SET opening_qty = opening_qty+".$insert['in_qty']."-".$insert['out_qty'].", closing_qty = closing_qty+".$insert['in_qty']."-".$insert['out_qty'].", opening_value = opening_value+".$insert['in_value']."-".$insert['out_value'].", opening_rate = opening_value/opening_qty, day_value = day_value+".$insert['in_value']."-".$insert['out_value'].", day_rate = day_value/closing_qty where item_id = ".$insert['item_id']." AND location_id='".$post['location_id']."' AND balance_date > '".$bdate."'";
		$callConfig->executeQuery($updatequery);
		//print_r($res); exit;
		return $res;
	}
	function insertoupdateitemwabalances($post){
		global $callConfig;
		$bdate = $post['transaction_date'];
		$insert = array('company_id' =>$post['company_id'] , 'item_id' =>$post['item_id'] ,'balance_date' =>$bdate ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['day_value'],'day_rate' => $post['day_rate']);
		//echo "<pre>"; print_r($insert);
		$whrstp="company_id = '".$post['company_id']."' AND  item_id= '".$post['item_id']."' AND balance_date = '".$bdate."'";
		$query=$callConfig->selectQuery('item_wa_balances','*',$whrstp,'','','');
		$data = $callConfig->getRow($query);
		$query2 = "SELECT * FROM item_wa_balances WHERE company_id = '".$post['company_id']."'  AND item_id= '".$post['item_id']."'  AND balance_date <= '".$bdate."' ORDER BY balance_date DESC LIMIT 0,1";
		//echo $query2;
		$data2 = $callConfig->getRow($query2);
		//print_r($data2);// exit;
		if($data->id !=''){
			$insert['in_qty'] = $insert['in_qty'] + $data->in_qty;
			$insert['in_value'] = $insert['in_value'] + $data->in_value;
			$insert['out_qty'] = $insert['out_qty'] + $data->out_qty;
			$insert['out_value'] = $insert['out_value'] + $data->out_value;
			$insert['closing_qty'] = $data->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['day_value'] = $data->day_value+$post['in_value']-$post['out_value'];
			$insert['day_rate'] = $insert['closing_qty'] != 0 ? $insert['day_value']/$insert['closing_qty'] : 0;	
			//echo "<pre>"; print_r($insert); //exit;
			$res=$callConfig->updateRecord('item_wa_balances',$insert,'id',$data->id); 
		}else{
			$insert['opening_qty'] = $data2->closing_qty;
			$insert['closing_qty'] = $data2->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['opening_value'] = $data2->day_value;
			$insert['day_value'] = $data2->day_value+$post['in_value']-$post['out_value'];
			$insert['opening_rate'] = $data2->day_rate;
			$insert['day_rate'] = $insert['closing_qty'] != 0 ? $insert['day_value']/$insert['closing_qty'] : 0;
			//print_r($insert); //exit;
			$res=$callConfig->insertRecord('item_wa_balances',$insert);
		}

		$updatequery = "update item_wa_balances SET opening_qty = opening_qty+".$insert['in_qty']."-".$insert['out_qty'].", closing_qty = closing_qty+".$insert['in_qty']."-".$insert['out_qty'].", opening_value = opening_value+".$insert['in_value']."-".$insert['out_value'].", opening_rate = opening_value/opening_qty, day_value = day_value+".$insert['in_value']."-".$insert['out_value'].", day_rate = day_value/closing_qty where item_id = ".$insert['item_id']." AND balance_date > '".$bdate."'";
		$callConfig->executeQuery($updatequery);
		//print_r($res); exit;
		return $res;
	}
	function insertoupdateitemstbalances($post){
		global $callConfig;
		$post['in_value'] = $post['in_qty']*$post['item_st_cost'];
		$post['out_value'] = $post['out_qty']*$post['item_st_cost'];
		$bdate = $post['transaction_date'];
		$insert = array('company_id' =>$post['company_id'] , 'item_id' =>$post['item_id'] ,'balance_date' =>$bdate ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['day_value'],'day_rate' => $post['day_rate']);
		//echo "<pre>"; print_r($insert);
		$whrstp="company_id = '".$post['company_id']."' AND  item_id= '".$post['item_id']."' AND balance_date = '".$bdate."'";
		$query=$callConfig->selectQuery('item_st_balances','*',$whrstp,'','','');
		$data = $callConfig->getRow($query);
		$query2 = "SELECT * FROM item_st_balances WHERE company_id = '".$post['company_id']."'  AND item_id= '".$post['item_id']."'  AND balance_date <= '".$bdate."' ORDER BY balance_date DESC LIMIT 0,1";
		//echo $query2;
		$data2 = $callConfig->getRow($query2);
		//print_r($data2);// exit;
		if($data->id !=''){
			$insert['in_qty'] = $insert['in_qty'] + $data->in_qty;
			$insert['in_value'] = $insert['in_value'] + $data->in_value;
			$insert['out_qty'] = $insert['out_qty'] + $data->out_qty;
			$insert['out_value'] = $insert['out_value'] + $data->out_value;
			$insert['closing_qty'] = $data->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['day_value'] = $data->day_value+$post['in_value']-$post['out_value'];
			$insert['day_rate'] = $insert['closing_qty'] != 0 ? $insert['day_value']/$insert['closing_qty'] : 0;	
			//echo "<pre>"; print_r($insert); //exit;
			$res=$callConfig->updateRecord('item_st_balances',$insert,'id',$data->id); 
		}else{
			$insert['opening_qty'] = $data2->closing_qty;
			$insert['closing_qty'] = $data2->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['opening_value'] = $data2->day_value;
			$insert['day_value'] = $data2->day_value+$post['in_value']-$post['out_value'];
			$insert['opening_rate'] = $data2->day_rate;
			$insert['day_rate'] = $insert['closing_qty'] != 0 ? $insert['day_value']/$insert['closing_qty'] : 0;
			//print_r($insert); //exit;
			$res=$callConfig->insertRecord('item_st_balances',$insert);
		}

		$updatequery = "update item_st_balances SET opening_qty = opening_qty+".$insert['in_qty']."-".$insert['out_qty'].", closing_qty = closing_qty+".$insert['in_qty']."-".$insert['out_qty'].", opening_value = opening_value+".$insert['in_value']."-".$insert['out_value'].", opening_rate = opening_value/opening_qty, day_value = day_value+".$insert['in_value']."-".$insert['out_value'].", day_rate = day_value/closing_qty where item_id = ".$insert['item_id']." AND balance_date > '".$bdate."'";
		$callConfig->executeQuery($updatequery);
		//print_r($res); exit;
		return $res;
	}
	function UpdateBudgetBlance($data){
		global $callConfig;
		$tr_date = explode('-', $data['transaction_date']);
		$month = $this->fiscalmonth($tr_date[1]);
		$field = 'rq_m'.$month;
		$itemsquery  = "SELECT account_code,sum(item_amount) as amount FROM ".$data['table']." WHERE transaction_id = ".$data['transaction_id']." AND company_id =".$data['company_id']." AND is_active = 0  GROUP BY account_code";
		//echo $itemsquery; exit;
		$rows = $callConfig->getAllRows($itemsquery);

		foreach ($rows as $row) {
			$updatequery = "UPDATE	budget_balances set ".$field." = ".$field ."+ ".$row->amount." WHERE company_id = ".$data['company_id']." AND cost_center_id =".$data['cost_center']." AND account_code = '".$row->account_code."' AND fiscal_year ='".FISCALYEAR."'"; 
			//echo $updatequery; exit;
			$updateblance = $callConfig->executeQuery($updatequery);
		}
		return $updatequery;
	}
	function fiscalmonth($month){
		global $callConfig;
		$array = array('01'=> '10','02'=> '11','03'=> '12','04'=> '1','05'=> '2','06'=> '3','07'=> '4','08'=> '5','09'=> '6','10'=> '7','11'=> '8','12'=> '9');
		return $array[$month];
	}
	function insertandupdatedataapplicationtag($data, $id, $option){
		global $callConfig;
		
		$appv_fieldnames=array('transaction_id'=>$id,'transaction_number'=>$data['transaction_number'],'transaction_date'=>$data['transaction_date'],'transaction_amount'=>$data['total_amount'],'transaction_status'=>$data['current_status'],'option_url'=>$option);
		//echo "<pre>"; print_r($appv_fieldnames); //exit;
		$whr="transaction_id = ".$id." AND transaction_number='".$data['transaction_number']."'";
		$query=$callConfig->selectQuery(TBL_APPLICATION_TAGS,'*',$whr,'','','');
		//echo $query; //exit;
		$row = $callConfig->getRow($query);
		if(count($row) > 0){
			//echo "updata";
			$res=$callConfig->updateRecord(TBL_APPLICATION_TAGS,$appv_fieldnames,'id',$row->id);
		}else{
			$res=$callConfig->insertRecord(TBL_APPLICATION_TAGS,$appv_fieldnames);
		}
		//print_r($row); exit;
		return $res; 

	}
	function tocheckDAOValues($data){
		global $callConfig;
		$whr="process_name='".$data['transaction_type']."'";
		$query1=$callConfig->selectQuery(TBL_WORKFLOW_CONFIGURATION,'*',$whr,'','','');
		$row = $callConfig->getRow($query1);
		//print_r($row);
		if($row->tigger_doa == 0){
			$where = "";
			if($data['opex_cpex']!=""){
				$where.= "AND opex_capex = '".$data['opex_cpex']."'";
			}
			if($data['category_type']!=""){
				$where.= "AND  transaction_category = '".$data['category_type']."'";
			}
			if($data['cost_center']!=""){
				$where.= "AND cost_center_id = '".$data['cost_center']."'";
			}
			$query = "SELECT id , mail_group_id, doa_condition, approval_type, approval_number FROM ". TBL_DOA ." WHERE transaction_type = '".$data['transaction_type']."' and from_transaction_amount <= '".$data['total_amount']."' and to_transaction_amount >= '".$data['total_amount']."' and  is_active =0 and status_value_from = '".$data['status_old_value']."' and status_value_to = '".$data['status']."'".$where;
			//echo $query;
			$data1 = $callConfig->getRow($query);
		}else{
			$data1['id'] = 1;
		}
		return $data1;
	}
	/*function toGetUserids($emails){
		global $callConfig;
		$query2="SELECT * FROM ".TBL_USERS." WHERE user_email in (".$emails.")";
		$users = $callConfig->getAllRows($query2);
		return  $users;
	}
*/
	function togetemailslist($data){
		global $callConfig;
		$emailstr = "";
		$email_id = array(); $email_group_id = array();
		$data1 = $this->tocheckDAOValues($data);
		if(count($data1) > 0){
			$email_group_id[] = $data1->mail_group_id;
		}
		$whrstp="table_name = '".$data['transaction_type']."' AND status='".$data['status']."'";
		$querystp=$callConfig->selectQuery(TBL_STATUSPROGRES,'*',$whrstp,'','','');
		//echo $querystp;
		$resstp = $callConfig->getRow($querystp);
		//print_r($resstp);
		$whrstpA="target_status = '".$resstp->id."'";
		$querystpA=$callConfig->selectQuery(TBL_STATUSPROGRESACTIONS,'*',$whrstpA,'','','');
		//echo $querystpA; exit;
		$resstpA = $callConfig->getRow($querystpA);
		$stpaemails_group= json_decode($resstpA->email_groups, 1);
		if(count($stpaemails_group) > 0){
			foreach ($stpaemails_group as $stpavalue) {
				$email_group_id[] = $stpavalue;
			}
		}
		$emil_lists = implode(",",$email_group_id);
		//echo $emil_lists; exit;
		//
		$whr = "email_groups_id IN (".$emil_lists.")";	
		$email_ids = $this->getAllTablesData(TBL_EMAIL_GROUP_EMAILS,'*',$whr,'');

		foreach ($email_ids as $emails_id) {
			$email_id [] = $emails_id->email_id;
		}
		$email_id = implode(",", $email_id);
		$query1="SELECT email,user_id FROM ".TBL_EMAIL." WHERE id in (".$email_id.")";
		//echo $query1; exit;
		$emailids = $callConfig->getAllRows($query1);
		foreach ($emailids as $email) {
			$return ['email'] []= $emailids->email;
			if($email ->user_id !=0){
				$return ['user_id'][] = $email->user_id;
			}
		}
		//print_r($return); exit;
		$return ['doa_id'] = $data1->id;
		return $return;
	}
	function sendEmailslist($data){
		global $callConfig;
		//print_r($data); exit;
		$email_data = array('email_source'=>$data['sender'], 'content'=>$data['content'], 'email_type'=>$data['email_type'], 'is_active'=>0 , 'current_status'=>0, 'created_by'=>1 ,'created_date'=> STR_TO_TIME);
		foreach ($data['revicer'] as $email) {
			$email_data['email'] = $email;
			//echo "<pre>"; print_r($email_data);
			$email_res=$callConfig->insertRecord(TBL_EMAIL_LOGS,$email_data);
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			// More headers
			$headers .= 'From:'.$data['sender'] . "\r\n";
			//$headers .= 'Cc: myboss@example.com' . "\r\n";
			$subject = ucwords(str_replace('_', ' ', $data['subject']));
			mail($email,$subject,$data['mesg'],$headers);
		}
		//echo "email send sucess";
		return $email_data;
	}
	function checkhierarchicallevel($data){
		global $callConfig;
		/*$email_data = array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_type'=>$table, 'transaction_date'=>$invoiced_at, 'amount'=>$post['subTotal'], 'tax_amount'=>$post['taxRate'] , 'discount_amount'=>$post['discount_amount'] , 'total_amount'=>$post['grandtotal'] ,'action_label'=>'Submit for Approval', 'option'=>$fieldarray[$table]['option'] ,'Rid'=>$insert_id,'email_template'=>1,'status_old_value'=>$post['status_old_value'],'status'=>$status_main,'updated_by'=>$post['updated_by'],'originator'=>$fieldnames['originator'],'opex_cpex'=>$post['opex_cpex'],'category_type'=>$post['type'],'cost_center'=>$post['cost_center']);*/
		$whrstp="company_id = '".$data['company_id']."' AND process_name='".$data['transaction_type']."'";
		$query=$callConfig->selectQuery(TBL_WORKFLOW_CONFIGURATION,'*',$whrstp,'','','');
		$res = $callConfig->getRow($query);
		//echo "<pre>"; print_r($res);
		$return['doa'] = $res->tigger_doa == 0 ? 1 : 2;
		if($res->trigger_workflow == 1){
			$level = $res->hierarchical_level;
			//echo $level;
			$where = "company_id = '".$data['company_id']."' AND transaction_table = '".$data['transaction_type']."' AND transaction_id='".$data['Rid']."' AND send_back_flag=0 AND response_type !=0 AND doa_id = 0";	
			$query1=$callConfig->selectQuery(TBL_TRANSACTION_APPROVAL_LOGS,'*',$where,'','','');
			$reslut = $callConfig->getAllRows($query1);
			//print_r($reslut);
			if(count($reslut) < $level ){
				//echo "ravi";
				//print_r($reslut);
				//$return['status'] = '0';
				$query3 = "SELECT id,user_email FROM ".TBL_USERS." WHERE id IN (SELECT `reports_to` FROM ".TBL_USERS." WHERE id=".$data['updated_by'].")";
				//echo $query3;
				$ret = $callConfig->getRow($query3);
				//print_r($ret);
				$return['email'] = $ret->user_email;
				$return['user_id'] = array($ret->id);
				$return['doa'] = 0;
			}

		}
		$return['doa_id'] =0;
		return $return;
	}

	function toSendEmailNotificationsBaseDAO($data){
		//echo "<pre>"; print_r($data); exit;
		global $callConfig;
		$email_data = $this->checkhierarchicallevel($data);
		//print_r($email_data);exit;
		if($email_data['doa'] == 1){
			$email_data = $this-> togetemailslist($data);
		}
		//print_r($email_data);exit;
		$emails[] = $email_data['email'];
		//$EMAIL FUNTION
		$users = $email_data['user_id'];
		$query1="SELECT email,user_id FROM ".TBL_EMAIL." WHERE user_id in (".$data['originator'].")";
		//echo $query1; exit;
		$emailids = $callConfig->getRow($query1);
		//print_r($emailids);exit;
		$emails[] = $email_ids->email;
		//print_r($email_data);exit;
		$whrstp="table_name = '".$data['transaction_type']."' AND status='".$data['status']."'";
		$querystp=$callConfig->selectQuery(TBL_STATUSPROGRES,'*',$whrstp,'','','');
		//echo $querystp; //exit;
		$resstp = $callConfig->getRow($querystp);
		//print_r($resstp); exit;
		$data['action_label'] =  $resstp->action_label;
		$data['email_template'] =  $resstp->email_template_id;
		//print_r($data); exit;
		$data['email_content_id'] = 1;
		$email_array = $this->emailtemplate($data);
		
		$file_name = 'email'.'_'.time().uniqid().'.'.'html';
		$email_array ['content'] = $file_name;
		$email_array['revicer'] = $emails;
		$email_array['email_type'] = 1;
		$email = $email_array['mesg'];
		
		//print_r($email_array); exit;
		$file_path='uploads/email/'.$file_name;
		file_put_contents($file_path, $email);
		fclose($file_path);
		$lenn = $this->sendEmailslist($email_array);

		$notarry['notification_message'] = ucwords($data['transaction_type'])." ".$data['transaction_number']. ": ".ucwords($resstp->action_label) ;
		//echo "ravi"; exit;
		$notarry['notification_users'] = count($users);
		$notarry['id'] = $data['Rid'];
		$notarry['option'] = $data['option'];
		//print_r($notarry); exit;
		$jsnot= json_encode($notarry);
		$NOTIfieldnames=array('notification_message'=>$notarry['notification_message'],'notification_details'=>$jsnot,'notification_datetime'=>date("Y-m-d H:i:s"));
		$noti_res=$callConfig->insertRecord(TBL_NOTIFICATIONS,$NOTIfieldnames);
		$notification_id = $noti_res['LASTID'];
		//$notification_id = 13;
		//echo "ravi".$notification_id ; print_r($users);	  exit;
		foreach ($users as $user) {
			//$user_id = $user->id;
			$appv_fieldnames=array('company_id'=>$data['company_id'],'transaction_table'=>$data['transaction_type'],'transaction_id'=>$data['Rid'],'user_id'=>$user,'request_date'=>date("Y-m-d H:i:s"),'doa_id' => $email_data['doa_id']);
			if($data['status'] == ST_APPROVAL_PENDING || $data['status'] == ST_APPROVED1 || $data['status'] == ST_APPROVED2 || $data['status'] == ST_APPROVED3 || $data['status'] == ST_APPROVED4 ||$data['status'] == ST_APPROVED5 || $data['status'] == ST_APPROVED6 || $data['status'] == ST_APPROVED7 || $data['status'] == ST_APPROVED8 || $data['status'] == ST_APPROVED9){
				$approval_id=$callConfig->insertRecord(TBL_TRANSACTION_APPROVAL_LOGS,$appv_fieldnames);
				$revalue = 'ACTION';
			}else{
				$revalue = 'INFORMATION';
			}
			$nti_use_fieldnames=array('user_id'=>$user,'notification_id'=>$notification_id,'notification_type'=>$revalue);
			$res=$callConfig->insertRecord(TBL_NOTIFICATON_USER,$nti_use_fieldnames);
			//echo "<pre>"; print_r($nti_use_fieldnames);
		}
		
		if($data['originator'] != ''){
		$nti_use_fieldnames=array('user_id'=>$data['originator'],'notification_id'=>$notification_id,'notification_type'=>'ACTION');
		//print_r($nti_use_fieldnames); exit;
		$res=$callConfig->insertRecord(TBL_NOTIFICATON_USER,$nti_use_fieldnames);
		}
		//echo "ravi"; exit;
		return $noti_res;
	}
	function getInvoicesData($id,$table){
		global $callConfig;	
		$fieldarray  = $this->toGetInvoicetabledata($table);
		/*$fieldarray ['quotations'] = array('item' => 'qo_items', 'tax' => 'qo_item_taxes' );
		$fieldarray ['purchase_orders'] = array('item' => 'po_items', 'tax' => 'po_item_taxes' );
		$fieldarray ['requisitions'] = array('item' => 'rq_items', 'tax' => 'rq_item_taxes' );
		$fieldarray ['bills'] = array('item' => 'bill_items', 'tax' => 'bill_item_taxes' );
		$fieldarray ['petty_cash'] = array('item' => 'pc_items', 'tax' => 'pc_item_taxes' );*/
		//echo "<pre>";print_r($fieldarray);exit;

		$where = "id = '".$id."'";	
		$query=$callConfig->selectQuery($table,'*',$where,'','','');
		$data = $callConfig->getRow($query);
		$pr['data'] = $data;
		//print_r($pr);
		//$pr = $pr->0;
		$whr = "transaction_id = '".$id."' AND is_active = 0";	
		$pr['items'] = $this->getAllTablesData($fieldarray['item'],'*',$whr,'');
		$whrs = "transaction_id = '".$id."'";	
		$pr['taxes'] = $this->getAllTablesData($fieldarray['tax'],'*',$whrs,'');
		$whrs1 = "transaction_id = '".$id."' AND application_table_name = '".$table."' AND is_active != 1";
		$pr['files'] = $this->getAllTablesData(TBL_APPLICATION_ATTACHMENTS,'*',$whrs1,'');
		return $pr;
	}
	function toGetStatusProgressActionList($table, $status){
		global $callConfig;
		//echo $table."|||".$status;
		$whr = "table_name = '".$table."' AND status = '".$status."'";
		$statusp = $this->getrowTablesData(TBL_STATUSPROGRES,'*',$whr,'');	
		$status_id = $statusp->id;
		$whrs = "status_progress_id = '".$status_id."' AND is_active = 0";
		$datas = $this->getAllTablesData(TBL_STATUSPROGRESACTIONS,'*',$whrs,'');
		foreach ($datas as  $data) {
		//	print_r($data);
			$whr1 = "id = '".$data->target_status."'";
			//echo $whr1;
			$status = $this->getrowTablesData(TBL_STATUSPROGRES,'*',$whr1,'');	
			$len[] = $status;
			//print_r($len);
		}

		//echo $status_id; exit;
		//print_r($status);
		return $len;
	}

	function updateapprovallog($post){
		global $callConfig;
		$whr = "";
		$fieldnames = array('comments'=>$post['comments'],'response_date'=>$post['response_date'],'response_type'=>$post['response_type']);
		//print_r($post);// exit;
		$whr = "transaction_table = '".$post['table']."' AND transaction_id='".$post['hid']."' AND user_id='".$post['user_id']."' AND send_back_flag= 0";
	//	echo  $whr; exit;
		$res=$callConfig->updateRecord(TBL_TRANSACTION_APPROVAL_LOGS,$fieldnames,'','',$whr);
		if($post['response_type'] != ST_SEND_BACK ){ 
		    //echo "ravi"; exit;
		    $data = array('transaction_type'=>$post['response_date'],'total_amount'=>$post['amount'],'status'=>6,'status_old_value'=>0,'company_id' => $post['company_id']);
			$return = $this->ToCheckStatusApprovalLog($post);
			//echo $return; exit;
			//$return = $post['response_type'];
		}else{
			$fieldname = array('response_date'=>$post['response_date'],'send_back_flag'=>1);
		//print_r($post); exit;ZZ
		$whrs = "transaction_table = '".$post['table']."' AND transaction_id='".$post['hid']."' AND send_back_flag= 0";
			$res=$callConfig->updateRecord(TBL_TRANSACTION_APPROVAL_LOGS,$fieldname,'','',$whrs);
			$return = $post['response_type'];
		} //exit;
		return $return;
	}
	function toGetPendingRequisitionQty($post){
		global $callConfig;
		$fieldarray  = $this->toGetInvoicetabledata($post['table']);
		$query = "SELECT rq.id ,sum(reqi.pending_qty)as pending_qty,rq.`transaction_number` FROM ".$post['table']." rq, ".$fieldarray['item']." reqi WHERE rq.id = reqi.transaction_id AND rq.current_status = '".ST_APPROVED."' GROUP BY rq.id,rq.transaction_number having sum(reqi.pending_qty) > 0";
		//echo $query;
		$data = $callConfig->getAllRows($query);
		return $data;
	}
	function emailtemplate($send_data){
		//print_r($send_data); exit;
		global $callConfig;
		if($send_data['email_template'] == 1){
			$data = $this->emailtemplate1($send_data);
		}
		if($send_data['email_template'] == 2){
			$data = $this->emailtemplate2($send_data);
		}
		return $data;
	}
	function emailtemplate2($send_data){
		global $callConfig;
		//print_r($send_data); exit;
		$temp = "";
		$where = "id = 1";	
		$config = $send_data['data_name']!="" ? $send_data['data_name'].".".TBL_CONFIG :TBL_CONFIG ; 
		$email_con = $send_data['data_name']!="" ? $send_data['data_name'].".".TBL_EMAIL_CONTENT :TBL_EMAIL_CONTENT ; 
		$query=$callConfig->selectQuery($config,'*',$where,'','','');
		//echo $query; exit;
		$head = $callConfig->getRow($query);
		//print_r($head); exit;
		$twhere = "id = '".$send_data['email_content_id'] ."' AND template_id= '".$send_data['email_template']."'";	
		$query1=$callConfig->selectQuery($email_con,'*',$twhere,'','','');
		//echo $query1; exit;
		$data = $callConfig->getRow($query1);
		//print_r($data); exit;
		$subject =  $this->emaildatareplace($send_data, $data->email_subject);	
		$temp = '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 
<head>
  
</head>
<body yahoo bgcolor="#f6f8f1">
<table width="70%" style="padding:20px;background-color:#f5f5f5;" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr>
	  <td style="font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424;">
            <img class="fix" src="'.UPLOADIMGURL.'/all/'.$head->display_logo.'" width="70" height="70" border="0" alt="" />
			
	  </td>
	</tr>
	<tr>
	  <td>
		<table width="100%" style="padding:10px;background-color:#FFFFFF;"  >
			<tr>
			  <td style="padding-left:32px; padding-top:20px; font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424" >
					'.$this->emaildatareplace($send_data, $data->email_header).'
			  </td>
			</tr>
			<tr>
			  <td style="padding-left:33px; font-size:18px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b" >
					'.$this->emaildatareplace($send_data, $data->email_sub_header).'
			  </td>
			</tr>
			<tr>
			  <td style="padding-left:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b" >
					<div>'.$this->emaildatareplace($send_data, $data->email_content_1).' </div>
					
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
			  <td>
				   &nbsp;
			  </td>
			</tr>
			<tr>
			  <td style="padding-left:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
				  '.$this->emaildatareplace($send_data, $data->email_content_2).'

			  </td>
			</tr>
			<tr>
			  <td style="padding-left:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
				 <button style="background-color: #00abfb;border-radius: 0.25rem;text-align: center;font-size: 18px;font-family: sans-serif;font-weight: bold;    padding: 0 30px 0 30px;height: 45px;color: #FFFFFF; border: 1px solid transparent;" ><a style="text-decoration:none;color:#FFF;" href="'.SITEURL.'/login.php?confirm='.$send_data[user_id].'">Confirm </a></button>
			  </td>
			</tr>
		</table>
           
	  </td>
	</tr>
</table>
</body>
</html>
		';
		//echo $temp; exit;
	$return ['subject'] = $subject;
	$return ['mesg'] = $temp;
	$return ['sender'] =  $head->admin_email;
	return $return;

	}
	function emailtemplate1($send_data){
		//echo "emai"; exit;
		global $callConfig;
		$temp = "";
		$where = "id = 1";	
		$query=$callConfig->selectQuery(TBL_CONFIG,'*',$where,'','','');
		$head = $callConfig->getRow($query);
		$twhere = "template_id = 1";	
		$query1=$callConfig->selectQuery(TBL_EMAIL_CONTENT,'*',$twhere,'','','');
		$data = $callConfig->getRow($query1);
		$rid = base64_encode(base64_encode("id:".$send_data['Rid']));
		$subject =  $this->emaildatareplace($send_data, $data->email_subject);
		//$data['transaction_number'] = 'RTQ001';
		//$datacqq = $this->emaildatareplace($send_data, $data->email_subject);
		$ddvalue = $this->emaildatareplace($send_data, $data->transaction_data);
		$ddvalue = $this->emailnumberschange($ddvalue);
		if($data->transaction_data_display == 0){
			$display = '';
			$display= "<tr> <td>";
			$display.= $ddvalue;
			$display.= "</td> </tr>";
		}else{
			$ddvalue =explode(',', $ddvalue);
			$display = '';
			foreach ($ddvalue as $key => $value) {
				$dvir= explode(':', $value);
				$display.= '<tr><td style="width:150px;">'.$dvir[0].' </td> <td >:'.$dvir[1].'</td></tr>';
			}
		}
		///echo "dfsf"; exit;
		$temp = '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		 
		<head>
		  <style type="text/css">
		  html {
		  font-family: sans-serif;
		  line-height: 1.15;
		  -webkit-text-size-adjust: 100%;
		  -webkit-tap-highlight-color: rgba(0, 0, 0, 0); }
		  body {margin: 0; padding: 0; min-width: 100%!important;}
		  img {height: auto;}
		  .content {width: 100%; max-width: 70%;}
		  .header {padding: 40px 30px 20px 30px;}
		  .innerpadding {padding: 30px 30px 30px 30px;}
		  .borderbottom {border-bottom: 1px solid #f2eeed;}
		  .subhead {font-size: 15px; color: #ffffff; font-family: sans-serif; letter-spacing: 10px;}
		  .h1, .h2, .bodycopy {color: #153643; font-family: sans-serif;}
		  .h1 {font-size: 33px; line-height: 38px; font-weight: bold;}
		  .h2 {padding: 0 0 15px 0; font-size: 18px; line-height: 28px; font-weight: bold;}
		  .bodycopy {font-size: 16px; line-height: 22px;}
		  .button {
		  background-color: #00abfb;
		    border-radius: 0.25rem;
		    text-align: center;
		    font-size: 18px;
		    font-family: sans-serif;
		    font-weight: bold;
		    padding: 0 30px 0 30px;
		    height: 45px;
		    color: #FFFFFF;
		    border: 1px solid transparent;
			}
		  .button a {color: #ffffff; text-decoration: none;}
		  .footer {padding: 20px 30px 15px 30px;}
		  .footercopy {font-family: sans-serif; font-size: 14px; color: #ffffff;}
		  .footercopy a {color: #ffffff; text-decoration: underline;}

		  @media only screen and (max-width: 550px), screen and (max-device-width: 550px) {
		  body[yahoo] .hide {display: none!important;}
		  body[yahoo] .buttonwrapper {background-color: transparent!important;}
		  body[yahoo] .button {padding: 0px!important;}
		  body[yahoo] .button a {background-color: #f0f0f0; padding: 15px 15px 13px!important;}
		  body[yahoo] .unsubscribe {display: block; margin-top: 20px; padding: 10px 50px; background: #2f3942; border-radius: 5px; text-decoration: none!important; font-weight: bold;}
		  }

		  /*@media only screen and (min-device-width: 601px) {
		    .content {width: 600px !important;}
		    .col425 {width: 425px!important;}
		    .col380 {width: 380px!important;}
		    }*/

		  </style>
		</head>

		<body yahoo bgcolor="#f6f8f1">
		<table width="100%" bgcolor="#f6f8f1" border="0" cellpadding="0" cellspacing="0">
		<tr>
		  <td>
		    <!--[if (gte mso 9)|(IE)]>
		      <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
		        <tr>
		          <td>
		    <![endif]-->     
		    <table bgcolor="#ffffff" class="content" align="center" cellpadding="0" cellspacing="0" border="0">
		      <tr>
		        <td bgcolor="#fofofo" class="header">
		          <table width="70" align="left" border="0" cellpadding="0" cellspacing="0">  
		            <tr>
		              <td height="70" style="padding: 0 20px 20px 0;">
		                <img class="fix" src="'.UPLOADIMGURL.'/all/'.$head->display_logo.'" width="70" height="70" border="0" alt="" />
		              </td>
		            </tr>
		          </table>
		          <!--[if (gte mso 9)|(IE)]>
		            <table width="425" align="left" cellpadding="0" cellspacing="0" border="0">
		              <tr>
		                <td>
		          <![endif]-->
		          <table class="col425" align="left" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 425px;">  
		            <tr>
		              <td height="70">
		                <table width="100%" border="0" cellspacing="0" cellpadding="0">
		                  <tr>
		                    <td class="h1" style="padding: 5px 0 0 0;">'.$head->name;
		                 //$temp.= $head['name'];
		            $temp.='</td>
		                  </tr>
		                </table>
		              </td>
		            </tr>
		          </table>
		          <!--[if (gte mso 9)|(IE)]>
		                </td>
		              </tr>
		          </table>
		          <![endif]-->
		        </td>
		      </tr>
			  <tr>
		        <td class="innerpadding borderbottom">
		          <table width="100%" border="0" cellspacing="0" cellpadding="0">
		            <tr>
		              <td class="h2">
		                '.ucwords(str_replace('_', ' ',$this->emaildatareplace($send_data, $data->email_header))).'
		              </td>
		            </tr>
		            <tr>
		              <td class="bodycopy">
		               '.$this->emaildatareplace($send_data, $data->email_sub_header).'
		              </td>
		            </tr>
		          </table>
		        </td>
		      </tr>
		      <tr>
		        <td class="innerpadding borderbottom">
		          <table width="100%" border="0" cellspacing="0" cellpadding="0">
		            <tr>
		              <td class="h2">
						 '.$this->emaildatareplace($send_data, $data->email_addressed).',
		              </td>
		            </tr>
		            <tr>
		              <td class="bodycopy">
		                '.$this->emaildatareplace($send_data, $data->email_content_1).'
		              </td>
					  </tr>
					  <tr>
						  <td style="padding: 20px 0 0 0;">
			                      <table>
			                        <tr>
			                          <td>
			                            <button class=" buttonwrapper button"><a target="_blank" href="'.SITEURL.'/index.php?option='.$send_data[option].'&Rid='.$rid.'">View Order</a></button>
			                          </td>
			                        </tr>
			                      </table>
			               </td>
			            </tr>
		          </table>
		        </td>
		      </tr>
		    
		      <tr>
		        <td class="innerpadding borderbottom">
		          <table>
					
					'.$display.'
				  </table>
		        </td>
		      </tr>
		      <tr>
		        <td class="innerpadding bodycopy">
		         '.$this->emaildatareplace($send_data, $data->email_content_2).'
				  
		        </td>
		      </tr>
		      <tr>
		        <td class="footer" bgcolor="#44525f">
		          <table width="100%" border="0" cellspacing="0" cellpadding="0">
		            <tr>
		              <td align="center" class="footercopy">
		                '.$data->email_fotter.'
		              </td>
		            </tr>
		            
		          </table>
		        </td>
		      </tr>
		    </table>
		    <!--[if (gte mso 9)|(IE)]>
		          </td>/
		        </tr>
		    </table>
		    <![endif]-->
		    </td>
		  </tr>
		</table>
		</body>
	</html>';
	//echo "daaa";
	//echo $temp; exit;
	$return ['subject'] = $subject;
	$return ['mesg'] = $temp;
	$return ['sender'] =  $head->admin_email;
	return $return;
	}
	function emaildatareplace($data, $mesg){
		global $callConfig;
		$mes1= strpos($mesg,'{')+1;
		$mes2= strpos($mesg,'}');
		if($mes2 == ''){ $mes2=0; }
		//echo "11qww".$mes1."ppp".$mes2;
		if($mes1>0 && $mes2>0){
			$str =''; $sstr ='';
			$str = substr($mesg,$mes1,$mes2-$mes1);
			$sstr = "{".$str."}";
			$mesg = str_replace($sstr, $data[$str], $mesg);
			$mesg = $this->emaildatareplace($data, $mesg);	
		} 
		return $mesg;
	}
	function emailnumberschange($mesg){
		global $callConfig;
		$mes1= strpos($mesg,'[')+1;
		$mes2= strpos($mesg,']');
		if($mes2 == ''){ $mes2=0; }
		//echo "11qww".$mes1."ppp".$mes2;
		if($mes1>0 && $mes2>0){
			$str =''; $sstr ='';
			$str = substr($mesg,$mes1,$mes2-$mes1);
			$sstr = "[".$str."]";
			$mesg = str_replace($sstr, $this->numberToWordFormateRupees($str), $mesg);
			$mesg = $this->emaildatareplace($data, $mesg);	
		} 
		return $mesg;
	}
	function numberToWordFormateRupees($number){
		global $callConfig;
	   $no = floor($number);
	   $point = round($number - $no, 2) * 100;
	   $hundred = null;
	   $digits_1 = strlen($no);
	   $i = 0;
	   $str = array();
	   $words = array('0' => '', '1' => 'one', '2' => 'two',
	    '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
	    '7' => 'seven', '8' => 'eight', '9' => 'nine',
	    '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
	    '13' => 'thirteen', '14' => 'fourteen',
	    '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
	    '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
	    '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
	    '60' => 'sixty', '70' => 'seventy',
	    '80' => 'eighty', '90' => 'ninety');
	   $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
	   while ($i < $digits_1) {
	     $divider = ($i == 2) ? 10 : 100;
	     $number = floor($no % $divider);
	     $no = floor($no / $divider);
	     $i += ($divider == 10) ? 1 : 2;
	     if ($number) {
	        $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
	        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
	        $str [] = ($number < 21) ? $words[$number] .
	            " " . $digits[$counter] . $plural . " " . $hundred
	            :
	            $words[floor($number / 10) * 10]
	            . " " . $words[$number % 10] . " "
	            . $digits[$counter] . $plural . " " . $hundred;
	     } else $str[] = null;
	  }
	  $str = array_reverse($str);
	  $result = implode('', $str);
	  $points = ($point) ?
	    ". " . $words[$point / 10] . " " . 
	          $words[$point = $point % 10] : '';
	    if($result !=''){
	    	$result = $result . "Rupees  ";
	    }
	    if($points !=''){
	    	$result.= $points . " Paise  ";
	    }
	  return ucwords($result." only");
	}
	function getplaceofsupply($company_id){
		global $callConfig;
		$query = "select  * from states where country_id in ( select base_country from companies where company_id =".$company_id.")";
		$data = $callConfig->getAllRows($query);
		return $data;
	}
	function notificationRead($id, $user_id){
		global $callConfig;
		$query = "select  * from ".TBL_NOTIFICATIONS." where id in ( select notification_id from ".TBL_NOTIFICATON_USER." where id =".$id.")";
		//echo $query; exit;
		$data = $callConfig->getRow($query);
		$query1 = "select  id from ".TBL_NOTIFICATIONS." where  application_table_name = '".$data->application_table_name."' AND transaction_number ='".$data->transaction_number."'";
		//echo $query1; exit;
		$datas = $callConfig->getAllRows($query1);
		foreach ($datas as $val) {
			$dd[]=$val->id;
		}
		$imp = implode(",", $dd);
		echo $imp;
		$lsquery = "UPDATE ".TBL_NOTIFICATON_USER." SET read_status =1 WHERE notification_id in (".$imp.") AND user_id = ".$user_id;
		//echo $lsquery; exit;
		$res = $callConfig->executeQuery($lsquery);
		return $res;

	}
	function ToCheckStatusApprovalLog($post){
		global $callConfig;
		//print_r($post); exit;
		$data['transaction_type'] = $post['table'];
		$data['total_amount'] = $post['amount'];
		$data['status'] = $post['response_type'];
		$data['status_old_value'] = $post['status_old'];
		$post['transaction_type'] = $post['table'];
		$post['Rid'] = $post['hid'];
		$post['updated_by'] = $post['user_id'];
		$workflow = $this->checkhierarchicallevel($post);
		//print_r($workflow); exit;
		if($workflow['doa'] == 1){
			$res = $this->tocheckDAOValues($data);
			$return = $post['status_old'];
			//print_r($res); exit;
			if($res->doa_condition == 1){
				$where = "transaction_table = '".$post['table']."' AND transaction_id='".$post['hid']."' AND send_back_flag=0 AND doa_id != 0 ";	
				$query=$callConfig->selectQuery(TBL_TRANSACTION_APPROVAL_LOGS,'*',$where,'','','');
				$maindata = $callConfig->getAllRows($query);
				$count= count($maindata);
				$doaval = $res->approval_type;
				$doanum = $res->approval_number;
				foreach ($maindata as $mvalue) {
					//if($mvalue->response_type == 13){	$val['sent_back'][] = $mvalue->id;	}
					
					if($mvalue->response_type == ST_REJECTED){	$val['reject'][]  =	$mvalue->id;	}
					else if($mvalue->response_type == 0){	$val['pending'][] =	$mvalue->id;	}
					else{	$val['approve'][] =	$mvalue->id;	}
				}
				if($doaval == 'A'){
					if($count == count($val['approve'])){ $return = ST_APPROVED; }
					if(count($val['reject'])>0){ $return = ST_REJECTED; }
				}
				if($doaval == 'M'){
				   
					if($doanum <= count($val['approve']) ){ $return = ST_APPROVED;  }
					elseif (count($val['reject'])<= 0) {
						$return = $post['status_old'];
					}else{
						$check = count($val['approve'])+count($val['pending']);
						if($check < $doanum ){ $return = ST_REJECTED; }
					}
					 //echo $count."|||".$return; 
				}
				if($doaval == 'L'){
					$return = $post['response_type'];
				}
			}else{
				$return = $post['response_type'];
			}
		}
		if($res->doa_condition == 0){
			$return = $post['response_type'];
		}
		if($res->doa_condition == 2){
			$return = ST_APPROVED;
		}
		return $return;
	}
	function ToInserttransactions($data){
		print_r($data); //exit;
		global $callConfig;
		$fieldarray  = $this->toGetInvoicetables();
		$vendors = explode(",", $data['vender_id']);
		$transaction_id = $data['tranid'];
		$transaction_table = $data['table'];
		$insert_table = $data['new_table'];
		$whr = "id = '".$transaction_id."'";	
		$query1=$callConfig->selectQuery($transaction_table,'*',$whr,'','','');
		$post = $callConfig->getRow($query1);
		//print_r($post);
		$fieldnames=array('company_id'=>$post->company_id,'transaction_number'=>"",'transaction_date'=>date("Y-m-d"),'currency_id'=>$post->currency_id,'currency_rate'=>$post->currency_rate,'originator'=>$data['user_id'],'customer_id'=>"",'customer_name'=>"",'customer_email'=>"",'customer_tax_number'=>"",'customer_phone'=>"",'customer_address'=>"",'customer_ship_adderss'=>"",'place_of_supply'=>$post->place_of_supply,'payment_terms'=>$post->payment_terms,'notes'=>$post->notes,'printing_notes'=>$post->printing_notes,'amount'=>$post->amount,'tax_amount'=>$post->tax_amount,'type'=>$post->type,'tax_type'=>$post->tax_type, 'discount_type'=>$post->discount_type,'discount_value'=>$post->discount_value,'discount_amount'=>$post->discount_amount, 'total_amount'=>$post->total_amount, 'shipping_tax'=>$post->shipping_tax,'shipping_amount'=>$post->shipping_amount,'shipping_tax_amount'=>$post->shipping_tax_amount,'tax_state'=>$post->tax_state,'opex_cpex'=>$post->opex_cpex,'ref_type'=>$transaction_table,'ref_id'=>$transaction_id,'ref_no'=>$post->transaction_number,'ref_date'=>$post->transaction_date,'cost_center_id'=>$post->cost_center_id,'current_status'=> ST_DRAFT);
		//print_r($fieldnames); print_r($vendors); exit;
		
		foreach ($vendors as $vendor) {
			$transation = $this->toGetApplicationSerialNumber($insert_table);
			$fieldnames['transaction_number'] = $transation;
			$where2 = "id =". $vendor;	
			$query2=$callConfig->selectQuery(TBL_VENDOR,'*',$where2,'','','');
			//echo $query2; exit;
			$data = $callConfig->getRow($query2);
			$fieldnames['customer_id'] = $vendor;
			$fieldnames['customer_name'] = $data->display_name;
			$fieldnames['customer_email'] = $data->email;
			$fieldnames['customer_tax_number'] = $data->vendor_tax_reg_no;
			$fieldnames['customer_phone'] = $data->phone;
			$fieldnames['customer_address'] = $data->billing_address;
			$fieldnames['customer_ship_adderss'] = $data->shipping_address;
				//print_r($fieldnames); exit;
			$insert_data=$callConfig->insertRecord($insert_table,$fieldnames);
			$insert_id = $insert_data['LASTID'];
			$whe = "transaction_id = '".$transaction_id."'";	
			$query_item=$callConfig->selectQuery($fieldarray[$transaction_table]['item'],'*',$whe,'','','');
			$item_datas = $callConfig->getAllRows($query_item);
			foreach ($item_datas as $key => $item_data) {
				$itemfieldnames=array('company_id'=>$item_data->company_id,'transaction_id'=>$insert_id,'item_id'=>$item_data->item_id,'account_code'=>$item_data->account_code,'item_name'=>$item_data->item_name,'description'=>$item_data->description,'purchase_uom'=>$item_data->purchase_uom,'item_quantity'=>$item_data->item_quantity,'item_price'=>$item_data->item_price,'item_amount'=>$item_data->item_amount,'tax_amount'=>$item_data->tax_amount,'discount_amount'=>$item_data->discount_amount,'total_amount'=>$item_data->total_amount);
				//6002print_r($fieldarray[$insert_table]); print_r($itemfieldnames); exit;
				$item_id=$callConfig->insertRecord($fieldarray[$insert_table]['item'],$itemfieldnames);
			}
			$wher = "transaction_id = '".$transaction_id."' AND is_active = 0";	
			$query_tax=$callConfig->selectQuery($fieldarray[$transaction_table]['tax'],'*',$wher,'','','');
			//echo $query_tax; exit;
			$tax_data = $callConfig->getAllRows($query_tax);
			if(count($tax_data)>0){
			foreach ($tax_data as $tax_data) {
				$taxfield=array('company_id'=>$tax_data->company_id,'transaction_id'=>$insert_id,'transaction_item_id'=>$tax_data->transaction_item_id,'tax_name'=>$tax_data->tax_name,'tax_value'=>$tax_data->tax_value,'tax_id'=>$tax_data->tax_id,'amount'=>$tax_data->amount,'tax_amount'=>$tax_data->tax_amount,'total_amount' =>$tax_data->total_amount,'is_active'=>0);
				$itax_id=$callConfig->insertRecord($fieldarray[$insert_table]['tax'],$taxfield);
			}
			}
		}
		return $item_datas;
	}
	function togetpcdata($companyid,$ownerid){
        global $callConfig; //echo "<pre>";
        $whr1 = "company_id = '".$companyid."' AND owner_id='".$ownerid."' AND is_active = 0";	
		$query1=$callConfig->selectQuery(TBL_PETTY_CASH_BOX_OWNER,'id,authorized_expenses,pc_limit,pc_box_id',$whr1,'','','');
		//echo $query1; exit;
        $data1 = $callConfig->getAllRows($query1);
       	//print_r($data1); exit;
       // $dates = $this->toGetFrequencyDate($data1->topup_start_date, $data1->topup_frequency);
        $returns = array();
        foreach ($data1 as $detail1) {
        	$return = array();
        	$return ['id'] = $detail1->id;
        	$return ['pc_limit'] = $detail1->pc_limit;
        	$whr2 = "company_id = '".$companyid."' AND id='".$detail1->authorized_expenses."' AND is_active = 0";	
			$query2 =$callConfig->selectQuery(TBL_PETTY_CASH_EXPENSES,'id,expense_name,description,gl_account',$whr2,'','','');
			//echo $query2; exit;
	        $data2 = $callConfig->getRow($query2);
	        $whr3 = "company_id = '".$companyid."' AND id='".$detail1->pc_box_id."' AND is_active = 0";	
			$query3 =$callConfig->selectQuery(TBL_PETTY_CASH_BOXES,'id,topup_frequency,topup_start_date',$whr3,'','','');
			//echo $query3; exit;
       		$data3 = $callConfig->getRow($query3);

       		$dates = $this->toGetFrequencyDate($data3->topup_start_date, $data3->topup_frequency);
	        $return ['expenss_id'] = $data2->id;
        	$return ['expense_name'] = $data2->expense_name;
        	$return ['expenss_description'] = $data2->description;
        	$return ['expense_gl_account'] = $data2->gl_account;
        	$return ['consume'] = 8500;
        	$return ['available'] = $return ['pc_limit'] - $return ['consume'];
        	$return ['republish_date'] = date("d-m-Y",strtotime('2020-05-26'));
        	$return ['expense_gl_account'] = $data2->gl_account;
        	$returns[] = $return;
        }  
        //print_r($returns); exit;
        /*
        $query2 = "SELECT max(`transaction_date`) as max_date FROM petty_cash_transactions WHERE company_id =$companyid AND pc_owner_id = $ownerid AND `pc_transaction_type` = 1";
        $data2 = $callConfig->getRow($query2);
        $todate = date("Y-m-d");
	    $query3 = "SELECT `expense_code`,`transaction_amount`,`pc_transaction_type` FROM `petty_cash_transactions` WHERE `company_id` = $companyid AND `pc_owner_id` = $ownerid AND `transaction_date` >= '$data2->max_date' AND transaction_date <= '$todate' AND `pc_transaction_type` = 0 ";
        $data3 = $callConfig->getAllRows($query3);*/
        //print_r($data);
        //$return = array();togetprimarykey
		/*$data =array_merge($data1,$data3);*/
		/*echo "<pre>";
		print_r($data1);
		//print_r($data2);
		print_r($data3);
		print_r($data);
		exit();
        foreach ($data as  $value) {
            $return [] = $value->column_name;
        }*/
        return $returns;
    }
    function toGetFrequencyDate($fdate, $frequency){
    	global $callConfig; 
    	$days = $frequency != 0 ? 30/$frequency : 0;
    	//echo $days;
    	$dates = explode("-", $fdate);
    	//print_r($dates);
    	$f_date = $dates['2'];
    	$new_date = date('d');
    	$new_month = date('m');
    	$new_year = date('Y');
    	for($i =0; $i<$frequency; $i++){
    		
    		if($f_date >31){
    			$f_date =  $f_date;
    			$new_month = $new_month+1;
    			if($new_month > 12){
    				$new_month =1;
    				$new_year = $new_year+1;
    			}
    		}
    		$date[$i]['from'] =$new_year."-".$new_month."-".$f_date;
    		$f_date = $f_date+$days;
    		if($f_date >31){
    			$f_date =  $f_date;
    			$new_month = $new_month+1;
    			if($new_month > 12){
    				$new_month =1;
    				$new_year = $new_year+1;
    			}
    		}
    		$date[$i]['to'] =$new_year."-".$new_month."-".$f_date;
    		if((strtotime($date[$i]['from']) < strtotime(date("Y-m-d")) )&&( strtotime(date("Y-m-d")) < strtotime($date[$i]['to']))){
    			$return = $date[$i];
    		}
    		//echo $f_date;

    	}
    	//print_r($date);
    	//echo "F date:".$fdate."###".$frequency; exit;
    	return $return;
    }
    function insertBills($post,$type){
    	global $callConfig; 
    	//echo "<pre>"; print_r($post); exit;
    	//echo $post['invoiced_at'];
    	$table = 'bills';
    	$bill_items = 'bill_items';
		$bill_item_taxes= 'bill_item_taxes';
    	if($post['old_place'] == $post['place_of_supply']){ 
			$tax_flag = "S";
		}else{
			$tax_flag = "I";
		}
		$status = $post['status_values'] != '' ? $post['status_values'] : ST_DRAFT ;
		$status_main = $status;
		if($type == 'update' ){
			$sarray  = array('comments' => $post['comments'], 'response_type' => $status, 'user_id' => $post['updated_by'], 'response_date' => date("Y-m-d"),'table' => $post['table_name'],'hid' => $post['hid'],'amount'=>$post['grandtotal'],'status_old' => $post['status_old_value']);
			$status = $this->updateapprovallog($sarray);
			//print_r($sarray); exit;
		} 	
    	$invoiced_at = str_replace('/"', '-', $post['invoiced_at']);
        $invoiced_at =  date('Y-m-d', strtotime($invoiced_at));
        $doc_ref_date = str_replace('/"', '-', $post['doc_ref_date']);
        $doc_ref_date =  date('Y-m-d', strtotime($doc_ref_date));
        $due_date = str_replace('/"', '-', $post['due_date']);
        $due_date =  date('Y-m-d', strtotime($due_date)); 
    	$fieldnames=array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_date'=>$invoiced_at,'currency_id'=>$post['currency_id'],'currency_rate'=>$post['currency_rate'],'party_id'=>$post['customer_id'],'party_name'=>$post['customer_name'],'party_email'=>$post['customer_email'],'party_tax_number'=>$post['customer_tax_number'],'party_phone'=>$post['customer_phone'],'party_address'=>$post['customer_address'],'party_ship_adderss'=>$post['customer_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'notes'=>$post['notes'],'amount'=>$post['subTotal'],'tax_amount'=>$post['taxRate'], 'discount_type'=>$post['discount_type'],'discount_value'=>$post['discount_value'],'discount_amount'=>$post['discount_amount'], 'total_amount'=>$post['grandtotal'], 'current_status'=> $status, 'ref_id'=>$post['ref_id'], 'ref_no'=>$post['ref_no'], 'ref_date'=>date("Y-m-d",strtotime($post['ref_date'])),'ref_type'=>$post['ref_date'],'originator'=>$post['originator'],'printing_notes'=>$post['printing_notes'],'type'=>$post['type'],'tax_type'=>$post['tax_type'],'shipping_tax'=>$post['shipping_tax'],'shipping_amount'=>$post['shipping_amount'],'shipping_tax_amount'=>$post['shipping_tax_amount'],'printing_notes'=>$post['printing_notes'],'tax_state'=>$tax_flag,'opex_cpex'=>$post['opex_cpex'],'payment_method'=>$post['payment_method'],'doc_ref_no'=>$post['doc_ref_no'],'doc_ref_date'=>$doc_ref_date,'due_date'=>$due_date);
    	$fieldnames['cost_center_id'] = $post['cost_center'];
    	if($type == 'update'){
			$insert_data=$callConfig->updateRecord($table,$fieldnames,'id',$post['hid']);
			$itemids = explode(',', $post['hid_itemds']);
			$insert_id = $post['hid'];
		 }else{
		 	$transation = $this->toGetApplicationSerialNumber('bills');
		 	$fieldnames['transaction_number'] = $transation;
		 	/*echo "<pre>"; print_r($fieldnames); exit;*/
		 	$insert_data=$callConfig->insertRecord('bills',$fieldnames);
			$insert_id = $insert_data['LASTID'];
			//$insert_id =2;
		 }

		 $fieldnames['cost_center_id'] = $post['cost_center'];
		 $email_data = array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_type'=>$table, 'transaction_date'=>$invoiced_at, 'amount'=>$post['subTotal'], 'tax_amount'=>$post['taxRate'] , 'discount_amount'=>$post['discount_amount'] , 'total_amount'=>$post['grandtotal'] ,'action_label'=>'Submit for Approval', 'option'=>'COM_INSERT_BILLS' ,'Rid'=>$insert_id,'email_template'=>1,'status_old_value'=>$post['status_old_value'],'status'=>$status_main,'updated_by'=>$post['updated_by'],'originator'=>$fieldnames['originator'],'opex_cpex'=>'','category_type'=>'','cost_center'=>$post['cost_center']);
		 
		 //echo "<pre>"; print_r($insert_data); //exit;
		 if($insert_id > 0){
			//echo "data"; exit;
			foreach ($post['productCode'] as $key => $item) {
				//echo $item; exit;
				$litemids[] = $item;
			 	$itemfieldnames=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'item_id'=>$item,'item_name'=>$post['item_name'][$key],'description'=>$post['productName'][$key],'purchase_uom'=>$post['uom'][$key],'item_quantity'=>$post['quantity'][$key],'item_price'=>$post['price'][$key],'item_amount'=>$post['amount'][$key],'tax_amount'=>$post['ptax_amount'][$key],'total_amount'=>$post['amount'][$key],'discount_amount'=>$post['pdiscount_amount'][$key],'req_date'=>$post['req_date'][$key]);
			 	//print_r($itemfieldnames); exit;
			 	$taxfield=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'transaction_item_id'=>$item,'tax_id'=>$post['tax'][$key],'amount'=>$post['p_amount'][$key],'tax_amount'=>$post['ptax_amount'][$key],'total_amount' =>$post['amount'][$key],'tax_type' =>$tax_flag, 'tax_value'=>$post['tax'][$key]);
			 	if($type == 'update'){
				 	$where = "transaction_id = '".$insert_id."' AND item_id = '".$item."' AND is_active = 0";
				 	$itemquery=$callConfig->selectQuery($bill_items,'*',$where,'','','');
					$data = $callConfig->getRow($itemquery);
					if($data->description != $itemfieldnames['description'] || $data->purchase_uom != $itemfieldnames['purchase_uom']  || $data->item_quantity != $itemfieldnames['item_quantity'] || $data->item_price != $itemfieldnames['item_price'] ){
						if($data->id !=''){
							$res=$callConfig->updateRecord($bill_items,$itemfieldnames,'id',$data->id);	
						}else{
							$item_id=$callConfig->insertRecord($bill_items,$itemfieldnames);
						}
					}
					$where = "transaction_id = '".$insert_id."' AND transaction_item_id	= '".$item."' AND is_active = 0";
				 	$itemquery=$callConfig->selectQuery($bill_item_taxes,'*',$where,'','','');
					$data_tax = $callConfig->getRow($itemquery);
					if(count($data_tax) > 0){
						if($data->tax_amount != $itemfieldnames['tax_amount']){
							$where = "transaction_id = '".$insert_id."' AND transaction_item_id = '".$item."' AND is_active = 0";
							$res=$callConfig->updateRecord($bill_item_taxes,$taxfield,'','',$where);
						}
					}else{
							$itax_id=$callConfig->insertRecord($bill_item_taxes,$taxfield);
					}
					
				}else{
				 	$item_id=$callConfig->insertRecord($bill_items,$itemfieldnames);
				 	if($post['tax'][$key]!=0){
				 		$itax_id=$callConfig->insertRecord($bill_item_taxes,$taxfield);
				 	}
				 	 //print_r($item_id);  print_r($itax_id); 
			 	}
			}
			if($type == 'update' ){
				$diffid = array_diff($itemids,$litemids);
				 if(count($diffid) > 0){
					 	foreach ($diffid as  $value_id) {
					 		$taxfieldde=array('is_active'=>1);
					 		$wheredd = "transaction_id = '".$insert_id."' AND item_id = '".$value_id."' AND is_active = 0";
					 		$wheredd1 = "transaction_id = '".$insert_id."' AND transaction_item_id = '".$value_id."' AND is_active = 0";
					 		$res=$callConfig->updateRecord($bill_items,$taxfieldde,'','',$wheredd);
					 		$res=$callConfig->updateRecord($bill_item_taxes,$taxfieldde,'','',$wheredd1);
					 	}
				 }
				 if($status > ST_DRAFT){
		 		//echo "data"; exit;
			 		$res =$this->toSendEmailNotificationsBaseDAO($email_data);
			 	}//print_r($itemids); exit;
			 	if($status != ST_CANCEL && $status != ST_DRAFT){
			 		$iu = $this->insertandupdatedataapplicationtag($fieldnames,$insert_id,'COM_INSERT_BILLS');
			 	} 	
			}
		} //exit;
		return $insert_data;
    	/*echo "<pre>"; print_r($insert_data); exit;
    	echo "<pre>"; print_r($post); exit;*/
    }

    function insertPC($post,$type){
    	global $callConfig; 
    	//echo "<pre>"; print_r($post); exit;
    	//echo $post['invoiced_at'];
    	$table = 'petty_cash';
    	$bill_items = 'pc_items';
		$bill_item_taxes= 'pc_item_taxes';
    	if($post['old_place'] == $post['place_of_supply']){ 
			$tax_flag = "S";
		}else{
			$tax_flag = "I";
		}
		$status = $post['status_values'] != '' ? $post['status_values'] : ST_DRAFT ;
		$status_main = $status;
		if($type == 'update' ){
			$sarray  = array('comments' => $post['comments'], 'response_type' => $status, 'user_id' => $post['updated_by'], 'response_date' => date("Y-m-d"),'table' => $post['table_name'],'hid' => $post['hid'],'amount'=>$post['grandtotal'],'status_old' => $post['status_old_value']);
			$status = $this->updateapprovallog($sarray);
			//print_r($sarray); exit;
		} 	
    	$invoiced_at = str_replace('/"', '-', $post['invoiced_at']);
        $invoiced_at =  date('Y-m-d', strtotime($invoiced_at));
        $doc_ref_date = str_replace('/"', '-', $post['doc_ref_date']);
        $doc_ref_date =  date('Y-m-d', strtotime($doc_ref_date));
        $due_date = str_replace('/"', '-', $post['due_date']);
        $due_date =  date('Y-m-d', strtotime($due_date)); 
    	$fieldnames=array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_date'=>$invoiced_at,'currency_id'=>$post['currency_id'],'currency_rate'=>$post['currency_rate'],'party_id'=>$post['customer_id'],'party_name'=>$post['customer_name'],'party_email'=>$post['customer_email'],'party_tax_number'=>$post['customer_tax_number'],'party_phone'=>$post['customer_phone'],'party_address'=>$post['customer_address'],'party_ship_adderss'=>$post['customer_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'notes'=>$post['notes'],'amount'=>$post['subTotal'],'tax_amount'=>$post['taxRate'], 'discount_type'=>$post['discount_type'],'discount_value'=>$post['discount_value'],'discount_amount'=>$post['discount_amount'], 'total_amount'=>$post['grandtotal'], 'current_status'=> $status, 'ref_id'=>$post['ref_id'], 'ref_no'=>$post['ref_no'], 'ref_date'=>date("Y-m-d",strtotime($post['ref_date'])),'ref_type'=>$post['ref_date'],'originator'=>$post['originator'],'printing_notes'=>$post['printing_notes'],'type'=>$post['type'],'tax_type'=>$post['tax_type'],'shipping_tax'=>$post['shipping_tax'],'shipping_amount'=>$post['shipping_amount'],'shipping_tax_amount'=>$post['shipping_tax_amount'],'printing_notes'=>$post['printing_notes'],'tax_state'=>$tax_flag,'opex_cpex'=>$post['opex_cpex'],'payment_method'=>$post['payment_method'],'due_date'=>$due_date);
    	//echo "<pre>"; print_r($fieldnames); exit;
    	$post['cost_center'] = $post['customer_id'];
    	$fieldnames['cost_center_id'] = $post['cost_center'];
    	if($type == 'update'){
			$insert_data=$callConfig->updateRecord($table,$fieldnames,'id',$post['hid']);
			$itemids = explode(',', $post['hid_itemds']);
			$insert_id = $post['hid'];
		 }else{
		 	$transation = $this->toGetApplicationSerialNumber($table);
		 	$fieldnames['transaction_number'] = $transation;
		 	/*echo "<pre>"; print_r($fieldnames); exit;*/
		 	$insert_data=$callConfig->insertRecord($table,$fieldnames);
		 	//echo "<pre>"; print_r($insert_data); exit;
			$insert_id = $insert_data['LASTID'];
			//$insert_id =2;
		 }
         $fieldnames['cost_center_id'] = $post['cost_center'];
		 $email_data = array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_type'=>$table, 'transaction_date'=>$invoiced_at, 'amount'=>$post['subTotal'], 'tax_amount'=>$post['taxRate'] , 'discount_amount'=>$post['discount_amount'] , 'total_amount'=>$post['grandtotal'] ,'action_label'=>'Submit for Approval', 'option'=>'COM_INSERT_PETTY_CASH' ,'Rid'=>$insert_id,'email_template'=>1,'status_old_value'=>$post['status_old_value'],'status'=>$status_main,'updated_by'=>$post['updated_by'],'originator'=>$fieldnames['originator'],'opex_cpex'=>'','category_type'=>'','cost_center'=>$post['cost_center']);
		 
		 //echo "<pre>"; print_r($email_data); exit;
		 if($insert_id > 0){
			//echo "data"; exit;
			foreach ($post['productCode'] as $key => $item) {
				//echo $item; exit;
				$doc_ref_date = $post['doc_ref_date'][$key];
				if($doc_ref_date !=''){
					$doc_ref_date = str_replace('/"', '-',$doc_ref_date);
        			$doc_ref_date =  date('Y-m-d', strtotime($doc_ref_date));
        		}
				$litemids[] = $item;
			 	$itemfieldnames=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'item_id'=>$item,'item_name'=>$post['item_name'][$key],'description'=>$post['productName'][$key],'purchase_uom'=>$post['uom'][$key],'item_quantity'=>$post['quantity'][$key],'item_price'=>$post['price'][$key],'item_amount'=>$post['amount'][$key],'tax_amount'=>$post['ptax_amount'][$key],'total_amount'=>$post['amount'][$key],'discount_amount'=>$post['pdiscount_amount'][$key],'doc_ref_no'=>$post['doc_ref_no'][$key],'doc_ref_date'=>$doc_ref_date,'account_code'=>$post['account_code'][$key]);
			 	//echo "<pre>"; print_r($itemfieldnames); exit;
			 	$taxfield=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'transaction_item_id'=>$item,'tax_id'=>$post['tax'][$key],'amount'=>$post['p_amount'][$key],'tax_amount'=>$post['ptax_amount'][$key],'total_amount' =>$post['amount'][$key],'tax_type' =>$tax_flag, 'tax_value'=>$post['tax'][$key]);
			 	if($type == 'update'){
				 	$where = "transaction_id = '".$insert_id."' AND item_id = '".$item."' AND is_active = 0";
				 	$itemquery=$callConfig->selectQuery($bill_items,'*',$where,'','','');
					$data = $callConfig->getRow($itemquery);
					if($data->description != $itemfieldnames['description'] || $data->purchase_uom != $itemfieldnames['purchase_uom']  || $data->item_quantity != $itemfieldnames['item_quantity'] || $data->item_price != $itemfieldnames['item_price'] ){
						if($data->id !=''){
							$res=$callConfig->updateRecord($bill_items,$itemfieldnames,'id',$data->id);	
						}else{
							$item_id=$callConfig->insertRecord($bill_items,$itemfieldnames);
						}
					}
					$where = "transaction_id = '".$insert_id."' AND transaction_item_id	= '".$item."' AND is_active = 0";
				 	$itemquery=$callConfig->selectQuery($bill_item_taxes,'*',$where,'','','');
					$data_tax = $callConfig->getRow($itemquery);
					if(count($data_tax) > 0){
						if($data->tax_amount != $itemfieldnames['tax_amount']){
							$where = "transaction_id = '".$insert_id."' AND transaction_item_id = '".$item."' AND is_active = 0";
							$res=$callConfig->updateRecord($bill_item_taxes,$taxfield,'','',$where);
						}
					}else{
							$itax_id=$callConfig->insertRecord($bill_item_taxes,$taxfield);
					}
					
				}else{
				 	$item_id=$callConfig->insertRecord($bill_items,$itemfieldnames);
				 	if($post['tax'][$key]!=0){
				 		$itax_id=$callConfig->insertRecord($bill_item_taxes,$taxfield);
				 	}
				 	 //echo "<pre>";print_r($item_id);  print_r($itax_id); exit;
			 	}
			}
			if($type == 'update' ){
				$diffid = array_diff($itemids,$litemids);
				 if(count($diffid) > 0){
					 	foreach ($diffid as  $value_id) {
					 		$taxfieldde=array('is_active'=>1);
					 		$wheredd = "transaction_id = '".$insert_id."' AND item_id = '".$value_id."' AND is_active = 0";
					 		$wheredd1 = "transaction_id = '".$insert_id."' AND transaction_item_id = '".$value_id."' AND is_active = 0";
					 		$res=$callConfig->updateRecord($bill_items,$taxfieldde,'','',$wheredd);
					 		$res=$callConfig->updateRecord($bill_item_taxes,$taxfieldde,'','',$wheredd1);
					 	}
				 }
				 if($status > ST_DRAFT){
		 		//echo "data"; exit;
			 		$res =$this->toSendEmailNotificationsBaseDAO($email_data);
			 	}//print_r($itemids); exit;
			 	if($status != ST_CANCEL && $status != ST_DRAFT){
			 		$iu = $this->insertandupdatedataapplicationtag($fieldnames,$insert_id,'COM_INSERT_PETTY_CASH');
			 	} 	
			}
		} //exit;
		return $insert_data;
    	/*echo "<pre>"; print_r($insert_data); exit;
    	echo "<pre>"; print_r($post); exit;*/
    }



    function getApprovalLogData($data){
    	global $callConfig;
    	//echo "<pre>"; print_r($data); exit;
    	$table = $_GET['table'];
    	$whr = "user_id = '".$data['user_id']."' AND response_type = '0' AND send_back_flag = 0";	
		$query = $callConfig->selectQuery(TBL_TRANSACTION_APPROVAL_LOGS,'DISTINCT(transaction_table) as button',$whr,'','','');
		//echo $query; exit;
       	$data1 = $callConfig->getAllRows($query);
       	//print_r($data); echo $data[0]->button;exit;
       	$return['button'] = $data1;
       	if(count($data) >0){
       		$table = $table!='' ? $table : $data1[0]->button;
       		$return['table'] = $table;
       		$whr2 = "user_id = '".$data['user_id']."' AND response_type = '0' AND send_back_flag = 0 AND transaction_table ='".$table."'";	

			$query2 = $callConfig->selectQuery(TBL_TRANSACTION_APPROVAL_LOGS,'*',$whr2,'','','');
			//echo $query2; exit;
			//echo $query; exit;
	       	$data2 = $callConfig->getAllRows($query2);
	       	//print_r($data2); exit;
	       	foreach ($data2 as  $dat) {
	       		$tran_ids[] = $dat->transaction_id;
	       	}
	       	$tran_id = implode(',', $tran_ids);
	       	//echo $tran_id ; exit;
	       	$fields = "id,transaction_number, transaction_date,  amount, discount_amount, shipping_amount, tax_amount";
       		if($table == 'bills'){
       			$fields.=",party_name";
       		}else{
       			$fields.=",customer_name as party_name";
       		}
	       	$query3 = "SELECT ".$fields." from ".$table." WHERE id in (".$tran_id.")";
	       	//echo $query3; exit;
	       	$data3 = $callConfig->getAllRows($query3);
	       	//echo "<pre>";print_r($data3); exit;
       	}
       	$return['data'] = $data3;
       	return $return;
    }
    function getchartofaccountdetailsuseglaccountuser($data){
    	global $callConfig;
    	$query = "select coa.* from chart_of_accounts coa, gl_account_users gau where coa.company_id = gau.company_id and coa.account_code = gau.account_code and  gau.company_id =".$data['company_id']."  and gau.user_id = ".$data['user_id'];
    	$data = $callConfig->getAllRows($query);
    	return $data;
    }
    function pettycashdetails($data){
    	global $callConfig;
    	$query = "select * From petty_cash_expenses where id in ( select authorized_expenses from petty_cash_box_owner where owner_id =".$data['user_id'].")";
    	$data = $callConfig->getAllRows($query);
    	return $data;
    }
    function budgetcheckreftadledata($data){
    	global $callConfig;
    	//echo"<pre>"; print_r($data); exit;
    	$str = '';
    	foreach ($data['account_code'] as  $code) {
    		$str.="'".$code."',";
    	}
    	$str = trim($str,",");
    	$query= "Select * from budget_balances where company_id=". $data['company_id']." AND cost_center_id=". $data['cost_center_id']." AND fiscal_year='". FISCALYEAR."' AND account_code in (".$str.")";
    	//echo $query;
    	$data1 = $callConfig->getAllRows($query);
    	//echo"<pre>"; print_r($data1); exit; 
    	//echo $data['fiscal_year'];

    	$fy = explode('-', $data['fiscal_year']);
    	$array = array('01'=> '10','02'=> '11','03'=> '12','04'=> '1','05'=> '2','06'=> '3','07'=> '4','08'=> '5','09'=> '6','10'=> '7','11'=> '8','12'=> '9');
    	//print_r($fy); exit;

		$month = $array[$fy[1]];
		$val = 'distribution_m'.$month;
		$val2 = 'rq_m'.$month;

		//echo $val."++".$val2."dafaefav".$data['fiscal_year'];print_r($fy);	 exit;
		$return = array();
		//echo"<pre>"; print_r($data1); exit;
    	foreach ($data1 as  $cudata) {
    		//echo $val.'%%%'.$val2;
    		$response = array();
    		//print_r($cudata);
			$response ['a'] = $cudata->$val - $cudata->$val2;
			$response ['b'] = $cudata->$val;
			$response ['c'] = $cudata->$val2;
			$return[$cudata->account_code] = $response ;
    	}
    	//echo"<pre>"; print_r($return); exit; 
    	return $return;
    }
    function toGetBluckApprovaldata($post){
    	global $callConfig;
    	//echo "<pre>"; print_r($data);exit;
    	$whr1 = "company_id = ".$post['company_id']." AND is_active = 0";	
		$query1=$callConfig->selectQuery(TBL_BULK_APPROVAL_BASE,'id,function_name,description',$whr1,'','','');
		//echo $query1; exit;
		$approval_tables = $callConfig->getAllRows($query1);

		$approval_data = array();$data = array();$return = array(); $gcdata = array();
		$str = '';
		foreach ($approval_tables as $approval_table) {
			$data[$approval_table->function_name]['table']=$approval_table->function_name;
			$data[$approval_table->function_name]['description']=$approval_table->description; 
			$data[$approval_table->function_name]['count']=10;
			$str.= "'".$approval_table->function_name."',";
		}
		$return['data'] = $data;
		$str = trim($str,",");
		$query2="select stp.* from status st , status_progress stp where st.name_value = stp.status and stp.table_name in (".$str." ) order by stp.table_name, st.sequence";
			//echo $query2; exit;
		$status_progress = $callConfig->getAllRows($query2);
		$i=0;
		foreach ($status_progress as  $status_progres) {
			//$status_prog  =  array();
			$status_prog[$status_progres->table_name][$i]['status_progress_id'] = $status_progres->id;
			$status_prog[$status_progres->table_name][$i]['status_name'] = $status_progres->name;
			$status_prog[$status_progres->table_name][$i]['status_action_label'] = $status_progres->action_label;
			$status_prog[$status_progres->table_name][$i]['status_status_id'] = $status_progres->status;
			$i++;
		}
		$return['status'] = $status_prog;
		$whr3 = "company_id = ".$post['company_id']." AND genc_code='TRANSACTIONTYPE' AND is_active = 0";	
		$query3=$callConfig->selectQuery(TBL_GENRAL_CODE_DETAILS,'*',$whr3,'','','');
		//echo $query3; exit;
		$gc_data = $callConfig->getAllRows($query3);
		foreach ($gc_data as $gc) {
			$gcdata[$gc->genc_detail_code] = $gc->description;
		}
		$return['genc_code'] = $gcdata;
		$query4 = "SELECT  bat.transaction_type, `current_status`, count(*) as count1 FROM `bulk_approval_transactions` bat, transaction_approval_logs tal where  bat.company_id = tal.company_id and   bat.transaction_type = 
		tal.transaction_table and  bat.transaction_id = tal.transaction_id  and  tal.user_id =".$post['user_id']." AND tal.response_type = 0 group by bat.transaction_type, bat.current_status";
		//echo $query4; exit;
		$approval_count = $callConfig->getAllRows($query4);
		foreach ($approval_count as $count) {
			$approval_count_data [$count->transaction_type] [$count->current_status] = $count->count1;
		}
		$return['count'] = $approval_count_data;	
		$query4=$callConfig->selectQuery(TBL_USERS,'id,display_name,user_photo','','','','');
		//echo $query3; exit;
		$user_data = $callConfig->getAllRows($query4);
		foreach ($user_data as  $user) {
			$users[$user->id]['name'] = $user->display_name;
			$users[$user->id]['photo'] = $user->user_photo;
		}
		$return['user'] = $users;
		//echo "<pre>"; print_r($approval_count_data); exit;
		return $return;
    }
    function toGetBluckStatusData($data){
    	global $callConfig;
    	//echo "<pre>"; print_r($data);
    	$query2="SELECT bat.id, bat.company_id, bat.transaction_type, bat.transaction_id, bat.cost_center_id, bat.transaction_number, bat.transaction_date, bat.currency_id, bat.currency_rate, bat.originator, bat.customer_id, bat.customer_name, bat.customer_email, bat.customer_tax_number, bat.customer_phone, bat.customer_address, bat.customer_ship_adderss, bat.place_of_supply, bat.payment_terms, bat.notes, bat.printing_notes, bat.parent_id, bat.amount, bat.tax_amount, bat.type, bat.tax_type, bat.discount_type, bat.discount_value, bat.discount_amount, bat.total_amount, bat.shipping_tax, bat.shipping_amount, bat.shipping_tax_amount, bat.tax_state, bat.opex_cpex, bat.ref_type, bat.ref_id, bat.ref_no, bat.ref_date, bat.old_ref_no, bat.old_ref_date, bat.current_status, tal.id, tal.`response_type`, tal.`user_id`, tal.`request_date`, tal.`send_back_flag`, tal.`response_date`, tal.`comments`, tal.`doa_id` FROM `bulk_approval_transactions` bat, transaction_approval_logs tal where bat.company_id = tal.company_id and    bat.transaction_type = tal.transaction_table and bat.transaction_id = tal.transaction_id and bat.current_status='".$data['status']."' and bat.transaction_type ='".$data['transaction_type']."' and  tal.user_id=".$data['user_id']." AND tal.response_type = 0" ;
		//echo $query2; exit;
		$data = $callConfig->getAllRows($query2);
		return $data;
    }
    function upadateblukapproval($data){
    	global $callConfig;
    	$ids = trim($data['rid'],',');
    	$table = $data['table'];
    	$status = $data['status'];
    	$ids = explode(",", $ids);
    	//print_r($ids); exit;
    	foreach ($ids as $id) {
    		$array['table_name'] = $table; 
    		$array['status'] = $status; 
    		$array['hid'] = $id; 
    		$array['comments'] = '';
    		$array['user_id'] = $data['user_id'];
    		$array['company_id'] = $data['company_id'];
    		//print_r($array); exit;
    		$res = $this->upadteablukprrovals($array); 
    	}
    	return $res;
    }
    function upadteablukprrovals($post){
    	global $callConfig;
    	$where = "id = '".$post['hid']."'";
		$itemquery=$callConfig->selectQuery($post['table_name'],'*',$where,'','','');
		$data = $callConfig->getRow($itemquery);
    	//$status_main = $data->current_status;
		$sarray  = array('comments' => $post['comments'], 'response_type' => $post['status'], 'user_id' => $post['user_id'], 'response_date' => date("Y-m-d"),'table' => $post['table_name'],'hid' => $post['hid'],'amount'=>$data->total_amount,'status_old' => $data->current_status);
		$status = $this->updateapprovallog($sarray);
		//print_r($sarray); echo $post['table_name']; exit;
		$suc_data=$callConfig->updateRecord($post['table_name'],array('current_status' => $status),'id',$post['hid']);
		$wheredd1 = "transaction_id = '".$post['hid']."' AND transaction_type = '".$post['table_name']."'";
		$res=$callConfig->updateRecord('bulk_approval_transactions',array('current_status' => $status),'','',$wheredd);
    	$email_data = array('company_id'=>$post['company_id'],'transaction_number'=>$data->transaction_number,'transaction_type'=>$post['table_name'], 'transaction_date'=>$data->transaction_date, 'amount'=>$data->amount, 'tax_amount'=>$data->tax_amount , 'discount_amount'=>$data->discount_amount, 'total_amount'=>$data->total_amount ,'action_label'=>'Submit for Approval', 'option'=>'COM_LIST_BLUK_APPROVAL_TRANSACTIONS' ,'Rid'=>$data->id,'email_template'=>1,'status_old_value'=>$data->current_status,'status'=>$post['status'],'updated_by'=>$post['user_id'],'originator'=>$data->originator,'opex_cpex'=>$data->opex_cpex,'category_type'=>$data->type,'cost_center'=>$data->cost_center_id);
    	//print_r($email_data); exit;
    	if($status > ST_DRAFT){
		 	//echo "data"; exit;
			 $res =$this->toSendEmailNotificationsBaseDAO($email_data);
		}//print_r($itemids); exit;]
		return $res;
    }
    function addAndUpdateBulkApprovalTransaction($data , $type){
    	global $callConfig;
    	//print_r($data); exit;
		if($type == 'insert'){
			$return = $callConfig->insertRecord('bulk_approval_transactions',$data);
		}else{
			$wheredd = "transaction_id = '".$data['transaction_id']."' AND transaction_type = '".$data['transaction_type']."'";
			//echo 
			$return = $callConfig->updateRecord('bulk_approval_transactions',$data,'','',$wheredd);
		}
		return $return;
	}
	function toGetPettyCashSummary($post){
		global $callConfig;
		//echo "ravi"; exit;
		$query = "SELECT po.`company_id`, po.`pc_box_id`, po.`owner_id`, po.`pc_type_id`, po.`currency_id`, po.`pc_limit`, po.`assigner_id`, po.`authorizer_id`, po.`authorized_expenses`, po.`gl_account_assignment`, po.`gl_account_pc_cash`, po.`tax_group_id`, po.`is_active`, pc.expense_name FROM `petty_cash_box_owner` po , `petty_cash_expenses` pc
WHERE po.`company_id` =  pc.company_id and po.`authorized_expenses` = pc.id  and po.`owner_id` = ".$post['user_id']." and po.`company_id` =  ".$post['company_id']."  and po.is_active = pc. is_active and po.is_active = 0";
		//$query = "SELECT `company_id`, `pc_box_id`, `owner_id`, `pc_type_id`, `currency_id`, `pc_limit`, `assigner_id`, `authorizer_id`, `authorized_expenses`, `gl_account_assignment`, `gl_account_pc_cash`, `tax_group_id`, `is_active` FROM `petty_cash_box_owner` WHERE `company_id` =  ".$post['company_id']." and `owner_id` = ".$post['user_id']." and is_active = 0";
		$data1 = $callConfig->getAllRows($query);
		//echo "<pre>"; print_r($data1); exit;
		$query2 = "select pc.id,pc.transaction_number, pc.transaction_date, pci.item_id, pci.item_name, pci.item_amount from petty_cash pc, pc_items pci where pc.company_id = pci.company_id and pc.id = pci.transaction_id and pc.company_id = ".$post['company_id']."  and pc.originator = ".$post['user_id'];
		$data2 = $callConfig->getAllRows($query2);
		//echo "<pre>"; print_r($data2); exit;
		$expense_data = array();  $ex_data = array();
		foreach ($data1 as $owner) {
			$expense = array();
			$expense['id'] = $owner->authorized_expenses;
			$expense['pc_limit'] = $owner->pc_limit;
			$expense['name'] = $owner->expense_name;
			$expense_data[] = $expense;
			//$ex_data[$owner->authorized_expenses] = array();
		}
		foreach ($data2 as $value) {

			$ex_data[$value->id][$value->item_id] = $value->item_amount;
			$message[$value->id] ['tr_no']= $value->transaction_number;
			$message[$value->id] ['tr_date']= $value->transaction_date;
			$message[$value->id] ['total']= 0;

			//$sum_arry[$value->item_id] = 0;
		}
		foreach ($ex_data as $key=>$exx) { //echo $key; //print_r($exx);
			$ex_de = array();
			$total = 0;
			foreach ($expense_data as  $ex) {
				
				$val = $exx[$ex['id']] !="" ? $exx[$ex['id']] : "0.00";
				$ex_de[$ex['id']]= $val;

				$sum_arry[$ex['id']] = $sum_arry[$ex['id']]+$val;
				$total = $total+$val;
			}	
			$message[$key] ['total']= $total;
			//echo"<pre>"; print_r($ex_de);
			$ex_detal[$key] = $ex_de;
		}

		//echo "<pre>"; print_r($expense_data);print_r($ex_detal);print_r($message);print_r($sum_arry); exit;
		$return = array();
		$return['header'] = $expense_data;
		$return['data'] = $ex_detal;
		$return['message'] = $message;
		$return['sum'] = $sum_arry;
		return $return;
	}
	function querynotificationandemail($data){
		global $callConfig;
		//print_r($data); exit;
		if($data['new_reply'] == 'N'){
			$notarry['notification_message'] = ucwords(str_replace('_', ' ',$data['table_name']))." ".$data['trno']. ": ".ucwords("query is raised") ;
		}else{
			$notarry['notification_message'] = ucwords(str_replace('_', ' ',$data['table_name']))." ".$data['trno']. ": ".ucwords("query is replied") ;
		}
		$notarry['notification_users'] = "1";
		$notarry['id'] = $data['transaction_id'];
		$notarry['option'] = $data['option'];
		$notarry['notification_datetime']=date("Y-m-d H:i:s");
		$jsnot= json_encode($notarry);
		$NOTIfieldnames=array('notification_message'=>$notarry['notification_message'],'notification_details'=>$jsnot,'notification_datetime'=>date("Y-m-d H:i:s"));
		$noti_res=$callConfig->insertRecord(TBL_NOTIFICATIONS,$NOTIfieldnames);
		$notification_id = $noti_res['LASTID'];
		$revalue = 'ACTION';
		$nti_use_fieldnames=array('user_id'=>$data['send_id'],'notification_id'=>$notification_id,'notification_type'=>$revalue);
		$res=$callConfig->insertRecord(TBL_NOTIFICATON_USER,$nti_use_fieldnames);
		$data['email_template'] =  2;
		$data['email_content_id'] = 5;
		$data['transaction_type'] = $data['table_name'];
		$data['transaction_number'] = $data['trno'];
		$email_array = $this->emailtemplate($data);
		$file_name = 'email'.'_'.time().uniqid().'.'.'html';
		$email_array ['content'] = $file_name;
		$email_array['revicer'] = $emails;
		$email_array['email_type'] = 1;
		$email = $email_array['mesg'];
		//print_r($email_array); exit;
		$file_path='uploads/email/'.$file_name;
		file_put_contents($file_path, $email);
		fclose($file_path);
		$lenn = $this->sendEmailslist($email_array);
		return $res;
	}
	/*function toGetTaxrates(){
		global $callConfig;
		
	}*/
	function multiplefileupload($post){
		global $callConfig;
		foreach ($post as $value) {
			$res=$callConfig->insertRecord(TBL_APPLICATION_ATTACHMENTS,$value);
		}
		return $res;
	}
	function serchforpos($post){
		global $callConfig;
		$vendor_id = $post['vendor_id']!='' ? $post['vendor_id'] : 'NULL';
		$cost_center_id = $post['cost_center_id']!='' ? $post['cost_center_id'] : 'NULL';
		$category_id = $post['category_id']!='' ? $post['category_id'] : 'NULL';
		$from = 'NULL';
		$to = 'NULL';
		if($post['from'] !=""){
			$from = str_replace('/"', '-', $post['from']);
	       	$from =  date('Y-m-d', strtotime($from));
       	}
		if($post['to'] !=""){
			$to = str_replace('/"', '-', $post['to']);
	       	$to =  date('Y-m-d', strtotime($to));
       	}
		$current = date('Y-m-d');
		//echo $vendor_id;
		if($post['table'] == 'goods_receipt_note'){
		$query = "select distinct   po.id, po.customer_id, (SELECT  display_name FROM  vendors WHERE vendors.id = po.customer_id) vendor_name,
        po.transaction_number, po.transaction_date, po.total_amount From  goods_receipt_notes  po,grn_items poitm WHERE  po.company_id = poitm.company_id AND  po.id = poitm.transaction_id   AND  po.customer_id = IFNULL(".$vendor_id.", po.customer_id) AND    po.cost_center_id  = IFNULL(".$cost_center_id.", po.cost_center_id) AND   poitm.item_id  in ( SELECT  id  FROM   items itm WHERE    category_id = IFNULL(".$category_id.", itm.category_id) ) AND  (po.transaction_date >= IFNULL(".$from.", '1900-01-10') AND po.transaction_date <= IFNULL(".$to.",'".$current."' )) ORDER BY  vendor_name, po.transaction_date asc";
    	}elseif($post['table'] == 'stock_request'){
    		$query = "select distinct   po.id, po.customer_id, po.transaction_number, po.transaction_date, po.total_amount, po.transfer_location_id From   stock_requests po,strq_items poitm WHERE  po.company_id = poitm.company_id AND  po.id = poitm.transaction_id AND   po.transfer_location_id = IFNULL(".$vendor_id.", po.transfer_location_id) AND    po.cost_center_id  = IFNULL(".$cost_center_id.", po.cost_center_id) AND   poitm.item_id  in ( SELECT  id  FROM   items itm WHERE    category_id = IFNULL(".$category_id.", itm.category_id) ) AND  (po.transaction_date >= IFNULL(".$from.", '1900-01-10') AND po.transaction_date <= IFNULL(".$to.",'".$current."' )) ORDER BY  po.transaction_date asc";
    	}elseif($post['table'] == 'stock_transfer'){
    		$query = "select distinct   po.id, po.customer_id, po.transaction_number, po.transaction_date, po.total_amount, po.transfer_location_id From   stock_transfers po,sttr_items poitm WHERE  po.company_id = poitm.company_id AND  po.id = poitm.transaction_id AND   po.transfer_location_id = IFNULL(".$vendor_id.", po.transfer_location_id) AND    po.cost_center_id  = IFNULL(".$cost_center_id.", po.cost_center_id) AND   poitm.item_id  in ( SELECT  id  FROM   items itm WHERE    category_id = IFNULL(".$category_id.", itm.category_id) ) AND  (po.transaction_date >= IFNULL(".$from.", '1900-01-10') AND po.transaction_date <= IFNULL(".$to.",'".$current."' )) ORDER BY  po.transaction_date asc";
    	}else{
    		$query = "select distinct   po.id, po.customer_id, (SELECT  display_name FROM  vendors WHERE vendors.id = po.customer_id) vendor_name,
        po.transaction_number, po.transaction_date, po.total_amount From  purchase_orders  po,po_items poitm,po_item_taxes poitmtax WHERE  po.company_id = poitm.company_id AND  po.id = poitm.transaction_id AND   poitm.company_id = poitmtax.company_id AND   poitm.transaction_id = poitmtax.transaction_id AND  poitm.item_id = poitmtax.transaction_item_id AND  po.customer_id = IFNULL(".$vendor_id.", po.customer_id) AND    po.cost_center_id  = IFNULL(".$cost_center_id.", po.cost_center_id) AND   poitm.item_id  in ( SELECT  id  FROM   items itm WHERE    category_id = IFNULL(".$category_id.", itm.category_id) ) AND  (po.transaction_date >= IFNULL(".$from.", '1900-01-10') AND po.transaction_date <= IFNULL(".$to.",'".$current."' )) ORDER BY  vendor_name, po.transaction_date asc";
    	}
        //echo $query; exit;
        $data = $callConfig->getAllRows($query);
        //print_r($data); exit;
        return $data;
	}
	function togetuserpettycashboxdetails($post){
		global $callConfig;
		$query = "SELECT * FROM `petty_cash_boxes` WHERE id IN(SELECT DISTINCT(`pc_box_id`) FROM `petty_cash_box_owner` WHERE `owner_id` = ".$post['user_id'].")";
		$data = $callConfig->getAllRows($query);
        //print_r($data); exit;
        return $data;
	}
	function pettycashbranchamount($post){
		global $callConfig;
		$whrs="pc_box_id= '".$post['branch_id']."' AND owner_id = '".$post['owner_id']."' AND pc_type_id='".$post['category_id']."'";
		//echo $whrs;
		$query=$callConfig->selectQuery('petty_cash_box_owner','*',$whrs,'','','');
		$data = $callConfig->getRow($query);
		$return = $data->pc_limit;
		$whr1="id= '".$post['branch_id']."'";
		$query1=$callConfig->selectQuery('petty_cash_boxes','*',$whr1,'','','');
		$data1 = $callConfig->getRow($query1);
		if($return == ""){
			$return = $data1->topup_amount;
		}
		//print_r($data);
		if($data->topup_type == 2){
	       	$dates = $this->toGetFrequencyDate($data1->topup_start_date, $data1->topup_frequency);
	       	//print_r($dates); exit;
			/*$query3 = "SELECT   pc.transaction_type, pc.company_id,  pc.transaction_number,  pc.transaction_date,  pc.originator, pc.customer_id, null item_id,  pc.amount  FROM  petty_cash pc, pc_items pcitm WHERE pc.id = pcitm.transaction_id and  pc.company_id = pcitm.company_id  and   pc.transaction_date BETWEEN ".$dates['from']." and  ".$dates['to']." and   pc.orginator =  ".$post['owner_id']." and   pc.branch_code = p_branch_code";*/
			$query3 = "SELECT sum(pc.amount) as tatal FROM petty_cash pc WHERE  pc.transaction_date BETWEEN '".$dates['from']."'' and  '".$dates['to']."'' and   pc.originator =  ".$post['owner_id']." and   pc.branch_code =".$post['branch_id'];
			//echo $query3; exit;
			$data3 = $callConfig->getRow($query3);
			if($data3->tatal == ""){
				$return = $return;
			}else{
				$return = $data3->tatal;
			}
		}
		return $return;
	}
	function getpettycashbuget($post){
		global $callConfig;
		//print_r($post);
		$whr1="id= '".$post['branch_id']."'";
		$query1="SELECT * from  petty_cash_boxes where id IN (SELECT `pc_box_id`  FROM  petty_cash_box_owner where `owner_id` =".$post['user_id']." and `authorized_expenses` =".$post['branch_id'].")";
		$data1 = $callConfig->getRow($query1);
		$dates = $this->toGetFrequencyDate($data1->topup_start_date, $data1->topup_frequency);
		$query = "SELECT pcbo.company_id, pcbo.pc_box_id,  pcbo.pc_type_id,  pcbo.pc_limit, pcbo.authorized_expenses, ( SELECT sum(pcitm.item_amount) FROM petty_cash pc,  pc_items pcitm WHERE pc.id = pcitm.transaction_id and pc.company_id = pcitm.company_id  and pcitm.item_id = ".$post['branch_id']." and  pc.transaction_date between '".$dates['from']."' and '".$dates['to']."') consumed FROM  petty_cash_box_owner pcbo WHERE pcbo.company_id =".$post['company_id']." AND  pcbo.pc_box_id = ".$data1->id." AND  pcbo.owner_id = ".$post['user_id']." and pcbo.authorized_expenses=".$post['branch_id'];
		//echo $query; exit;
		$data3 = $callConfig->getRow($query);
		return $data3;
	}
	function dailypettycashformonth($post){
		global $callConfig;
		$from = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - 180 day"));
		$day = 180;
		//print_r($dates); exit;	
		for ($i=0 ; $i<6 ; $i++) {
			$day = $day-30;
			$to =  date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - ".$day." day"));
			$query = "SELECT pcbo.company_id,  sum(pcbo.pc_limit) pc_limit, ( SELECT sum(pcitm.item_amount) FROM petty_cash pc,  pc_items pcitm WHERE pc.id = pcitm.transaction_id and pc.company_id = pcitm.company_id   and  pc.transaction_date between '".$from."' and '".$to."') consumed FROM  petty_cash_box_owner pcbo WHERE pcbo.company_id =".$post['company_id']." AND  pcbo.from_date <= '".$from."' AND  pcbo.to_date >= '".$from."'  AND  pcbo.owner_id = ".$post['user_id'];
			//echo $query."<br>"; exit;
			$data3 = $callConfig->getRow($query);
			$return[$i]['pc_limit']=  $data3->pc_limit !="" ? $data3->pc_limit : 0;
			$return[$i]['consumed']=  $data3->consumed !="" ? $data3->consumed : 0;
			$return[$i]['list']=  date('M-y',strtotime($to));
			$return[$i]['date']=  $from;
			$from = $to;
		}
		//echo "<pre>"; print_r($return);
		return $return; 
	}
	function dailypettycashmonth($post){
		global $callConfig;
		$from = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - 30 day"));
		//print_r($dates); exit;
		$to =  date('Y-m-d');
		$query = "SELECT pcbo.company_id,  sum(pcbo.pc_limit) pc_limit, ( SELECT sum(pcitm.item_amount) FROM petty_cash pc,  pc_items pcitm WHERE pc.id = pcitm.transaction_id and pc.company_id = pcitm.company_id   and  pc.transaction_date between '".$from."' and '".$to."') consumed FROM  petty_cash_box_owner pcbo WHERE pcbo.company_id =".$post['company_id']." AND  pcbo.owner_id = ".$post['user_id'];
		//echo $query."<br>";
		$data3 = $callConfig->getRow($query);
		$return['pc_limit']=  $data3->pc_limit !="" ? $data3->pc_limit : 0;
		$return['consumed']=  $data3->consumed !="" ? $data3->consumed : 0;
		$return['date']=  $from;
		//echo "<pre>"; print_r($return);
		return $return; 
	}
	function dailypettycashdaydetails(){
		global $callConfig;
		$from = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - 30 day"));
		//print_r($dates); exit;
		$to =  date('Y-m-d');
		$query = "SELECT sum(pcitm.item_amount) as amount, pcitm.item_id, pcitm.item_name FROM petty_cash pc,  pc_items pcitm WHERE pc.id = pcitm.transaction_id and pc.company_id = pcitm.company_id  and pcitm.item_id and  pc.transaction_date between '".$from."' and '".$to."' AND pcitm.item_id != ".TOP_UP." GROUP BY pcitm.item_id" ;
		//echo $query."<br>";
		$data3 = $callConfig->getAllRows($query);
		//print_r($data3);
		//echo "<pre>"; print_r($return);
		return $data3; 
	}
	function montlypettycashdetails($post){
		global $callConfig;
		//print_r($post);
		$query1="SELECT  id as pc_box_id, pc_box_code, pc_box_description FROM petty_cash_boxes WHERE id IN (SELECT `pc_box_id`  FROM  petty_cash_box_owner where `owner_id` =".$post['user_id']." AND `company_id` =".$post['company_id']." ORDER BY pc_box_id)";
		//echo $query1; exit;
		$datas1 = $callConfig->getAllRows($query1);
		//print_r($datas1); exit;
		//$dates = $this->toGetFrequencyDate($data1->topup_start_date, $data1->topup_frequency);
		$to = date('Y-m-d');
		$from = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - 30 day"));
		//print_r($datas1); //exit;	

		foreach ($datas1 as $data1) {
			$main ['name'] = $data1->pc_box_code;
			$main ['description'] = $data1->pc_box_description;
			$main ['id'] = $data1->pc_box_id;
			$query = "SELECT sum(pcitm.item_amount) as amount, pcitm.item_id, pcitm.item_name FROM petty_cash pc,  pc_items pcitm WHERE pc.id = pcitm.transaction_id and pc.company_id = pcitm.company_id  and pcitm.item_id and  pc.transaction_date between '".$from."' and '".$to."' AND pcitm.item_id IN ( SELECT `authorized_expenses`  FROM  petty_cash_box_owner where `owner_id` =".$post['user_id']." AND `company_id` =".$post['company_id']." AND pc_box_id = ".$data1->pc_box_id." ) GROUP BY pcitm.item_id";
			//echo $query."<br>"; 
			$data3 = $callConfig->getAllRows($query);
			$i=0;
			$return = array();
			foreach ($data3 as $data31) {
				$query4="SELECT `pc_limit`  FROM  petty_cash_box_owner where `owner_id` =".$post['user_id']." AND `company_id` =".$post['company_id']." ORDER BY pc_box_id AND pc_box_id = ".$data1->pc_box_id." AND authorized_expenses = ".$data31->item_id;
				$data4 = $callConfig->getRow($query4);
				$return[$i]['pc_limit']=  $data4->pc_limit !="" ? $data4->pc_limit : 0;
				$return[$i]['consumed']=  $data31->amount !="" ? $data31->amount : 0;
				$return[$i]['name']=  $data31->item_id !="-1" ? $data31->item_name : "Other";
				$i++;
			}
			$main ['data']= $return;
			$return_main[] = $main;
		} 
		//echo "<pre>"; print_r($return);
		return $return_main; 
	}
	function dailypettycashdetails($post){
		global $callConfig;
		//print_r($post);
		$whr1="id= '".$post['branch_id']."'";
		$query1="SELECT * from  petty_cash_boxes where id IN (SELECT `pc_box_id`  FROM  petty_cash_box_owner where `owner_id` =".$post['user_id']." ORDER BY id)";
		//echo $query1; exit;
		$datas1 = $callConfig->getAllRows($query1);
		//print_r($datas1); exit;
		//$dates = $this->toGetFrequencyDate($data1->topup_start_date, $data1->topup_frequency);
		$dates['to'] = date('Y-m-d');
		$dates['from'] = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - 365 day"));
		//print_r($dates); exit;	
		foreach ($datas1 as $data1) {
		$query = "SELECT pcbo.company_id, pcbo.pc_box_id,  pcbo.pc_type_id,  pcbo.pc_limit, pcbo.authorized_expenses, ( SELECT sum(pcitm.item_amount) FROM petty_cash pc,  pc_items pcitm WHERE pc.id = pcitm.transaction_id and pc.company_id = pcitm.company_id  and pcitm.item_id  in (SELECT `authorized_expenses`  FROM  petty_cash_box_owner where `owner_id` =".$post['user_id']." and pc_box_id = ".$data1->id." ) and  pc.transaction_date between '".$dates['from']."' and '".$dates['to']."') consumed FROM  petty_cash_box_owner pcbo WHERE pcbo.company_id =".$post['company_id']." AND  pcbo.pc_box_id = ".$data1->id." AND  pcbo.owner_id = ".$post['user_id'];
		//echo $query."<br>"; 
		$data3 = $callConfig->getRow($query);
		$return[$data1->id]=  $data3;
		}
		//echo "<pre>"; print_r($return);
		return $return; 
	}
	function dasboaradrequisition1details($post){
		global $callConfig;
		$to = date('Y-m-d');
		$month = date('m');
		$year = date('Y');
		if($month > 2){
			$from = $year."-3-1";
		}else{
			$year = $year-1;
			$from = $year."-3-1";
		}
		//echo $from."#".$to;
		$query1="SELECT cost_center_id,transaction_number,total_amount,current_status from requisitions where company_id = '".$post['company_id']."' AND originator = '".$post['user_id']."'AND  current_status NOT IN (".ST_DRAFT.",".ST_CANCEL.") AND transaction_date between '".$from."' and '".$to."'" ;
		//echo $query1; exit;
		$active_only =0;  $active = 0; $pending=0; $approve=0;
		$datas1 = $callConfig->getAllRows($query1);
		foreach ($datas1 as  $data) {
			if($data->current_status == ST_ACTIVE){
				$active_only = $active_only+$data->total_amount;
				//echo "active_only=".$data->total_amount;
			}
			else if($data->current_status == ST_APPROVED || $data->current_status == ST_QUOTATION_GENERATED || $data->current_status == ST_PURCHASE_ORDER_GENRATED  ){
				$approve = $approve+$data->total_amount;
				//echo "approve=".$data->total_amount;
			}else{
				$pending = $approve+$data->total_amount;
				//echo "pending=".$data->total_amount;
			}
			$active = $active+$data->total_amount;
			//echo "<br>";	
		}
		$return['active_only'] = $active_only;
		$return['pending'] = $pending;
		$return['active'] = $active;
		$return['approve'] = $approve;
		$return['budget'] = 400000;
		//print_r($return); exit;
		return $return;
	}
	function dasboaradrequisition1coscenterdetails($post){
		global $callConfig;
		$query12 = "SELECT id,cost_center_name from cost_centers where id in (SELECT `cost_center_id` FROM `user_cost_centers` where user_id = ".$post['user_id']." AND company_id = ".$post['company_id']." )";
		$data2 = $callConfig->getAllRows($query12);
		$to = date('Y-m-d');
		$month = date('m');
		$year = date('Y');
		if($month > 2){
			$from = $year."-03-01";
		}else{
			$year = $year-1;
			$from = $year."-03-01";
		}
		$query1="SELECT rq.cost_center_id,rq.transaction_number,rqit.item_id,rqit.item_name,rqit.description,rqit.account_code,sum(rqit.total_amount) total_amount from requisitions rq, rq_items rqit where rq.id = rqit.transaction_id AND  rq.company_id = '".$post['company_id']."' AND rq.originator = '".$post['user_id']."' AND  rq.current_status NOT IN (".ST_DRAFT.",".ST_CANCEL.") AND rq.transaction_date between '".$from."' and '".$to."' GROUP BY rqit.item_id, rq.cost_center_id" ;
		//echo $query1;
		$data3 = $callConfig->getAllRows($query1);
		$i = 0;
		foreach ($data3 as  $value) {
			$data[$value->cost_center_id][$i] = (array) $value;
			$data[$value->cost_center_id][$i]['budget'] = 100000;	
			$i++;
		}
		foreach ($data2 as  $dat) {
			$return[$dat->id] = (array)	$dat;
			//$return[$dat->id]['nsm'] = "ravi";
			$return[$dat->id]['data'] = $data[$dat->id];
		}
		return $return;
	}
	function togetstatusprogresstables($post){
		global $callConfig;
		$query = "SELECT DISTINCT(table_name) as table_name FROM `status_progress` WHERE company_id = '".$post['company_id']."'";
		$data = $callConfig->getAllRows($query);
		//echo "<pre>"; print_r($data);
		return $data;
	}
	function togetbudgetuserdeails($post){
		global $callConfig;
		$query = "SELECT * FROM chart_of_accounts WHERE account_code IN (SELECT account_code FROM `gl_account_users` WHERE user_id = ".$post['user_id'].")";
		$data = $callConfig->getAllRows($query);
		foreach ($data as $value) {
			$return [$value->accgro_group_id][] = (array)$value;
			$group_ids[] = $value->accgro_group_id;
		}
		$group_ids = implode(",", $group_ids);
		$query2 = "SELECT id,group_name,parent_group_id,accsec_section_id FROM `account_groups` WHERE id in(".$group_ids.")";
		$data2 = $callConfig->getAllRows($query2);
		foreach ($data2 as  $value2) {
			$returns1  = (array)$value2;
			$returns1['details'] = $return[$value2->id];
			$returns[$value2->accsec_section_id][] = $returns1;
			$account_section[] = $value2->accsec_section_id;
		}

		$account_section = implode(",", $account_section);
		$query3 = "SELECT id,section_name,section_description FROM `account_sections` WHERE id in(".$account_section.")";
		$data3 = $callConfig->getAllRows($query3);
		foreach ($data3 as  $value3) {
			$main[$value3->id] = (array)$value3;
			$main[$value3->id]['data'] = $returns [$value3->id];
		}
		//echo "<pre>".$account_section; print_r($main);
		return $main;
	}
	function budgetinsert($post){
		global $callConfig;		
		//print_r($post); exit;
		foreach ($post['data'] as $value) {
			$value['company_id'] = $post['company_id'];
			//$value['account_code'] = $post['account_code'];
			$value['budget_version'] = $post['budget_version'];
			$value['budget_date'] = $post['budget_date'];
			$value['cost_center_id'] = $post['cost_center_id'];
			$value['fiscal_year'] = $post['fiscal_year'];
			$value['is_active'] = 0;
			$value['created_by'] = $post['user_id'];
			$value['current_status'] = 0;
			$value['action_if_exceed_yearly'] = $post['action_if_exceed_yearly'];
			$value['action_if_exceed_quaterly'] = $post['action_if_exceed_quaterly'];
			$value['action_if_exceed_halfyearly'] = $post['action_if_exceed_halfyearly'];
			$value['action_if_exceed_month'] = $post['action_if_exceed_month'];
			$data = $this->insertorupdatebudget($value);
		}
	}
	function insertorupdatebudget($post){
		global $callConfig;
		$whr1="company_id= '".$post['company_id']."' and budget_version='".$post['budget_version']."' and cost_center_id='".$post['cost_center_id']."' and account_code='".$post['account_code']."' and fiscal_year='".$post['fiscal_year']."'";
		$query2 = $callConfig->selectQuery('budget','*',$whr1,'','','');
		//echo $query2; exit;
		$data = $callConfig->getRow($query2);
		if($data->id !=''){
			$suc_data=$callConfig->updateRecord('budget',$post,'id',$data->id);
		}else{
			$res=$callConfig->insertRecord('budget',$post);
		}
		return $res;
	}
	function pendingQuerieslist($post){
		global $callConfig;
		$query="select id, table_name, transaction_id, parent_count, query_type from ( SELECT tq1.id, tq1.table_name, tq1.query_type, tq1.transaction_id, ( select count(*) from `transaction_queries` tq2 WHERE tq2.company_id = tq1.company_id AND tq2.parent_query = tq1.id AND tq2.company_id = ".$post['company_id']." ) parent_count FROM `transaction_queries` tq1 WHERE originator = ".$post['user_id']." AND tq1.new_reply = 'N' ) a where parent_count =0";
		//echo $query;
		$data = $callConfig->getAllRows($query);
		//print_r($data);
		$query2 = "SELECT * FROM `transaction_approval_logs` WHERE company_id = ".$post['company_id']." AND response_type = 0 AND user_id =".$post['user_id']." AND send_back_flag = 0";
		$data2 = $callConfig->getAllRows($query2);
		$return['query']= $data;
		$return['approval']= $data2;
		return $return;
	}
	function addletters($post,$type=''){
		global $callConfig;
		//echo "ravi";
		//echo "<pre>"; print_r($post); exit;
		$oldwhrs="id= '".$post['hid']."'";
		$old_query=$callConfig->selectQuery('letters','*',$oldwhrs,'','','');
		$old_data = $callConfig->getRow($old_query);
		//print_r($old_data); exit;
		$status = $post['status_values'] != '' ? $post['status_values'] : ST_DRAFT ;
		$status_main = $status;
		if($type == 'update' && $status > ST_DRAFT){
			if( $post['status_old_value'] !=  $post['current_status']){
			$sarray  = array('comments' => $post['comments'], 'response_type' => $status, 'user_id' => $post['updated_by'], 'response_date' => date("Y-m-d"),'table' => 'letters','hid' => $post['hid'],'amount'=>0,'status_old' => $post['status_old_value'],'company_id'=>$post['company_id']);
			$status = $this->ToCheckStatusApprovalLog($sarray);
			//print_r($sarray); exit;
			}
		}
		$table = 'letters';
		
		$invoiced_at = str_replace('/"', '-', $post['invoiced_at']);
       	$invoiced_at =  date('Y-m-d', strtotime($invoiced_at));
       	$doc_ref_date = str_replace('/"', '-', $post['ref_date']);
        $doc_ref_date =  date('Y-m-d', strtotime($doc_ref_date));
		$fieldnames=array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_date'=>$invoiced_at,'version_number'=>$post['version_no'],'originator'=> $post['customer_id'],'ref_no'=>$post['ref_no'],'ref_date'=>$doc_ref_date,'subject'=>$post['subject'],'address_to'=>$post['address_to'],'content'=>$post['content'], 'current_status'=> $status);
		if($type == 'update'){
			$fieldnames['updated_date'] = $post['updated_date'];
			$fieldnames['updated_by'] = $post['updated_by'];
			if($old_data->content != $post['content']){
				//echo "change"; exit;
				$dd = $this->dumpaddletters($post);
				$fieldnames['version_number'] = $post['version_no']+0.01;
			}
			//echo "<pre>";print_r($fieldnames); exit;
			$insert_data=$callConfig->updateRecord($table,$fieldnames,'id',$post['hid']);
			$itemids = explode(',', $post['hid_itemds']);
			$insert_id = $post['hid'];
			$fieldnames['transaction_type'] = $table;
			$fieldnames['transaction_id'] = $insert_id;
			$res = $this->addAndUpdateBulkApprovalTransaction($fieldnames,'update');
		 }else{
		 	$fieldnames['created_by'] = $post['created_by'];
			$fieldnames['created_date'] = $post['created_date'];
			//echo "<pre>";print_r($fieldnames); exit;
		 	$transation = $this->toGetApplicationSerialNumber($table);
		 	$fieldnames['transaction_number'] = $transation;
		 	
		 	//echo "<pre>"; print_r($fieldnames); exit;
		 	$insert_data=$callConfig->insertRecord($table,$fieldnames);
		 	//print_r($insert_data); exit;
			$insert_id = $insert_data['LASTID'];
			$fieldnames['transaction_type'] = $table;
			$fieldnames['transaction_id'] = $insert_id;
			$res = $this->addAndUpdateBulkApprovalTransaction($fieldnames,'insert');
		 }
		$email_data = array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_type'=>$table, 'transaction_date'=>$invoiced_at, 'amount'=>$post['subTotal'], 'tax_amount'=>$post['taxRate'] , 'discount_amount'=>$post['discount_amount'] , 'total_amount'=>$post['grandtotal'] ,'action_label'=>'Submit for Approval', 'option'=>$fieldarray[$table]['option'] ,'Rid'=>$insert_id,'email_template'=>1,'status_old_value'=>$post['status_old_value'],'status'=>$status_main,'updated_by'=>$post['updated_by'],'originator'=>$fieldnames['originator'],'opex_cpex'=>$post['opex_cpex'],'category_type'=>$post['type'],'cost_center'=>$post['cost_center']);
		 	
		if($status > ST_DRAFT){
		 	$res =$this->toSendEmailNotificationsBaseDAO($email_data);
		 }
		 return $insert_data;
	}
	function dumpaddletters($post){
		global $callConfig;
		$query= " INSERT INTO letter_dumps (`transaction_id`, `company_id`, `transaction_number`, `transaction_date`, `version_number`, `originator`, `customer_id`, `ref_id`, `ref_no`, `ref_date`, `subject`, `address_to`, `content`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date`) SELECT  `id`, `company_id`, `transaction_number`, `transaction_date`, `version_number`, `originator`, `customer_id`, `ref_id`, `ref_no`, `ref_date`, `subject`, `address_to`, `content`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date` FROM  letters Where id = '".$post['hid']."'";
		$insert_data=$callConfig->executeQuery($query);
		//echo $query; //exit;
		return $insert_data;
	}
	function productupload($data){
		global $callConfig;
		//echo "<pre>"; print_r($data); exit;
		foreach ($data['data'] as  $value) {
		/*echo "<pre>"; print_r($value); 
		echo "<pre>"; print_r($data['names']);*/ 
			for($k=0; $k<count($data['names']); $k++){
			//	echo $data['names'][$k]."####".$value[$k]."<br>";
				$main[$data['names'][$k]] = $value[$k];
			}
			$main['vendor_id'] = $data['vendor_id'];
			//echo "<pre>"; print_r($main); exit;
			$last[] = $main; 
		}
		//echo "<pre>";	print_r($last); exit;
		foreach ($last as  $fieldnames) {
			$insert_data=$callConfig->insertRecord('product_upload',$fieldnames);
		}
	}
	function togetgenralledgerlistdata($post){
		global $callConfig;
		if($post['from']!='' && $post['to'] !=''){
			$from = $post['from'];
			$to = $post['to'];
		}else{
			$from = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - 30 day"));
			$to = date('Y-m-d');
		}

		//$query = "SELECT * FROM `general_ledger` WHERE `created_date` > '".$from."' AND `created_date` < '".$to."'";
		$query = "SELECT * FROM `general_ledger` WHERE `created_date` between '".$from." 00:00:00' and '".$to." 23:59:59'";
		if($post['account_code']!=''){
			$query.=" AND account_code in (".$post['account_code'].")";
		}
		$data = $callConfig->getAllRows($query);
		$darray = array(); 
		foreach ($data as  $value) {
			if(in_array($value->account_code, $darray)){
				//echo "ravi";
				$larray[$value->account_code]['debit_amount'] = $larray[$value->account_code]['debit_amount'] + $value->debit_amount;
				$larray[$value->account_code]['credit_amount'] = $larray[$value->account_code]['credit_amount'] + $value->credit_amount; 
			}else{
				//echo "Shankar";
				$darray[] = $value->account_code;
				$marray['RecordID'] = '1';
				$marray['acuount_code'] = $value->account_code;
				$marray['acuount_name'] = 'account_type';
				$marray['debit_amount'] = $value->debit_amount;
				$marray['credit_amount'] = $value->credit_amount;
				$marray['blance'] = '1000'; 
				$marray['type'] = $value->account_type;
				$marray['Actions'] = 'http://dev.mylejar.com/login.php';
				$larray[$value->account_code] = $marray;
				$larray[$value->account_code]['Orders'] =  array();
			}
			$carray['transaction_date'] = $value->transaction_date;
			$carray['transaction_type'] = $value->transaction_date;
			$carray['number'] = $value->transaction_ref_no;
			$carray['debit_amount'] =  $value->debit_amount;
			$carray['credit_amount'] = $value->credit_amount;
			$carray['blance'] = $value->balance_amount;
			$carray['Status'] = '1';
			$carray['Actions'] = 'https://www.google.com/doodles/';

			//'Actions'=>'http://dev.mylejar.com/login.php';
			$larray[$value->account_code]['Orders'][] = $carray;
		}
		$k=1;
		foreach ($larray as $mvalue) {
			$mvalue['RecordID'] =  $k;
			$maindata [] = $mvalue;
			$k++;
		}
		//echo "<pre>"; print_r($maindata); print_r($data);
		return $maindata;
	}
	function insertORUpadateStatusprogressaction($post){
		global $callConfig;		
		//echo "<pre>"; print_r($post); //exit;
		$whr1="company_id= '".$post['company_id']."' and status_progress_id='".$post['status_progress']."'";
		$query2 = $callConfig->selectQuery('status_progress_actions','*',$whr1,'','','');
		//echo $query2; exit;
		$progress = $callConfig->getAllRows($query2);
		//print_r($progress);
		$statuaction= array(); $rechack= array();
		foreach ($progress as $progres) {
			$statuaction[] = $progres->target_status;
		}
		//print_r($statuaction); //exit;
		foreach ($post['target'] as $target) {
			if(in_array($target, $statuaction)){
				$update = "update status_progress_actions set is_active = 0 where status_progress_id ='".$post['status_progress']."' and target_status = '".$target."'";
				$update_data=$callConfig->executeQuery($update);
			}else{
				$insert_filed = array('company_id'=>$post['company_id'],'status_progress_id'=>$post['status_progress'],'target_status'=>$target,'email_groups'=>$post['email_groups'],'email_template_id'=>1,'current_status'=>$post['current_status'],'status_date'=>$post['status_date'],'from_date'=>$post['from_date'],'to_date'=>$post['to_date'],'created_date'=>$post['created_date'],'created_by'=>$post['created_by'],'is_active'=>'0'); 
				//print_r($insert_filed); exit;
				$insert_data=$callConfig->insertRecord('status_progress_actions',$insert_filed);
			}
			$rechack[] = $target;
		}
		$chek = implode(',', $rechack);
		$lupdate = "update status_progress_actions set is_active = 1 where status_progress_id ='".$post['status_progress']."' and target_status not in (".$chek.")";
		$update_data=$callConfig->executeQuery($lupdate);
		//exit;
		return $update_data;
	}
	function togetstatusprogressactiondetails(){
		global $callConfig;
		$query11 = "SELECT * FROM `status_progress` WHERE `id` IN  (SELECT status_progress_id FROM status_progress_actions GROUP BY status_progress_id)";
		$data11 = $callConfig->getAllRows($query11);
		foreach ($data11 as $data1) {
			$array['id'] =  $data1->id;
			$array['table_name'] =  $data1->table_name;
			$array['name'] =  $data1->name;
			$array['action_label'] =  $data1->action_label;
			$query12 = "SELECT  action_label FROM `status_progress` WHERE `id` IN (Select `target_status` from status_progress_actions WHERE status_progress_id =".$data1->id." )";
			$data12 = $callConfig->getAllRows($query12);
			//print_r($data12); exit;
			$sub = array();
			foreach ($data12 as  $data2) {
				$sub[] = $data2->action_label;
			}	
			$array['statu_labels'] = implode(',', $sub);
			$mainarray[] = $array;
		}
		return $mainarray;
		//echo "<pre>"."ravi"; print_r($mainarray);
	}
	function toGetItemcostingtyewithdata($post){
		global $callConfig;
		//$whr1="company_id= '".$post['company_id']."' and id='".$post['status_progress']."'";
		$query = "SELECT itmc.`default_costing_type` FROM items itm,`item_categories` itmc where itmc.company_id = itm.company_id AND itm.category_id = itmc.id and itm.id=".$post['item_id'];
		$data = $callConfig->getRow($query);
		$return = $this->togetitemvalues($post['item_id']);
		$return->item_st_price = $return->purchase_price;
		$query2 = "SELECT  `day_rate` FROM `item_wa_balances` WHERE `item_id`=".$post['item_id']." AND company_id =".$post['company_id']." AND `balance_date`<='".$post['date']."'  ORDER BY  balance_date DESC LIMIT 0,1";
		//echo $query2;
		$wadata = $callConfig->getRow($query2);
		$return->item_wa_price = $wadata->day_rate;

		if($data->default_costing_type == 'W'){
		    if($wadata->day_rate !=""){
			    $return->purchase_price = $wadata->day_rate;
		    }
		}else{
			if($post['table'] == 'delivery_orders' || $post['table'] == 'sales_returns'){ 
				$return->purchase_price = $return->sale_price;
			}
		}	
		return $return;
	}
	function employeeDetails($data){
		global $callConfig;
		$query1 = "SELECT * FROM `employees` WHERE id = ".$data['emp_id']." AND is_active=0 AND company_id=".$data['company_id'];
		$data1 = $callConfig->getRow($query1);
		$data['report_id'] = $data1->reporting_manager_id;
		$return['emp_data'] = (array)$data1;
		//'','reporting_manager',
		$array =  array('departments','application_users','verticals','designations','locations','employment_bands','employment_levels','employment_roles','application_roles');
		foreach ($array as  $arr) {
			$loopdata = $this->togettablebasedata($arr,$data);
			$return[$arr] =  (array)$loopdata;
		}
		$return['blood_groups'] = $this->getAllTablesData('general_code_details','*',"genc_code='BLOODGROUPS'");
		$return['teams'] = $this->togetemployeeteamdata($data);
		$return['asserts'] = $this->togetemployeeassertdata($data);
		return $return;
	}
	function togettablebasedata($table,$data ){
		global $callConfig;
		$whr = "is_active = 0 AND company_id = ".$data['company_id'];
		//echo $where;
		$query=$callConfig->selectQuery($table,'*',$whr,'','','');
		//echo $tablename."//".$query;exit;
		return $callConfig->getAllRows($query);
	}
	function togetemployeeteamdata($data){
		global $callConfig;
		$report_id =$data['report_id'];
		$by_emp_ids = array();
		$query22 = "SELECT id FROM `employees` WHERE reporting_manager_id = ".$data['emp_id']." AND is_active=0 AND company_id=".$data['company_id'];
		$data22 = $callConfig->getAllRows($query22);
		if($report_id != ""){	
		$by_emp_ids[] = $report_id;
		}
		foreach ($data22 as $emp_ids) {
			$by_emp_ids[] =  $emp_ids->id;
			$by_emp[] =  $emp_ids->id;
		}
		$str_by_emp_ids = implode(",", $by_emp_ids);
		$query23 = "SELECT id,`designation_id`,`location_id`,`display_name`,`email_id`,`phone`,`image` FROM `employees` WHERE id IN (".$str_by_emp_ids.")";
		//echo $query23;
		$data23 = $callConfig->getAllRows($query23);	
		foreach ($data23 as  $data23_data) {
			$array23[$data23_data->id] = (array)$data23_data;
		}
		if($by_emp != ""){
			foreach ($by_emp as $byemp_id) {
				$by_emp_data[]= $array23[$byemp_id];
			}
		}else{ //echo "ravi";
			$by_emp_data= array();
		}
		$teams ['reproter'] = $array23[$report_id];
		$teams ['teams'] = $by_emp_data;
		//echo "<pre>";print_r($teams); echo count($by_emp_data);
		return $teams;
	}
	function togetemployeeassertdata($data){
		global $callConfig;
		$query24 = "SELECT id,assets_id,allocation_from,allocation_to,remarks,current_status FROM `emp_assets` WHERE emp_id = ".$data['emp_id']." AND is_active=0 AND company_id=".$data['company_id'];
		$data24 = $callConfig->getAllRows($query24);
		foreach ($data24 as $data24_data) {
			$assets_data[$data24_data->id] = (array)$data24_data;
			$assert_ids[] = $data24_data->assets_id;
		}
		$str_asserts_ids = implode(",", $assert_ids);
		$query25 = "SELECT `id`,`asset_code`,`asset_description`,`long_description` FROM `assets`  WHERE id IN (".$str_asserts_ids.")";
		//echo $query23;
		$data25 = $callConfig->getAllRows($query25);	
		foreach ($data25 as  $data25_data) {
			$array25[$data25_data->id] = (array)$data25_data;
		}
		$data24 = (array)$data24;
		foreach ($assets_data as $key => $asset) {
			//echo $key;
			$assets_data[$key]['assert'] = $array25[$asset['assets_id']];
		}
		return $assets_data;
	}
	function updatedynamicapprovallog($post){
		global $callConfig;
		$statusarray = array(ST_ACTIVE, ST_DRAFT, ST_INACTIVE);
		print_r($statusarray); print_r($post);
		if( in_array($post['status'], $statusarray) ){
			//echo "hai"; exit;
			$res=$callConfig->updateRecord($post['table'],array('current_status'=>$post['status']),'id',$post['hid']);
		}else{
			//echo "ravi"; exit;
			$post['response_type'] = >$post['status'];
			$post['Rid'] = >$post['hid']; 
			$post['updated_by'] = >$post['user_id'];
			$whr = "";
			$fieldnames = array('comments'=>$post['comments'],'response_date'=>$post['response_date'],'response_type'=>$post['status']);
			$whr = "transaction_table = '".$post['table']."' AND transaction_id='".$post['hid']."' AND user_id='".$post['user_id']."' AND send_back_flag= 0";
			$res=$callConfig->updateRecord(TBL_TRANSACTION_APPROVAL_LOGS,$fieldnames,'','',$whr);
			if($post['response_type'] != ST_SEND_BACK ){ 
				$return = $this->sendemailsandnotificationsfordynamic($post);
			}
		}
		return $return;
	}
	
	/*function sendemailsandnotificationsfordynamic($data){
		
		global $callConfig;
		$email_data = $this->checkhierarchicallevel($data);
		if($email_data['doa'] == 1){
			//$email_data = $this-> togetemailslist($data);
		}
		$emails[] = $email_data['email'];
		$users = $email_data['user_id'];
		$query1="SELECT email,user_id FROM ".TBL_EMAIL." WHERE user_id in (".$data['updated_by'].")";
		$emailids = $callConfig->getRow($query1);
		$emails[] = $email_ids->email;
		$whrstp="table_name = '".$data['transaction_type']."' AND status='".$data['status']."'";
		$querystp=$callConfig->selectQuery(TBL_STATUSPROGRES,'*',$whrstp,'','','');
		$resstp = $callConfig->getRow($querystp);
		$data['action_label'] =  $resstp->action_label;
		$data['email_template'] =  $resstp->email_template_id;
		$data['email_content_id'] = 1;
		$email_array = $this->emailtemplate($data);
		$file_name = 'email'.'_'.time().uniqid().'.'.'html';
		$email_array ['content'] = $file_name;
		$email_array['revicer'] = $emails;
		$email_array['email_type'] = 1;
		$email = $email_array['mesg'];
		$file_path='uploads/email/'.$file_name;
		file_put_contents($file_path, $email);
		fclose($file_path);
		$lenn = $this->sendEmailslist($email_array);
		$notarry['notification_message'] = ucwords($data['transaction_type'])." ".$data['transaction_number']. ": ".ucwords($resstp->action_label) ;
		$notarry['notification_users'] = count($users);
		$notarry['id'] = $data['Rid'];
		$notarry['option'] = $data['option'];
		$jsnot= json_encode($notarry);
		$NOTIfieldnames=array('notification_message'=>$notarry['notification_message'],'notification_details'=>$jsnot,'notification_datetime'=>date("Y-m-d H:i:s"));
		$noti_res=$callConfig->insertRecord(TBL_NOTIFICATIONS,$NOTIfieldnames);
		$users[]= $data['updated_by'];
		foreach ($users as $user) {
			$appv_fieldnames=array('company_id'=>$data['company_id'],'transaction_table'=>$data['transaction_type'],'transaction_id'=>$data['Rid'],'user_id'=>$user,'request_date'=>date("Y-m-d H:i:s"),'doa_id' => $email_data['doa_id']);
				if($user != $data['updated_by']){
					$approval_id=$callConfig->insertRecord(TBL_TRANSACTION_APPROVAL_LOGS,$appv_fieldnames);
				}
				$revalue = 'ACTION';
			$nti_use_fieldnames=array('user_id'=>$user,'notification_id'=>$notification_id,'notification_type'=>$revalue);
			$res=$callConfig->insertRecord(TBL_NOTIFICATON_USER,$nti_use_fieldnames);
		}

	}
	function sendDynamicnotifications($data){
		global $callConfig;
		$notarry['notification_message'] = ucwords($data['transaction_type'])." ".$data['transaction_number']. ": ".$data['label'] ;
		//echo "ravi"; exit;
		$notarry['notification_users'] = count($data['users']);
		$notarry['id'] = $data['Rid'];
		$notarry['option'] = $data['option'];
		//print_r($notarry); exit;
		$jsnot= json_encode($notarry);
		$NOTIfieldnames=array('notification_message'=>$notarry['notification_message'],'notification_details'=>$jsnot,'application_table_name'=>$data['transaction_type'],'transaction_number'=>$data['Rid'],'company_id'=>$data['company_id'],'notification_datetime'=>date("Y-m-d H:i:s"));
		$noti_res=$callConfig->insertRecord(TBL_NOTIFICATIONS,$NOTIfieldnames);
		$notification_id = $noti_res['LASTID'];
		foreach ($data['users'] as  $value) {
			
		$nti_use_fieldnames=array('user_id'=>$value,'notification_id'=>$notification_id,'notification_type'=>'ACTION','company_id'=>$data['company_id']);
		//print_r($nti_use_fieldnames); exit;
		$res=$callConfig->insertRecord(TBL_NOTIFICATON_USER,$nti_use_fieldnames);
		}
	}*/
	
	function addorupdatewizardsettings($post,$urldata){
		global $callConfig; 
		//echo "<pre>"; print_r($post); print_r($urldata); exit;
		foreach ($post['name'] as  $page_id) {
			$whr1="company_id= '".$urldata['company_id']."' and user_id='".$urldata['user_id']."' and widget_id='".$page_id."'";
			$query2 = $callConfig->selectQuery('application_user_widgets','*',$whr1,'','','');
			//echo $query2; exit;
			$data = $callConfig->getRow($query2);
			if($data->id != ""){
				$update = "update application_user_widgets set is_active = 0 where id ='".$data->id."'";
				$update_data=$callConfig->executeQuery($update);
			}else{
				$insert_array = array('company_id'=>$urldata['company_id'],'user_id'=>$urldata['user_id'],'widget_id'=>$page_id,'is_active'=>0,'current_status'=>0,'created_by'=>$post['created_by'],'created_by'=>$post['created_by']);
				$approval_id=$callConfig->insertRecord('application_user_widgets',$insert_array);
			}
			$ids[] = $page_id;
		}
		$ids = implode(",", $ids);		
		$update = "update application_user_widgets set is_active = 1  where id NOT IN(".$ids.")";
		$data = $callConfig->executeQuery($update);
		return $data;
	}
	function addorupdatekpi($post,$get){
		global $callConfig; 
		//echo "<pre>";print_r($post);  exit;
		if($post['type']='update'){
			$update_query = "UPDATE key_performance_indicators SET is_active = 1 WHERE company_id= '".$get['company_id']."' and role_id='".$post['role_id']."' and  version_no='".$post['version_no']."'";
			$data = $callConfig->executeQuery($update_query);
		}
		foreach ($post['name'] as  $key=>$name) {

			$insert_array = array('company_id'=>$get['company_id'],'indicator_name'=>$name,'key_reslut_id'=>$post['result'][$key],'weightage'=>$post['weightage'][$key],'model'=>$post['model'][$key],'uom'=>$post['uom'][$key],'role_id'=>$post['role_id'],'version_no'=>$post['version_no'],'is_active'=>'0','current_status'=>0,'created_by'=>$post['created_by'],'created_by'=>$post['created_by']);
			//print_r($insert_array); exit;
			$insert = $callConfig->insertRecord('key_performance_indicators',$insert_array);
			
		}
		return $insert;
	}	
	function togetcandidatedocumentlist($data){
		global $callConfig; 
		$query="SELECT * FROM `document_lists` WHERE id in (SELECT document_list_id FROM `candidate_documents` WHERE `candidate_id` = ".$data['cand_id']." AND company_id = ".$data['cand_id']." AND is_active = 0)";
		$data = $callConfig->getAllRows($query);
		return $data;
	}
}
?>