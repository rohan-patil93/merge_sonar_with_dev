<?php

    if( $_GET["PAY_MONTH"] == ''){
        $_GET["PAY_MONTH"] = date('n');
        $_GET["PAY_YEAR"] = date('Y');
    }

    $array = array(array('id'=>1,'name'=>'New Joinees','page'=>'custom_insert_payroll_addition.php'),array('id'=>2,'name'=>'Separation','page'=>'custom_insert_payroll_separation.php'),array('id'=>3,'name'=>'Employees to be excluded','page'=>'custom_insert_payroll_exclusion.php'),array('id'=>4,'name'=>'Settlements','page'=>'custom_insert_payroll_settlements.php'),array('id'=>7,'name'=>'Salary Revisions','page'=>'custom_insert_payroll_salary_revisions.php'),array('id'=>8,'name'=>'Update Onetime Earnings','page'=>'custom_insert_payroll_updateonetime_earnings.php'),array('id'=>8,'name'=>'Update Onetime Deductions','page'=>'custom_insert_payroll_updateonetime_deductions.php'),array('id'=>9,'name'=>'Hold Salary','page'=>'custom_insert_payroll_hold_salaray.php'));
    $process = $_GET['payroll']=='process' ? 'process' : '';
    if( $_GET["REG"] == 'R'){
        $payroll = API."?mode=regenratepayrolldetails&pay_year=".$_GET["PAY_YEAR"]."&pay_month=". $_GET["PAY_MONTH"]."&payroll=".$process;
        $cusresponse = callAPI($payroll);
        header("location:index.php?option=COM_INSERT_PAYROLL_STEPS&PAY_YEAR=".$_GET['PAY_YEAR']."&PAY_MONTH=".$_GET['PAY_MONTH']); exit;
    }
    $payroll = API."?mode=payrolldetails&pay_year=".$_GET["PAY_YEAR"]."&pay_month=". $_GET["PAY_MONTH"]."&payroll=".$process;
    $cusresponse = callAPI($payroll);
    $data = json_decode($cusresponse,1);
    $payroll_data = $data['payroll'];
    $payroll_ex = $data['exclusion'];

    $epwr["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0);
    $epwrs = urlencode(json_encode($epwr));
    $eurl = API."?mode=gettabledetailswithconditions&table=employees&data=".$epwrs;
    $epdatas = callAPI($eurl);
    $epdatas = json_decode($epdatas,1);
    foreach ($epdatas as $emp) {
        $empdata[$emp['id']] = $emp['display_name'];
        $empcodedata[$emp['id']] = $emp['display_name'];
    }

    ?>
    <style>
    .font-weight-bold{
        font-size: 14px;
        color: black;

    }

</style>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="col-lg-12">
            <!--begin::Content-->
            <div class="flex-row-fluid ml-lg-8  ">
                <!--begin::Row-->
                <div class="row ">
                    <div class=" card card-body card-custom">
                        <div class=" mb-5">
                            <?php
                            $month = strtotime(date('Y').'-'.date('m').'-01 -12 months');
                            $end = strtotime(date('Y').'-'.date('m').'-01 +0 months');
                            $monthd = $month; $k=1;
                            while($month <= $end){
                                $disss = date('M Y',$month);
                                $dis_year = date('Y',$month);
                                $dis_month = date('n',$month);

                                if($_GET['PAY_YEAR'] == $dis_year && $_GET['PAY_MONTH'] == $dis_month ){
                                   $class = 'btn btn-primary';
                               }else{
                                $class = 'btn btn-light';
                            }

                            ?>
                            <div class="float-left m-r-5 b-radius-5 nav-item ">
                             <a class="<?php echo $class; ?> nav-link  me-6 active fs-12" href="index.php?option=COM_INSERT_PAYROLL&PAY_YEAR=<?php echo $dis_year; ?>&PAY_MONTH=<?php echo $dis_month; ?>"> <?php echo $disss; ?></a>
                         </div>
                         <?php $month = strtotime("+".$k." month", $monthd);  $k++;
} ?>

                     </div>
                     <div class="row">
                        <div class="card-header table-card-header ">
                            <h5 class="">
                                <b>
                                    <?php echo date("F", mktime(0, 0, 0, $_GET['PAY_MONTH'], 10))." ".$_GET["PAY_YEAR"]; ?>  Payroll
                                </b>
                            <?php
                            $date2 = date("Y-m-d", strtotime('+1 months', strtotime($date1)));
                            $date2 = date("Y-m-d", strtotime('-1 days', strtotime($date2)));
                            ?>

                            <?php if(count((array)$payroll_data)>0){ ?>
                               <div class="card-header-right table-card-header ">

                              </div>
                          <?php } ?>

                      </div>

                    </div>
                </form>
                <?php if($data['status']==3){ ?>
                    <div class="row ">
                        <div class="col-md-4">
                            <div class="font-size-lg text-muted font-weight-bold mt-1"><b>Net Payout</b></div>
                            <h4 class="" style="    color: black;">Rs. <?php echo customeummarmonyformate($payroll_data['gross_pay']-$payroll_data['deductions']); ?></h4>
                        </div>

                    </div>
                    <div class="row mt-5">
                        <div class="col-md-4">
                            <hr>
                            <span class="font-weight-bold">Gross Pay</span>
                            <span class="font-weight-bold float-right"><?php echo customeummarmonyformate($payroll_data['gross_pay']);?></span>
                            <hr>
                            <span class="font-weight-bold">Deductions</span>
                            <span class="font-weight-bold float-right">-<?php echo customeummarmonyformate($payroll_data['deductions']);?></span>
                            <hr>
                            <span class="font-weight-bold">Work Days</span>
                            <span class="font-weight-bold float-right"><?php echo $payroll_data['work_days'];  ?></span>
                <hr>
                            <span class="font-weight-bold">Salary Statements</span>
                            <span class="font-weight-bold float-right"><a href="csv_bank_payment_download.php?pay_year=<?php echo $_GET['PAY_YEAR']."&pay_month=".$_GET['PAY_MONTH']; ?>" target="_blank" class="btn btn-success">Download</a></span>
                        </div>
                        <div class="col-md-4">
                            <hr>
                            <span class="font-weight-bold">Addition</span>
                            <span class="font-weight-bold float-right"><?php echo $payroll_data['addition'];  ?></span>
                            <hr>
                            <span class="font-weight-bold">Separation</span>
                            <span class="font-weight-bold float-right"><?php echo $payroll_data['separation'];  ?></span>
                            <hr>
                            <span class="font-weight-bold">Exclusion</span>
                            <span class="font-weight-bold float-right"><?php echo $payroll_data['exclusion'];  ?></span>
                            <hr>
                            <span class="font-weight-bold">Settlements</span>
                            <span class="font-weight-bold float-right"><?php echo $payroll_data['settlements'];  ?></span>
                        </div>
                        <div class="col-md-4">
                            <hr>
                            <span class="font-weight-bold">Payroll input:</span>
                            <div class="btn-group btn-group-toggle float-right pay-lock" data-toggle="buttons" style="margin-top: -10px;">
                                <label class="btn btn-success active">
                                    <input aria-label="Options" type="radio" name="options" id="option1" checked="checked"/> Unlock
                                </label>
                                <label class="btn btn-success">
                                    <input aria-label="Options" type="radio" name="options" id="option2"/> Lock
                                </label>
                            </div>
                            <hr>
                            <span class="font-weight-bold">Employee View Realese:</span>
                            <div class="btn-group btn-group-toggle float-right pay-lock" data-toggle="buttons" style="margin-top: -10px;">
                                <label class="btn btn-success active">
                                    <input aria-label="Options" type="radio" name="options" id="option1_2"/> Release
                                </label>
                                <label class="btn btn-success">
                                    <input aria-label="Options" type="radio" name="options" id="option2_2" checked="checked"/> Hold
                                </label>
                            </div>
                            <hr>
                            <span class="font-weight-bold">IT Settlements employee view:</span>
                            <div class="btn-group btn-group-toggle float-right pay-lock" data-toggle="buttons" style="margin-top: -10px;">
                                <label class="btn btn-success active">
                                    <input aria-label="Options" type="radio" name="options" id="option1_3"/> Release
                                </label>
                                <label class="btn btn-success">
                                    <input aria-label="Options" type="radio" name="options" id="option2_3" checked="checked"/> Hold
                                </label>
                            </div>
                            <hr>
                            <span class="font-weight-bold">Payroll:</span>
                            <div class="btn-group btn-group-toggle float-right pay-lock" data-toggle="buttons" style="margin-top: -10px;">
                                <label class="btn btn-success active">
                                    <input aria-label="Options" type="radio" name="options" id="option1_4" checked="checked"/> Unlock
                                </label>
                                <label class="btn btn-success">
                                    <input aria-label="Options" type="radio" name="options" id="option2_4"/> Lock
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-4">
                            <p class="text-dark-75 font-weight-bolder font-size-h5 m-0">Exceptions</p>
                            <?php foreach ($payroll_ex as $exclusion) {
                                if($exclusion['category_id'] == 5){ ?>
                                    <div class="d-flex align-items-center mb-4 mt-5">
                                        <div class="d-flex flex-column font-weight-bold">
                                            <a href="#" class="text-dark text-hover-primary mb-1 font-size-lg"><?php echo  $empdata[$exclusion['emp_id']]." ".
                                            $empcodedata[$exclusion['emp_id']];?></a>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="col-md-4">
                                <p class="text-dark-75 font-weight-bolder font-size-h5 m-0">Hold Salary Payout</p>
                                <?php foreach ($payroll_ex as $exclusion) {
                                    if($exclusion['category_id'] == 2){ ?>
                                        <div class="d-flex align-items-center mb-4 mt-5">
                                            <div class="d-flex flex-column font-weight-bold">
                                                <a href="#" class="text-dark text-hover-primary mb-1 font-size-lg"><?php echo  $empdata[$exclusion['emp_id']]." ".
                                                $empcodedata[$exclusion['emp_id']];?></a>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php } ?>

                                </div>
                                <div class="col-lg-4">
                                    <p class="text-dark-75 font-weight-bolder font-size-h5 m-0">To Do</p>
                                    <form class="form">
                                        <div class="form-group  col-form-label">
                                            <div class="checkbox-inline">
                                                <?php foreach ($array as $steps) { ?>
                                                    <div class="col-lg-12 mt-2">
                                                        <div class="checkbox-fade fade-in-primary">
                                                            <label class="form-label">
                                                                <input aria-label="Checkboxes1" readonly type="checkbox" checked name="Checkboxes1" disabled="">
                                                                <span class="cr">
                                                                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                              </span><span><?php echo $steps['name']; ?></span>
                                                          </label>
                                                      </div>
                                                  <?php } ?>
                                              </div>

                                          </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }elseif($data['status']==2){ ?>
             <div class="col-lg-12 text-center" >
               <a target="_blank"  href="index.php?option=COM_INSERT_PAYROLL&payroll=process&PAY_YEAR=<?php echo $_GET['PAY_YEAR']; ?>&PAY_MONTH=<?php echo $_GET['PAY_MONTH']; ?>"  class="btn btn-primary btn-shadow-hover font-weight-bold mr-2">
                <?php echo "Payroll Process";  ?></a>
            </div>
        <?php } else{?>
          <div class="col-lg-5 mr-auto ml-auto" style="margin-left: 30%;">
            <div class="card card-custom"  style="padding: 80px;">
                <div class="card-body text-center bg-primary">
                    <a class="text-white font-size-h6" href="index.php?option=COM_INSERT_PAYROLL_STEPS&PAY_YEAR=<?php echo $_GET['PAY_YEAR']; ?>&PAY_MONTH=<?php echo $_GET['PAY_MONTH']; ?>"><i class="fas fa-plus"></i> Genrate Payroll For <?php echo date("F", mktime(0, 0, 0, $_GET['PAY_MONTH'], 10))." ".$_GET["PAY_YEAR"]; ?></a>
                </div>
            </div>
        </div>
    <?php }?>
</div>
</div>
</div>
</div>
</form>
</div>
</div>
