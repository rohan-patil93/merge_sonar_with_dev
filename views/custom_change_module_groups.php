<?php 
session_start();
$row= array('id'=>$_SESSION['userid'],'user_name'=>$_SESSION['us_name'],'company_id'=>$_SESSION['company_id'],'email'=>$_SESSION['user_email'],'lastlogin'=>$_SESSION['lastlogin'],'role_id'=>$_SESSION['role_id'],'db'=>$_SESSION['DB_NAME'],'reset_password'=>$_SESSION['reset_password'],'confirmed_basic_ino'=>$_SESSION['confirmed_basic_ino'],'admin_access_employee'=>$_SESSION['admin_access_employee'],'user_token'=>$_SESSION['user_token'],'HTTP_INFI_TOKEN'=>$_SESSION['HTTP_INFI_TOKEN']);

session_destroy ();
$_SESSION = array();
session_start();
$_SESSION['userid'] = $row['id'];
$_SESSION['us_name']=$row['user_name'];
$_SESSION['company_id'] = $row['company_id'];
$_SESSION['email']=$row['user_email'];
$_SESSION['lastlogin']=$row['lastlogin'];
$_SESSION['role_id']=$row['role_id'];
$_SESSION['DB_NAME']=$row['db'];
$_SESSION['lastlogin']=$row['lastlogin'];
$_SESSION['reset_password']=$row['reset_password'];
$_SESSION['confirmed_basic_ino'] = $row['confirmed_basic_ino'];
$_SESSION['admin_access_employee'] = $row['admin_access_employee'];
$_SESSION['user_token'] = $row['user_token'];
///$_SESSION['default_module_group_id'] = base64_decode($_GET['Rid']);
$_SESSION['work_module_setup_id'] = base64_decode($_GET['Rid']);
$_SESSION['working_role_short_code'] = $_GET['working_role_short_code'];
$_SESSION['HTTP_INFI_TOKEN'] = $row['HTTP_INFI_TOKEN'];
//echo "ravi".$_SESSION['userid'];
//print_r($_SESSION);
header("Location:index");
exit;
?>