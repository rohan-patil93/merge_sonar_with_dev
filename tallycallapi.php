<?php
ini_set('display_errors', 1);
include_once 'dbconfig.php';
include_once 'includes/dbconnection.php';
date_default_timezone_set('Asia/Kolkata');
include_once "model/user.class.php";
$userObj=new userClass();
$method = $_GET['method'];
$mode = $_GET['mode'];
$api_code = $_REQUEST['uniquecode'];
if($method == 'post'){
  // echo "working";
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $_GET['json_data'] = 'Y';
    } else {
        $_GET['json_data'] = 'N';
    }
   include_once "model/tally.class.php";
    $tallyObj=new tallyClass();
    $return = $tallyObj->getAPImastersAPICode($_GET,$data);
    $response = json_encode($return);
    echo $response;
}else{
    include_once "model/tally.class.php";
    $tallyObj=new tallyClass();
    $return = $tallyObj->getAPImastersAPICode($_GET,$_POST);
    $response = json_encode($return);
    echo $response;
}

