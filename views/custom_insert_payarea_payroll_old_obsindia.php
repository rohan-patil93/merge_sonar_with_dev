<?php 
    if( $_GET["PAY_MONTH"] == ''){
        $_GET["PAY_MONTH"] = date('n');
        $_GET["PAY_YEAR"] = date('Y');
    }   
   $array = array(
array('id'=>1,'name'=>'New Joinees','page'=>'custom_insert_payroll_addition.php'),
array('id'=>2,'name'=>'Separation','page'=>'custom_insert_payroll_separation.php'),
array('id'=>3,'name'=>'Employees to be excluded','page'=>'custom_insert_payroll_exclusion.php'),
array('id'=>4,'name'=>'Settlements','page'=>'custom_insert_payroll_settlements.php'),
array('id'=>5,'name'=>'Salary Revisions','page'=>'custom_insert_payroll_salary_revisions.php'),
array('id'=>6,'name'=>'Update Onetime Earnings','page'=>'custom_insert_payroll_updateonetime_earnings.php'),
array('id'=>7,'name'=>'Update Onetime Deductions','page'=>'custom_insert_payroll_updateonetime_deductions.php'),
array('id'=>8,'name'=>'Hold Salary','page'=>'custom_insert_payroll_hold_salaray.php'));
    $process = $_GET['payroll']=='process' ? 'process' : '';
    if( $_GET["REG"] == 'R'){
       $payroll = API."?mode=payareapayrolldetails&pay_year=".$_GET["PAY_YEAR"]."&pay_month=". $_GET["PAY_MONTH"]."&PPJ=". $_GET["PPJ"]."&payroll=".$process;
        $cusresponse = callAPI($payroll); 
        header("location:index.php?option=COM_INSERT_PAYAREA_PAYROLL&PAY_YEAR=".$_GET['PAY_YEAR']."&PAY_MONTH=".$_GET['PAY_MONTH']."&PPJ=".$_GET["PPJ"]); exit;
    }
    $payroll = API."?mode=listoutpayareapayrolldetails&pay_year=".$_GET["PAY_YEAR"]."&pay_month=". $_GET["PAY_MONTH"]."&PPJ=".$_GET["PPJ"]."&payroll=".$process;
    $cusresponse = callAPI($payroll);
    $datas = json_decode($cusresponse,1);
 
    foreach ($datas['employees'] as $emp) {
        $empdata[$emp['id']] = $emp['display_name'];
        $empcodedata[$emp['id']] = $emp['emp_code'];
    } 
    foreach ($datas['pay_areas'] as $pmp) {
        $patyareadata[$pmp['id']] = $pmp['name'];
    }
    //print_r($patyareadata);
?>
<style>
.font-weight-bold{
    font-size: 14px;
    color: black;
}
</style>
<script type="text/javascript">
    function checksdisplay(pid){
        var count = $('#count').val();
        for(var i=1; i<count; i++){
            if(pid==i){
              $('#payarea_'+i).show();
              document.getElementById('tabp_'+i).style.backgroundColor = 'blue' ;
              document.getElementById('tabp_'+i).style.borderColor  = 'blue' ; 
              document.getElementById('tabp_'+i).style.color  = 'white' ;
            }else{
              $('#payarea_'+i).hide();
                document.getElementById('tabp_'+i).style.backgroundColor = 'white' ;
              document.getElementById('tabp_'+i).style.borderColor  = 'white' ; 
              document.getElementById('tabp_'+i).style.color  = 'black' ;
            }
        }
    }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="flex-row-fluid ml-lg-8  ">
                <div class="row ">
                    <div class=" card card-body card-custom">
                        <div class=" mb-5">
                            <?php $m=1;
                                 foreach($datas as $pkey=>$data){           
                                    if($m==1){
                                        $class = 'btn btn-primary';
                                    }else{
                                        $class = 'btn btn-light';
                                    }              
                            ?>
                            <div class="float-left m-r-5 b-radius-5 nav-item ">
                                <a  onclick="checksdisplay(<?php echo $m; ?>)" id="tabp_<?php echo $m; ?>" class="<?php echo $class; ?> nav-link  me-6 active fs-12" href="#">  
                                    <?php echo $patyareadata[$pkey]; ?>
                                </a>
                            </div>
                            <?php $m++; } ?>
                            <input type="hidden" name="" id="count" value="<?php echo $m; ?>">
                            <input type="hidden" name="" id="pay_area_id" value="<?php echo $_GET['PPJ']; ?>">
                        </div> 
                        <div class="row">
                            <div class="card-header table-card-header ">
                                <h5 class=""> 
                                <b><?php echo date("F", mktime(0, 0, 0, $_GET['PAY_MONTH'], 10))." ".$_GET["PAY_YEAR"]; ?>  Payroll</b>
                                <?php 
                                    $date2 = date("Y-m-d", strtotime('+1 months', strtotime($date1)));
                                    $date2 = date("Y-m-d", strtotime('-1 days', strtotime($date2)));
                                ?>                               
                            </div>
                        </div>
                        <?php $m=1; foreach((array)$datas as $pkey=>$data){ 
                            $payroll_data = $data['payroll'];
                            $payroll_ex = $data['exclusion'];
                            $payroll_steps = $data['payroll_steps'];
                            //echo "<pre>"; print_r($payroll_ex); echo "</pre>";
                            if($data['status']==3){  ?>
                                <div id="payarea_<?php echo $m; ?>" <?php if($m!=1){ ?> style="display: none;" <?php } ?>>
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
                                <span class="font-weight-bold float-right"><a href="csv_bank_payment_download.php?pay_year=<?php echo $_GET['PAY_YEAR']."&pay_month=".$_GET['PAY_MONTH']; ?>&pay_area_id=<?php echo $_GET['PPJ']; ?>" target="_blank" class="btn btn-success">Download</a></span>
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
                                        <input type="radio" name="options" id="option1" checked="checked"/> Unlock
                                    </label>
                                    <label class="btn btn-success">
                                        <input type="radio" name="options" id="option2"/> Lock
                                    </label>
                                </div>
                                <hr>
                                <span class="font-weight-bold">Employee View Realese:</span>
                                <div class="btn-group btn-group-toggle float-right pay-lock" data-toggle="buttons" style="margin-top: -10px;">
                                    <label class="btn btn-success active">
                                        <input type="radio" name="options" id="option1"/> Release
                                    </label>
                                    <label class="btn btn-success">
                                        <input type="radio" name="options" id="option2" checked="checked"/> Hold
                                    </label>
                                </div>
                                <hr>
                                <span class="font-weight-bold">IT Settlements employee view:</span>
                                <div class="btn-group btn-group-toggle float-right pay-lock" data-toggle="buttons" style="margin-top: -10px;">
                                    <label class="btn btn-success active">
                                        <input type="radio" name="options" id="option1"/> Release
                                    </label>
                                    <label class="btn btn-success">
                                        <input type="radio" name="options" id="option2" checked="checked"/> Hold
                                    </label>
                                </div>
                                <hr>
                                <span class="font-weight-bold">Payroll:</span>
                                <div class="btn-group btn-group-toggle float-right pay-lock" data-toggle="buttons" style="margin-top: -10px;">
                                    <label class="btn btn-success active">
                                        <input type="radio" name="options" id="option1" checked="checked"/> Unlock
                                    </label>
                                    <label class="btn btn-success">
                                        <input type="radio" name="options" id="option2"/> Lock
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-4">
                                <p class="text-dark-75 font-weight-bolder font-size-h5 m-0">Exceptions</p>
                                <?php foreach ($payroll_ex as $exclusion) {
                                if($exclusion['category_id'] == 3){ ?>
                                    <div class="d-flex align-items-center mb-4 mt-5">
                                        <div class="d-flex flex-column font-weight-bold">
                                            <a href="#" class="text-dark text-hover-primary mb-1 font-size-lg"><?php echo  $empdata[$exclusion['emp_id']]."  (".
                                            $empcodedata[$exclusion['emp_id']].")";?></a>
                                        </div>
                                    </div>
                                <?php }  } ?>
                            </div>
                            <div class="col-md-4">
                                <p class="text-dark-75 font-weight-bolder font-size-h5 m-0">Hold Salary Payout</p>
                                <?php foreach ($payroll_ex as $exclusion) {
                                    if($exclusion['category_id'] == 8){ ?>
                                        <div class="d-flex align-items-center mb-4 mt-5">
                                            <div class="d-flex flex-column font-weight-bold">
                                                <a href="#" class="text-dark text-hover-primary mb-1 font-size-lg"><?php echo  $empdata[$exclusion['emp_id']]." ( ".
                                                $empcodedata[$exclusion['emp_id']].")";?></a>
                                            </div>
                                        </div>
                                <?php }  } ?>
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
                 <input readonly type="checkbox" <?php if(in_array($steps['id'], (array)$payroll_steps)){ echo "checked"; } ?>    name="Checkboxes1" disabled="">
                                                            <span class="cr">
                                                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                          </span><span><?php echo $steps['name']; ?></span>
                                                      </label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                                 </div>
                        <?php }else{ ?>
                            <!-- <div class="f-24 text-danger text-center"><?php // echo $_SESSION['error_message']['8009']; ?> </div> -->
                        <?php } $m++; } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  
