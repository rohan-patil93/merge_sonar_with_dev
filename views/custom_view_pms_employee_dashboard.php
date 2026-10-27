<?php
$apiurl = API."?mode=getpmsemployeedashboard&employee_id=".$_SESSION['emp_id'];
$apicallss = callAPI($apiurl);
$result = json_decode($apicallss,1);
$total_cnt = $result['total_cnt'];
$employee_performance_appraisals = $result['employee_performance_appraisals'];
$employee_goals = $result['employee_goals'];
$employee_feedbacks = $result['employee_feedbacks'];
$arslabs  = $result['arslabs'];
?>
<script type="text/javascript">
    function getEmployeeGoals(emp_goal_id,emp_id){
        jQuery.ajax({
            url: "views/custom_insert_employee_goals_popup.php",
            data:'emp_goal_id='+emp_goal_id+'&emp_id='+emp_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $("#kt_modal_dynamic_popup").modal("show");
            },
            error:function (){}
        });
    }
    function getEmployeeFeedbacks(emp_fd_id, emp_id){
        jQuery.ajax({
            url: "views/custom_insert_employee_feedbacks_popup.php",
            data:'emp_fd_id='+emp_fd_id+'&emp_id='+emp_id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $("#kt_modal_KTDatatable_remote").modal("show");
                $(".js-example-basic-single").select2();
            },
            error:function (){}
        });
    }
</script>
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card prod-p-card card-red">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-b-5 text-white">Total Employess</h6>
                        <h3 class="m-b-0 f-w-700 text-white">1</h3>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-alt text-c-red f-18"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card prod-p-card card-blue">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-b-5 text-white">Total Appraisals</h6>
                        <h3 class="m-b-0 f-w-700 text-white"><?php echo $total_cnt['total_appraisals']; ?></h3>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-alt text-c-red f-18"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card prod-p-card card-green">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col col-lg-5">
                        <h6 class="m-b-5 text-white f-14 text-center">Self Goals</h6>
                        <div class="row align-items-center text-center">
                            <div class="col">
                                <h3 class="m-b-0 text-white"><?php echo $total_cnt['total_self_goals']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col col-lg-2 text-center">
                        <i class="fas fa-exchange-alt text-c-green f-18"></i>
                    </div>
                    <div class="col col-lg-5">
                        <h6 class="m-b-5 text-white f-14 text-center">Total Publish</h6>
                        <div class="row align-items-center text-center">
                            <div class="col">
                                <h3 class="m-b-0 text-white"><?php echo $total_cnt['total_shared_goals']?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card prod-p-card card-yellow">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col col-lg-5">
                        <h6 class="m-b-5 text-white f-14 text-center">Request FeedBacks</h6>
                        <div class="row align-items-center text-center">
                            <div class="col">
                                <h3 class="m-b-0 text-white"><?php echo $total_cnt['total_self_feedbacks']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col col-lg-2 text-center">
                        <i class="fas fa-exchange-alt text-c-green f-18"></i>
                    </div>
                    <div class="col col-lg-5">
                        <h6 class="m-b-5 text-white f-14 text-center">Received FeedBacks</h6>
                        <div class="row align-items-center text-center">
                            <div class="col">
                                <h3 class="m-b-0 text-white"><?php echo $total_cnt['total_feedback']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>My Appraisals</h5>
                <div class="card-header-right table-card-header">
                    <span class="text-c-yellow f-w-700">Count Of Appraisals : <?php echo count((array)$employee_performance_appraisals); ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive custom-scroll" style=" height: 450px;">
                    <table class="table table-hover m-b-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th scope="col" class="w-b-w-s" style="width: 70%;">Appraisal</th>
                                <th scope="col" style="width: 15%;">Self Score</th>
                                <th scope="col" style="width: 15%;">RO Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($employee_performance_appraisals as $epa){
                                ?>
                                <tr>
                                    <td class="p-2 w-b-w-s">
                                        <a href="index.php?option=COM_INSERT_EMPLOYEE_PERFORMANCE_APPRAISALS&Rid=<?php echo deocdeId('id:'.$epa['id']); ?>" target="_blank"> <i class="fa fa-edit"></i>
                                            <?php echo $epa['performance_appraisal_name']; ?>
                                        <p><small><?php echo fromDate($eg['start_date']); ?></small>
                                        To <small><?php echo fromDate($eg['end_date']); ?></small></p>
                                    </a>
                                    </td>
                                    <td class="p-2 text-right">
                                        <?php
                                        foreach($arslabs as $slab){
                                            if($slab['start_range'] <= $epa['self_total'] && $slab['end_range'] >= $epa['self_total']){  ?>
                                               <span class="label label-lg label-inline f-w-700" style="background-color:<?php echo $slab['background_color'];?> "><?php echo $slab['slabs_name'];?></span>

                                            <?php }
                                        }
                                        ?>
                                    </td>
                                    <td class="p-2 text-right">
                                         <?php
                                        if($epa['current_status'] == ST_APPROVED){
                                            foreach($arslabs as $slab){
                                                if($slab['start_range'] <= $epa['ro_total'] && $slab['end_range'] >= $epa['ro_total']){  ?>
                                                   <span class="label label-lg label-inline f-w-700" style="background-color:<?php echo $slab['background_color'];?> "><?php echo $slab['slabs_name'];?></span>

                                                <?php }
                                            }
                                        }else{ echo " - ";
}  ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>My Goals</h5>
                <div class="card-header-right table-card-header">
                    <span class="text-c-yellow f-w-700">Count Of Goals : <?php echo count((array)$employee_goals); ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive custom-scroll" style=" height: 450px;">
                    <table class="table table-hover m-b-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th scope="col" class="w-b-w-s" style="width: 70%;">Goal</th>
                                <th scope="col" style="width: 30%;">Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($employee_goals as $eg){
                                ?>
                                <tr class="cursor-pointer" onclick="getEmployeeGoals('<?php echo $eg['id']; ?>','<?php echo $eg['employee_id']?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                    <td class="p-2 w-b-w-s">
                                        <i class="fa fa-edit"></i>
                                        <?php echo $eg['goal_name']; ?>
                                        <p><small><?php echo fromDate($eg['goal_start_date']); ?></small> TO <small><?php echo fromDate($eg['goal_end_date']); ?></small></p>
                                    </td>
                                    <td class="p-2">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                                    <span class="">Progress</span>
                                                    <span class=""><?php echo $eg['percentage_of_completion']; ?>%</span>
                                                </div>
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-c-green" style="width:<?php echo $eg['percentage_of_completion']; ?>%"></div>

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>My FeedBacks</h5>
                <div class="card-header-right table-card-header">
                    <span class="text-c-yellow f-w-700">Count Of FeedBacks : <?php echo count((array)$employee_feedbacks); ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive custom-scroll" style=" height: 450px;">
                    <table class="table table-hover m-b-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 10%;">#</th>
                                <th scope="col" class="w-b-w-s" style="width: 80%;">FeedBacks</th>
                                <th scope="col" style="width: 10%;">Response</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $efseq = 1; foreach($employee_feedbacks as $ef){
                                ?>
                                <tr onclick="getEmployeeFeedbacks('<?php echo $ef['id']; ?>','<?php echo $ef['employee_id']?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                    <td class="p-2"><i class="fa fa-edit"></i> <?php echo $efseq; ?></td>
                                    <td class="p-2 w-b-w-s">
                                        <?php echo $ef['question']; ?>
                                    </td>
                                    <td class="p-2  text-center">
                                        <?php if($ef['response_status']==''){ echo "Pendding";
}else{  ?>
                                            <i class="ti-eye f-16" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View FeedBack" onclick="getEmployeeFeedbacks('<?php echo $ef['id']; ?>','<?php echo $ef['employee_id']?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php $efseq++;
} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
