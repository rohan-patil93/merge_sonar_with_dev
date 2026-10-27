<?php
$project_id = $_GET['RID'];
$url = API."?mode=togetprojectdnewashboarddetails&project_id=".$project_id;
$newdata_details = callAPI($url);
$newdata = json_decode($newdata_details,1);

$graph1 = $newdata['graphone'];
$gaa = round(($graph1['schd']/ $graph1['total_project'])*100);
$gab = round(($graph1['complete']/ $graph1['total_project'])*100);

$_SESSION['apply_js']['project_dashboard_task'] = 1;
$_SESSION['apply_js']['project_dashboard_progress'] = 1;
$_SESSION['apply_js']['project_dashboard_workload'] = 1;
$_SESSION['apply_js']['project_dashboard_time'] = 1;
$_SESSION['apply_js']['project_dashboard_cost'] = 1;
$cdaa  = array();
$cdaa[] = "Yet to start:".($graph1['yet']!="" ? $graph1['yet'] : 0);
$cdaa[] = "Inprogress:".($graph1['ing'] !="" ? $graph1['ing'] : 0 );
$cdaa[] = "Completed:".($graph1['complete'] !="" ? $graph1['complete'] : 0);
$cdaa[] = "Overdue:".($graph1['overdue'] !="" ? $graph1['overdue'] : 0);

foreach($newdata['graphsix'] as $six){
  $pps = round(($six['complete']/$six['total_project'])*100);
  $workload[] = $six['display_name'].", ".$pps."% :".$pps.":".(100-$pps);
}
$cntworkload = count($workload);
if($cntworkload<5){
  for($i=$cntworkload; $i<6; $i++){
      $workload[] = " : : ";
  }
}
$cntgraphtwo = count($newdata['graphtwo']);
if($cntgraphtwo<5){
  for($i=$cntgraphtwo; $i<6; $i++){
      $newdata['graphtwo'][] = " : : ";
  }
}
?>
<div class="row">
  <div class="col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-header">
        <h5>Health</h5>
      </div>
      <div class="card-body p-4">
        <div class="table-responsive">
          <table class="table table-hover table-sm">
            <tr>
              <th scope="col">Time</th>
              <td><?php echo $gaa; ?>% ahead of schedule</td>
            </tr>
            <tr>
              <th scope="col">Task</th>
              <td> <?php echo $graph1['tobe']; ?>  task to be completed</td>
            </tr>
            <tr>
              <th scope="col">Workload</th>
              <td><?php echo $graph1['overdue']; ?> task overdue</td>
            </tr>
            <tr>
              <th scope="col">Progress</th>
              <td><?php echo $gab; ?>% Complete</td>
            </tr>
            <tr>
              <th scope="col">Cost</th>
              <td>27% under budget</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-header">
        <h5>Tasks</h5>
      </div>
      <div class="card-body p-4" style="height: 300px;">
          <div id="chart_Donut_Project_task" class="float-left" ></div>
          <input type="hidden" name="chart_project_task" id="chart_project_task" value="<?php echo implode("||",$cdaa); ?>" >
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-header">
        <h5>Progress</h5>
      </div>
      <div class="card-body p-4" style="height: 300px;">
       <div id="chart_bar_progress" style="width: 100%; height: 300px; margin-top:-25px;"></div>
       <input type="hidden" name="chart_project_progress" id="chart_project_progress" value="<?php echo implode("||",$newdata['graphtwo']); ?>" >

      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-header">
        <h5>Time</h5>
      </div>
      <div class="card-body p-4" style="height: 300px;">
         <div id="chart_bar_time" style="width: 100%; height: 300px; margin-top:-25px;"></div>
        <input type="hidden" name="chart_project_time" id="chart_project_time" value="<?php echo implode("||",$workload); ?>" >
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-header">
        <h5>Cost</h5>
      </div>
      <div class="card-body p-4" style="height: 300px;">
        <div id="chart_Combo" style="height:250px;"></div>
        <input type="hidden" name="chart_project_cost" id="chart_project_cost" value="<?php echo implode("||",$newdata['graphfive']); ?>" >
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-header">
        <h5>Workload</h5>
      </div>
      <div class="card-body p-4" style="height:300px;">
        <div id="chart_bar_workload" style="width: 100%; height: 300px; margin-top:-25px;"></div>
        <input type="hidden" name="chart_project_workload" id="chart_project_workload" value="<?php echo implode("||",$workload); ?>" >
      </div>
    </div>
  </div>
</div>
