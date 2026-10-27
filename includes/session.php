<?php
ob_start();
if(!session_id()) {
    session_start();
}

$now = time();
// get the time the session should have expired
$timeout_duration =  600* 1000;
$limit = $now - $timeout_duration;
// check the time of the last activity
if (isset ($_SESSION['last_activity']) && $_SESSION['last_activity'] < $limit || $_SESSION['userid']=="") {
  $_SESSION = array();
  header('Location:login.php');
  exit;
}else{  // otherwise, set the value to the current time
      $_SESSION['last_activity'] = $now;
}
