<?php

include_once "model/db.class.php";
$db = $_GET['data_name']!=""?$_GET['data_name']:MAINDB;
$extra['user_id'] = $_GET['user_id'];
$extra['company_id'] = $_GET['company_id'];
$extra['ip_address'] = $_GET['ipaddress'];
$extra['option'] = $_GET['page_option'];
$extra['extra_paramiter'] = $_GET['extra_paramiter'];
$extra['extra_details'] = $_GET['extra_details'];
$extra['module_id'] = $_GET['module_id'];
$extra['working_role_short_code'] = $_GET['working_role_short_code'];

$callConfig=new configClass($db,$extra);

$callConfig->configConnection($db,$extra);
