<?php

$_SESSION['apply_js']['Payroll_dashboard'] = '1';
$url1 = API."?mode=payrolldashboard&table=payroll_steps&pay_year=2023&pay_month=05&default_payroll_day=".$_SESSION['config']['default_payroll_day'];
$resultapi = callAPI($url1);
$result = json_decode($resultapi,1);
$payrollempcnt = $result['payrollempcnt'];
$payroll_exclusions = $result['payroll_exclusions'];
$payroll_steps = $result['payroll_steps'];
foreach($payroll_steps as $payroll_step){
    $payrollsteps[$payroll_step['category_id']] = $payroll_step;
}
$statutory = $result['statutory'];
$pay_run_summary_components = $result['pay_run_summary_components'];
$payrunsummarycomponents[] = array("Month",'Gross salary','Employeer Contribution','Reimbursement');
foreach ($pay_run_summary_components as $key => $pay_run_summary_component) {
    $grosssalary = $pay_run_summary_component['gross_salary'] !=""?$pay_run_summary_component['gross_salary']:0;
    $pfsalary = $pay_run_summary_component['pf_salary'] !=""?$pay_run_summary_component['pf_salary']:0;
    $reimbursement = $pay_run_summary_component['reimbursement'] !=""?$pay_run_summary_component['reimbursement']:0;
    $payrunsummarycomponents[] = array($key,$grosssalary,$pfsalary,$reimbursement);
}
$audit_trails = $result['audit_trails'];
foreach($audit_trails as $audit_trail){
    $audittrails[$audit_trail['page_option']]  = $audit_trail;
}
$payroll = $result['payroll'];
$todotaskarray = array(array('id'=>1,'name'=>'New Joinees','page'=>'custom_insert_payroll_addition.php','cnt'=>$payrollempcnt['this_month_join']),
    array('id'=>2,'name'=>'Separation','page'=>'custom_insert_payroll_separation.php','cnt'=>$payrollempcnt['this_month_leaving']),
    array('id'=>3,'name'=>'Employees to be excluded','page'=>'custom_insert_payroll_exclusion.php','cnt'=>$payroll_exclusions['exclusion_cnt']),
    array('id'=>4,'name'=>'Settlements','page'=>'custom_insert_payroll_settlements.php','cnt'=>$payroll_exclusions['settlement_cnt']),
    array('id'=>7,'name'=>'Salary Revisions','page'=>'custom_insert_payroll_salary_revisions.php','cnt'=>$payroll_exclusions['revised_cnt']),
    array('id'=>8,'name'=>'Update Onetime Earnings','page'=>'custom_insert_payroll_updateonetime_earnings.php'),
    array('id'=>10,'name'=>'Update Onetime Deductions','page'=>'custom_insert_payroll_updateonetime_deductions.php'),
    array('id'=>9,'name'=>'Hold Salary','page'=>'custom_insert_payroll_hold_salaray.php','cnt'=>$payroll_exclusions['hold_salary_cnt']));

$chart_data = array("Complited"=>count((array)$payrollsteps),"InProcess"=>count((array)$todotaskarray)-count((array)$payrollsteps));

?>

<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5>PayRoll</h5>
                <?php
                    $paurolldate = date($_SESSION['config']['default_payroll_day']." M Y");
                    $payrolldates = $payroll['id']==""?$paurolldate:date('d M Y', strtotime('+1 month', strtotime($paurolldate)));
                    $remainingdays = (strtotime($payrolldates)-strtotime(date('d-m-Y'))) / (60 * 60 * 24);
                     ?>
                <div class="card-header-right">
                    <span class="form-label label label-lg label-warning" style="font-size: 100%;">Pay Roll Date <?php echo $payrolldates;?> </span>
                </div>
            </div>
            <div class="card-body">
                <div class=" social-card">
                    <div class="row mt-3 mb-3">
                        <div class="col-lg-8">
                        <h5 class="text-c-yellow f-w-700  m-l-20"><?php echo $remainingdays;?> More Day To Run Pay Roll</h5>
                        </div>
                        <div class="col-lg-4">
                            <a href="index.php?option=COM_INSERT_PAYROLL_PAY_AREA_WISE" class="btn btn-success desktop-view float-end"><i class="icofont icofont-plus icon-white "></i>Run PayRoll</a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-4">
                        <div class=" social-card">
                            <div class="card-body text-center">
                                <div class="text-primary"><i class="fab fa fa-users"></i></div>
                                <h3 class="text-facebook f-w-700 mb-0"><?php echo $payrollempcnt['total_emp']; ?></h3>
                                <p>Employees</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class=" social-card">
                            <div class="card-body text-center">
                                <div class="text-info"><i class="fab fa fa-user-plus"></i></div>
                                <h3 class="text-facebook f-w-700 mb-0"><?php echo $payrollempcnt['this_month_join']; ?></h3>
                                <p>New Hires</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class=" social-card">
                            <div class="card-body text-center">
                                <div class="text-danger"><i class="fab fa fa-user-times"></i></div>
                                <h3 class="text-facebook f-w-700 mb-0"><?php echo $payrollempcnt['this_month_leaving']; ?></h3>
                                <p>Resignees</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Pay Run Process</h5>
            </div>
            <div class="card-body">
                <input type="hidden" id="payrunprocess" value='<?php echo json_encode($chart_data); ?>'>
                <div id="chart_Threshold" style="width: 100%; height: 235px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header">
                <h5>To Do Task</h5>
            </div>
            <div class="card-body  custom-scroll" style=" height: 265px;">
                <?php foreach($todotaskarray as $todotaskarrays){ ?>
                    <div class="row m-b-30 align-items-center">

                        <div class="col">
                            <a href="#!"><h6 class="m-b-5 f-14"><span class="form-label label label-md <?php echo (isset($payrollsteps[$todotaskarrays['id']]))?"bg-success":"bg-warning"; ?>" ><?php echo $todotaskarrays['cnt'] !=""?$todotaskarrays['cnt']:'<i class="ti-wallet"></i>'; ?></span> <?php echo $todotaskarrays['name']; ?> <span class="text-primary float-end f-14">View</span></h6></a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <h5>Payroll Cost Summary</h5>
            </div>
            <div class="card-body">
                <div class="card-block">
                    <input type="hidden" id="pay_run_summary_components" value='<?php echo json_encode($payrunsummarycomponents); ?>'>
                    <div id="chart_bar" style="width: 100%; height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header">
                <h5>PayRoll Reports</h5>
            </div>
            <div class="card-body custom-scroll" style=" height: 350px;">
            <?php $incr = 1; foreach($statutory as $statutoryreport){ ?>
                <div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">

                    <div class="col-lg-2">
                        <div class="p-2 bg-info b-radius-10 text-center" >
                            <?php echo $incr; ?>

                        </div>
                    </div>
                    <div class="col-lg-8 p-0">
                        <a href="index.php?option=<?php echo $statutoryreport['page_name']; ?>" class="text-primary">
                            <h6 class="f-w-600"><?php echo ucwords($statutoryreport['menu_label']); ?> </h6>
                            <span class="text-muted f-12"><?php echo (isset($audittrails[$statutoryreport['page_name']]))?$audittrails[$statutoryreport['page_name']]['created_date']:"No Visit"?></span>
                        </a>
                    </div>
                    <div class="col-lg-2">
                        <i class="ti-eye"></i>
                    </div>
                </div>
<?php $incr++;
} ?>
            </div>

        </div>
    </div>
</div>
