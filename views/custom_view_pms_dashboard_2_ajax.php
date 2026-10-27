<?php
if(isset($_POST['appraisal_id'])){
    include('../includes/session.php');
    include('../dbconfig.php');
    include('../comm.php');
    $appraisal_id = $_POST['appraisal_id'];
    $employee_id = 1;
    $url1 = API."?mode=pmsdashboard2&appraisal_id=".$appraisal_id."&employee_id=".$employee_id; 
    $resultapi = callAPI($url1);
    $result = json_decode($resultapi,1);
    $employee_goals = $result['employee_goals'];
    $performance_setup = $result['performance_setup'];
}
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
$all_employee_goals = $result['new_hierd_employee_goals'];
//$employee_performance_appraisal_headers= $result['employee_performance_appraisal_headers'];
foreach($all_employee_goals as $all_employee_goals1){
    $mktraget[$all_employee_goals1['employee_id']] += $all_employee_goals1['traget'];
    $mkself_achievement[$all_employee_goals1['employee_id']] += $all_employee_goals1['self_achievement'];
    $mkapproved_achievement[$all_employee_goals1['employee_id']] += $all_employee_goals1['approved_achievement'];
}
$sum = array();
foreach($arslabs as $arslabs1){  //array create to ploting chart
    $allpmsdashboardchart[str_replace(' ','_',$arslabs1['slabs_name'])] = array($arslabs1['slabs_name'],0);
}
foreach($mktraget as $empid => $mktraget1){ //created array value added
    $mktraget1 = ($mktraget1 !="" AND $mktraget1 != 0)?$mktraget1:1;
    $selfscore = customNanAndEmptyValidation(round(($mkself_achievement[$empid]/$mktraget1)*100));
    $hrscore = customNanAndEmptyValidation(round(($mkapproved_achievement[$empid]/$mktraget1)*100));
    foreach($arslabs as $arslabs1){
        if($arslabs1['start_range'] <= $selfscore AND $arslabs1['end_range'] >= $selfscore){
            $sum[str_replace(' ','_',$arslabs1['slabs_name'])]++;
            $allpmsdashboardchart[str_replace(' ','_',$arslabs1['slabs_name'])] = array($arslabs1['slabs_name'],$sum[str_replace(' ','_',$arslabs1['slabs_name'])]);
        }
    }
    $mkselfpercentagecalc[$empid] = $selfscore;
    $approvepercentagecalc[$empid] = $hrscore;
}
$my_team = $result['my_team'];
//echo "<pre>";print_r($result);echo "</pre>";
?>
<script type="text/javascript">
    function getMyTeamPreformance(emp_id){
        var appraisal_id = '<?php echo $appraisal_id; ?>';
        jQuery.ajax({
            url: "views/custom_view_pms_dashboard_2_popup.php",
            data:'appraisal_id='+appraisal_id+'&emp_id='+emp_id,
            type: "POST",
            success:function(data){
                 $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal('show');
            },
            error:function (){}
        });
   
    }
</script>
<div class="row mt-4">
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
                <div class="table-responsive custom-scroll" style=" height: 200px;">
                    <table class="table table-hover m-b-0" style="font-size:13px;">
                        <tr>
                            <th>Goal Name</th>
                            <th>Target</th>
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
                                <td><?php echo $traget;?></td>
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
                <input type="hidden" id="performancereviewratting" value='<?php echo json_encode($pmsdashboardcahrtrepl); ?>'>
                <div id="performance_review_ratting" style="width: 100%; height: 300px;"></div>
            </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h5>New hired Trend</h5>
            </div>
            <div class="card-body">
                <?php 
                foreach($allpmsdashboardchart as $allpmsdashboardchart){
                    $allperformancereviewratting[] =  $allpmsdashboardchart;
                }
                ?>
                <div class="card-block">
                    <input type="hidden" id="allperformancereviewratting" value='<?php echo json_encode($allperformancereviewratting); ?>'>
                    <div id="placeholder" class="demo-placeholder" style="height:300px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5>My Team</h5>
                <div class="card-header-right">
                    <div class="input-group input-group-button mb-3">
                        <input type="text" id="filter1111" class="form-control form-control-sm width1" placeholder="Search The Name" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search The Name" aria-label="Search The Name"> 
                        <span class="input-group-text addon-style m-0">
                            <label class="form-label m-t-5"><i class="ti-search f-w-600"></i></label>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body custom-scroll" style=" height: 370px;">
                <div class="card-block">
                    <div class="row text-center" id="results">
                        <?php foreach($my_team  as $epah){?>
                        <div class="col-lg-4 mt-3 results" onclick="getMyTeamPreformance('<?php echo $epah['employee_id'];?>')">
                            <?php if($epah['image'] !=""){?>
                            <img src="images/all/<?php echo $epah['image']; ?>" class="img-radius img-80" alt="">
                             <?php }else{ ?>
                             <span alt="user image" class="img-radius align-top p-t-25 custom_txt_workentry f-30 text-center img-80 text-warning" style="width:80px"><?php echo initials($epah['display_name']); ?></span>
                             <?php } ?>
                            <h5 class="mt-2 f-16"><?php echo $epah['display_name']; ?></h5>
                        </div>
                        <?php } ?> 
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>
<?php if(isset($_POST['appraisal_id'])){ ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="assets/pages/chart/float/jquery.flot.js"></script>
    <script src="assets/pages/chart/float/jquery.flot.categories.js"></script>
    <script src="assets/pages/chart/float/jquery.flot.pie.js"></script>
    <script type="text/javascript" src="assets/js/custom_view_pms_dashboard_2.js"></script>
<?php } ?>