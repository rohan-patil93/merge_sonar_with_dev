<?php

session_start();

if (isset($_SESSION['userid'])){

unset($_SESSION['userid']);

unset($_SESSION['us_name']);

unset($_SESSION['lastlogin']);

unset($_SESSION['roletype']);

unset($_SESSION['email']);

unset($_SESSION['adminmemberjoinedon']);

unset($_SESSION['last_activity']);

unset($_SESSION['application_pages']);

unset($_SESSION['application_menu']);

session_destroy ();

}
header("Location:login.php");
if($_SESSION['userid']=="")

{

header("Location:index.php");

exit();

}
