<?php 
if(isset($_POST['appraisal_id'])){
    include('../includes/session.php');
    include('../dbconfig.php');
    include('../comm.php');
    $appraisal_id = $_POST['appraisal_id'];
    $employee_id = $_POST['emp_id'];
    $url1 = API."?mode=pmsdashboard2&appraisal_id=".$appraisal_id."&employee_id=".$employee_id; 
    $resultapi = callAPI($url1);
    $result = json_decode($resultapi,1);
    $employee_goals = $result['employee_goals'];
    $performance_setup = $result['performance_setup'];
    $employeegoal = array();
    foreach ($employee_goals as $employeegoals){
        $employeegoal[$employeegoals['akra_id']][] = $employeegoals; 
        if(!isset($notrepet[$employeegoals['akra_id']])){
            if($employeegoals['approved_achievement'] != ""){
                $appraisalemp['completed'][$employeegoals['akra_id']]++; 
            }elseif($employeegoals['self_achievement'] !=""){
                $appraisalemp['in_progress'][$employeegoals['akra_id']]++;
            }else{
                $appraisalemp['not_start'][$employeegoals['akra_id']]++; 
            }
        }
        $notrepet[$employeegoals['akra_id']] =$employeegoals['akra_id'];
    }
        $arslabs = $result['arslabs'];
}
?>
<div class="modal-header">
    <h4 class="modal-title">PMS Report</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5>My Goals</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-4">
                        <div class=" social-card">
                            <div class="card-body text-center">
                                <h2 class="text-primary"><i class="fa fa-hourglass-half"></i></h2>
                                <h3 class="text-facebook f-w-700 mb-0"><?php echo count((array)$appraisalemp['in_progress']); ?></h3>
                                <p>In Progress</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4"> 
                        <div class=" social-card"> 
                            <div class="card-body text-center">
                                <h2 class="text-info"><i class="fa fa-question-circle"></i></h2>
                                <h3 class="text-facebook f-w-700 mb-0"><?php echo count((array)$appraisalemp['not_start']); ?></h3>
                                <p>Not Started</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class=" social-card">
                            <div class="card-body text-center">
                                <h2 class="text-danger"><i class="fa fa-thumbs-up"></i></h2>
                                <h3 class="text-facebook f-w-700 mb-0"><?php echo count((array)$appraisalemp['completed']); ?></h3>
                                <p>Completed</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" social-card">
                    <div class="row">
                        <div class="col-lg-8">
                            <h5 class="text-c-yellow f-w-700  m-l-20">Count Of Goals : <?php echo count((array)$employeegoal); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="table-responsive custom-scroll" style=" height: 250px;">
                    <table class="table table-hover m-b-0" style="font-size:13px;">
                        <tr>
                            <th>Goal Name</th>
                            <th>Self Progress</th>
                            <th>Manager Progress</th>
                        </tr>
                        <?php 
                        $pmsdashboardcahrt[] = array("Score Slabs","emp_cnt"); 
                        foreach($arslabs as $arslabs1){
                            $pmsdashboardcahrt[str_replace(' ','_',$arslabs1['slabs_name'])] = array($arslabs1['slabs_name'],0);
                        }   
                         foreach($employeegoal as $employeegoals){ 
                            $traget =$self_achievement=$approved_achievement=$selfpercentagecalc=$approvepercentagecalc=$kraweightage =  0;
                            foreach($employeegoals as $employeegoal1){
                                $traget += $employeegoal1['traget'];
                                $self_achievement += $employeegoal1['self_achievement'];
                                $approved_achievement += $employeegoal1['approved_achievement'];
                                $kraweightage += $employeegoal1['weightage'];
                            }
                            if($traget ==0){
                                $traget = 1;
                            }

                            $selfpercentagecalc = customNanAndEmptyValidation(round(($self_achievement/$traget)*100));
                            $approvepercentagecalc = customNanAndEmptyValidation(round(($approved_achievement/$traget)*100));
                            foreach($arslabs as $arslabs1){
                                if($arslabs1['start_range'] <= $selfpercentagecalc AND $arslabs1['end_range'] >= $selfpercentagecalc){
                                    $selfscoreslab = $arslabs1['slabs_name'];
                                }
                                if($arslabs1['start_range'] <= $approvepercentagecalc AND $arslabs1['end_range'] >= $approvepercentagecalc){
                                    $hrscoreslab = $arslabs1['slabs_name'];
                                    $pmsdashboardcahrt[str_replace(' ','_',$arslabs1['slabs_name'])][1]++;
                                }   
                            }

                            ?>
                            <tr>
                                <td><?php echo $employeegoals[0]['area_name'];?></td>
                                <!-- <td><?php echo $traget;?></td> -->
                                <td>
                                    <?php
                                       // echo $selfpercentagecalc." -- ".$traget." --- ".$kraweightage." -- ".$scoreslab; 
                                    echo $selfscoreslab; ?>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: <?php echo $selfpercentagecalc; ?>%" aria-valuenow="<?php echo $selfpercentagecalc; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                                <td>
                                    <?php echo $hrscoreslab; ?>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: <?php echo $approvepercentagecalc; ?>%" aria-valuenow="<?php echo $approvepercentagecalc; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>     
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5>Performance Rating</h5>
                <?php //echo "<pre>"; print_r($pmsdashboardcahrt); echo "</pre>";

                foreach($pmsdashboardcahrt as $pmsdashboardcahrt){
                    $pmsdashboardcahrtrepl[] =  $pmsdashboardcahrt;
                }
                         ?>
            </div>
            <div class="card-body">
                <div class="card-block">
                <input type="hidden" id="performancereviewrattingpopup" value='<?php echo json_encode($pmsdashboardcahrtrepl); ?>'>
                <div id="performance_review_rattingpopup" style="width: 100%; height: 400px;"></div>
            </div>
            </div>
        </div>
    </div>
</div> 
</div>
<!-- <div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div> -->
<?php if(isset($_POST['appraisal_id'])){ ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        $(document).ready(function() { 
            google.charts.load("current", { packages: ["corechart"] });
            google.charts.setOnLoadCallback(drawChartDonut);
            function drawChartDonut() {
               var payrunprocess  = JSON.parse($("#performancereviewrattingpopup").val()); 
               console.log(payrunprocess);
               var dataDonut = google.visualization.arrayToDataTable(payrunprocess);
               var optionsDonut = {
                pieHole: 0.4,
                colors: ['#448aff', '#11c15b', '#ffe100', '#ffe100', '#ffe100']
            };
            var chart = new google.visualization.PieChart(document.getElementById('performance_review_rattingpopup'));
            chart.draw(dataDonut, optionsDonut);
            }
        });
    </script>
<?php } ?>