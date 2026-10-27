<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['emp_goal_id'];
$apiurl = API."?mode=getemployeegoals&rid=".$rid;
$apicallss = callAPI($apiurl);
$result = json_decode($apicallss,1);
$employee_goals = $result['employee_goals'];
?>
<link rel="stylesheet" type="text/css" href="assets/css/pages.css">
<div class="modal-header">
    <h5 class="modal-title"><?php echo $employee_goals['goal_name']; ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
      <div class="range-slider">
        <input aria-label="Ex13" id="ex13" type="text" data-slider-ticks="[0, 100, 200, 300, 400]" data-slider-ticks-snap-bounds="30" data-slider-ticks-labels='["$0", "$100", "$200", "$300", "$400"]' />
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitform()">Submit</button>
</div>

<script type="text/javascript" src="assets/bower_components/seiyria-bootstrap-slider/js/bootstrap-slider.js"></script>
<!-- Custom js -->
<script type="text/javascript">
         //#13
     $("#ex13").slider({
         ticks: [0, 100, 200, 300, 400],
         ticks_labels: ['$0', '$100', '$200', '$300', '$400'],
         ticks_snap_bounds: 30
     });
</script>
