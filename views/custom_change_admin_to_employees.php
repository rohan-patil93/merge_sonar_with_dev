<?php 
session_start();
$_SESSION['admin_access_employee'] = $_POST['admin_access_employee'];
//header("Location:index");
exit;
?>