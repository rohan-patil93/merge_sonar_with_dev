
<?php
if($_SESSION['config']['application_module']==3){
$enddate = date('Y-m')."-".$_SESSION['config']['default_timesheet_date'];
$startdate = date('Y-m-d', strtotime('-5 day', strtotime($enddate)));
$todaydate = strtotime(date('Y-m-d'));
if(strtotime($startdate)<= $todaydate && strtotime($enddate) >=$todaydate && !isset($_SESSION['alertdiplay'])){
    $_SESSION['alertdiplay'] = 1;
?>

<div class="alert alert-warning alert-dismissible">
      <strong>Please submit your timesheet by <?php echo date("d-M-Y",strtotime($enddate)); ?>, please ignore this message if already submitted</strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php }
} ?>

<?php
include_once 'views/custom_list_dash_board.php';
