<?PHP
$admintype=$_SESSION['admin_type'];
$url = API."?mode=getperformanceappraisalreport&emp_id=".$_SESSION['emp_id']."&admin_type=".$admintype;
$response = callAPI($url);
$perapprreport = json_decode($response,1);
$performance_appraisal_setups  = $perapprreport['performance_appraisal_setups'];
$performancesetupary= array();
foreach($performance_appraisal_setups as $performance_sret){
    $performancesetupary[$performance_sret['assessment_year']][] = $performance_sret;
}
$performance_appraisal_setups_self  = $perapprreport['performance_appraisal_setups_self'];
$performancesetupselfary= array();
foreach($performance_appraisal_setups_self as $performance_sret_self){
    $performancesetupselfary[$performance_sret_self['assessment_year']][] = $performance_sret_self;
}
$transaction_approval_logs = $perapprreport['transaction_approval_logs'];
foreach($transaction_approval_logs as $tal){
    if($tal['response_type'] == 0){
        $transactionapprovallogs['pending'][] = $tal;
    }else{
        $transactionapprovallogs['approved'][] = $tal;
    }
}
?>
<script type="text/javascript">
    function showAppraisalList(assessment_year,id,type){
        $.ajax({
            url :'views/custom_list_performance_appraisals.php',
            type:'post',
            data : 'assessment_year='+assessment_year+"&type="+type,
            success:function(respons){
                $("#"+id).html(respons);

                $(".empty_"+id).removeClass('bg-primary');
                $("#"+id+"_1_"+assessment_year).addClass('bg-primary');
            }

        })
    }
</script>
<div class="card" id="kt_page_sticky_card">
    <div class="card-header">
        <h5>Performance Appraisals</h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>  Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <ul class="nav md-tabs infisuite_tab">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#appraisal_setting_team" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Team Appraisals</a>
                <div class="slide"></div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#appraisal_setting_self" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Self Appraisals</a>
                <div class="slide"></div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#approvalpayments" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Approval Payments</a>
                <div class="slide"></div>
            </li>
        </ul>
        <div class="tab-content mt-4">
            <div class="tab-pane card-body active" id="appraisal_setting_team" role="tabpanel">
                <div class="row">
                    <?php if(count((array)$performancesetupary) != 0){  foreach($performancesetupary as $assetyaerkey=>$performancesetu){?>
                    <div class="col-lg-2 col-md-4 col-sm-6 col-12" onclick="showAppraisalList('<?php echo $assetyaerkey; ?>','pageviewteam','T')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <div class="card comp-card">
                            <div class="card-body empty_pageviewteam" id="pageviewteam_1_<?php echo $assetyaerkey; ?>">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-b-25">
                                            <a> <?php echo $assetyaerkey; ?></a>
                                        </h6>
                                    </div>
                                    <div class="col-auto" data-container="body" data-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Count Of Appraisals" >
                                        <i class="fas bg-c-blue" ><?php echo count((array)$performancesetu); ?></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php } else { ?>
                    <div class="card-px text-center py-20 mt-2">
                        <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Team Appraisals </p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                    <?php } ?>
                    <div class="row" id="pageviewteam">
                    </div>
                </div>
            </div>
            <div class="tab-pane card-body" id="appraisal_setting_self" role="tabpanel">
                <div class="row">
                    <?php if(count((array)$performancesetupselfary) != 0){ foreach($performancesetupselfary as $assetyaerkey=>$performancesetupary){?>
                    <div class="col-xl-2" onclick="showAppraisalList('<?php echo $assetyaerkey; ?>','pageviewself','S')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <div class="card comp-card">
                            <div class="card-body empty_pageviewself" id="pageviewself_1_<?php echo $assetyaerkey; ?>">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-b-25"> <a> <?php echo $assetyaerkey; ?></a></h6>
                                    </div>
                                    <div class="col-auto" data-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Count Of Appraisals" >
                                        <i class="fas bg-c-blue"><?php echo count((array)$performancesetupary); ?></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php } else { ?>
                    <div class="card-px text-center py-20 mt-2">
                        <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Self Appraisals </p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                    <?php } ?>
                    <div class="row" id="pageviewself">
                    </div>
                </div>
            </div>
            <div class="tab-pane card-body p-0" id="approvalpayments" role="tabpanel">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12">
                        <div class="card table-card">
                            <div class="card-header">
                                <h5>Approval Pending</h5>
                            </div>
                            <div class="card-body custom-scroll" style="max-height: 250px; min-height: 250px;">
                                <div class="card-block table-responsive" >
                                    <table id="basic-btn" class="table table-hover m-b-0" style="font-size:13px;">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Request Date</th>
                                                <th scope="col">Transaction Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $mq = 1; if(count((array)$transactionapprovallogs['pending'])!='0'){
                                                foreach($transactionapprovallogs['pending'] as $empe){ $ridrecengtbils = deocdeId("id:".$empe['id']);
                                                ?>
                                            <tr>
                                                <td><?php echo $mq; ?></td>
                                                <td><a href="index?option=COM_INSERT_EMPLOYEE_PERFORMANCE_APPRAISALS&Rid=<?php echo $ridrecengtbils; ?>" class="text-primary"><?php echo $empe['display_name']; ?></a></td>
                                                <td><?php echo fromDate($empe['request_date']); ?></td>
                                                <td><?php echo fromDate($empe['payment_date']); ?></td>
                                            </tr>
                                            <?php $mq++;
}
}else{
                                                echo "<tr><td colspan='7' class='text-center'><h4 ><i class='fa fa-warning'></i> No Approval Pending</h4></td></tr>";
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12">
                        <div class="card table-card">
                            <div class="card-header">
                                <h5>Approved</h5>
                            </div>
                            <div class="card-body custom-scroll" style="max-height: 250px; min-height: 250px;">
                                <div class="card-block table-responsive" >
                                    <table  id="basic-btn_2"  class="table table-hover m-b-0" style="font-size:13px;">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Response Date</th>
                                                <th scope="col">Transaction Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $mq = 1; if(count((array)$transactionapprovallogs['approved'])!='0'){
                                                foreach($transactionapprovallogs['approved'] as $empe){
                                                    $ridrecengtbils = deocdeId("id:".$empe['id']);
                                                    ?>
                                            <tr>
                                                <td><?php echo $mq; ?></td>
                                                <td><a href="index?option=COM_INSERT_EMPLOYEE_PERFORMANCE_APPRAISALS&Rid=<?php echo $ridrecengtbils; ?>" class="text-primary"><?php echo $empe['display_name']; ?></a></td>
                                                <td><?php echo fromDate($empe['response_date']); ?></td>
                                                <td><?php echo fromDate($empe['payment_date']); ?></td>
                                            </tr>
                                            <?php $mq++;
}
}else{
                                                echo "<tr><td colspan='7' class='text-center'><h4 ><i class='fa fa-warning'></i> No Approved</h4></td></tr>";
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

