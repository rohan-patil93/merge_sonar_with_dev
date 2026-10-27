<?php
$_SESSION['apply_js']['payslipchart']=1;
if(isset($_POST['emp_id']) && $_POST['emp_id']!=""){
  $employee_id =$_POST['emp_id'];
}else{
  $employee_id =$_SESSION['emp_id'];
}

/**
*
itd_code = 'ITD' is IT declartion
salary_component_type = 0 is earnings
salary_component_type = 1 is deductions
reimbursement_component_id
*
**/

$financial_year = date('Y');
$assessment_year = getAssessmentYear();
$itd_code = 'ITD';
$component_type = 'SP';
$application_page_name = 'custom_list_payslip';

/** Get ITD component ID **/



/** Get Reimbursement component ID **/


/** Get ITD declaration Tax amount **/


$whrcon["where"] = array('company_id'=> $_SESSION['company_id'],'emp_id'=>$employee_id,'assessment_year'=>$assessment_year,'is_active'=>0);
$vdata = urlencode(json_encode($whrcon));
$url = API."?mode=gettabledetailswithconditions&table=emp_it_declaration&data=".$vdata;
$response = callAPI($url);
$data6 = json_decode($response,1);
$itd_tax = $data6[0];
$itd_tax_amount = $itd_tax['declaration_tax'];
$total_earnings_in_current_year = 0;
$total_deductions_in_current_year = 0;
$total_due_in_current_year = 0;
$total_tax_paid_in_current_year = 0;
$total_perks_in_current_year = 0;
$total_tax_due_in_current_year = 0;


$whrcon["where"] = array('company_id'=> $_SESSION['company_id'],'employee_id'=>$_SESSION['emp_id'],'is_active'=>0,
  'year(status_date)'=>$financial_year);
$vdata = urlencode(json_encode($whrcon));
$url = API."?mode=gettabledetailswithconditions&table=salary_template&data=".$vdata;
$response = callAPI($url);
$data3 = json_decode($response,1);


$m_total = $data3[0];
$nid = "id:".$_SESSION['emp_id'];
$calslab_total = $m_total['annual_ctc'] - $itd_tax_amount;

$whrcon["where"] = array('company_id'=> $_SESSION['company_id'],'assessment_year'=>$assessment_year,'is_active'=>0);
$vdata = urlencode(json_encode($whrcon));
$url = API."?mode=gettabledetailswithconditions&table=tax_slads&data=".$vdata;
$response = callAPI($url);
$tax_slab = json_decode($response,1);
$cal_intax = 0;
if($calslab_total >0){
  foreach ($tax_slab as $slab) {
   if($slab['slad_from'] < $calslab_total && $calslab_total < $slab['slad_to'] ){
     $cal_amount = $calslab_total - ($slab['slad_from']-1);
     $cal_intax = $cal_intax + ($cal_amount * ($slab['rate']/100));
     break;
   }else{
     $cal_amount = $slab['slad_to'] - ($slab['slad_from']-1);
     $cal_intax = $cal_intax + ($cal_amount * ($slab['rate']/100));
   }
 }
}


$total_tax_paid_in_current_year = 0; // Need to get it from API call


$total_tax_paid_in_current_year = 0; // Need to get it from API call
$startmonths = date("Y-m-d",strtotime("-6 months", strtotime(date('d-m-Y'))));
$endmonths = date("Y-m-d",strtotime("-0 months", strtotime(date('d-m-Y'))));
 $url = API."?mode=getpayrunsummarysalary&startdate=".$startmonths."&enddate=".$endmonths."&employee_id=".$employee_id."&pay_year=".$financial_year."&itd_code=".$itd_code."&component_type=".$component_type."&assessment_year=".$assessment_year;
$response = callAPI($url);
$data1 = json_decode($response,1);
$cal_intax = $data1['cal_intax'];
$payslip_array = $data1['payslip_array'];
$total_monthly_earnings  = $data1['total_monthly_earnings'];
$total_earnings_in_current_year= $data1['total_earnings_in_current_year'];
$total_monthly_deductions= $data1['total_monthly_deductions'];
$total_deductions_in_current_year= $data1['total_deductions_in_current_year'];
$total_tax_paid_in_current_year= $data1['total_tax_paid_in_current_year'];
$total_perks_in_current_year= $data1['total_perks_in_current_year'];

//Chart quris




$total_tax_due_in_current_year = $cal_intax - $total_tax_paid_in_current_year;
$tax_summary_percent = 0;
if($total_tax_paid_in_current_year != 0 && $cal_intax != 0){
  $tax_summary_percent = ($total_tax_paid_in_current_year / $cal_intax) * 100;
}

if($_SESSION['config']['default_document_payslip_view']==0){
  $whrcon["where"] = array('company_id'=> $_SESSION['company_id'],'emp_id'=>$_SESSION['emp_id'],'is_active'=>0);
  $vdata = urlencode(json_encode($whrcon));
  $url = API."?mode=gettabledetailswithconditions&table=emp_assign_release_documents&data=".$vdata;
  $respay = callAPI($url);
  $paysiilps = json_decode($respay,1);
  $saypayslip = array();
  foreach($paysiilps as $paysiilp){
    $saypayslip[$paysiilp['assessment_year']][$paysiilp['document_month']] =  'Y';
  }
}else{
  $check_payslip = 1;
}
?>
<script>

function expensesDetailsPopUp(emp_id,exp_id="",sub_id=""){
        var table_name = 'project_timesheets';
      jQuery.ajax({
       url: "views/custom_insert_emp_expense_details_popup.php",
       data:'table='+table_name+'&exp_id='+exp_id+'&sub_id='+sub_id+'&emp_id='+emp_id,
       type: "POST",
       success:function(data){
        $('#dynamic_popup11').html(data);
        $('#kt_modal_dynamic_popup2').modal("show");
      },
      error:function (){}
    });
  }
</script>

<div class="">
  <!--begin::Container-->
  <div class="">
    <!--begin::Card-->
    <div class="card-custom gutter-b">

      <!--begin::Top-->
      <div class="card-custom card-sticky" id="kt_page_sticky_card">

        <div class="card-header">
          <h5>My Pay</h5>
          <span>lorem ipsum dolor sit amet, consectetur adipisicing elit</span>
          <div class="card-header-right cutm-header-mob">


                  <a href="index.php?option=COM_VIEW_BILLSTACK_EMPLOYEE_DASHBOARD" class="btn  btn btn-primary waves-effect waves-light text-uppercase mb-2" >Reimbursement</a>

                  <a href="index.php?option=COM_INSERT_SALARY&SEid=<?php echo deocdeId($nid); ?>" target="_blank" class="btn btn-primary waves-effect waves-light text-uppercase mb-2">Salary Structure</a>
                  <a href="index.php?option=COM_INSERT_IT_PLANS" target="_blank" class="btn btn-primary waves-effect waves-light text-uppercase mb-2">Tax Planning</a>

                  <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                    <i class="icofont icofont-reply"></i> Back</a>
          </div>
        </div>



    <div class="card-body">
        <div class="d-flex align-items-center flex-wrap mt-2 mgnt row my-pay-page">
          <!--begin: Item-->
          <div class="align-items-center col-lg-2 col-md-4 col-sm-4 col-6 text-center">
            <div class="card prod-p-card card-red m-b-20">
              <div class="card-body">
                <span class="mr-4">
                  <i class="flaticon-piggy-bank icon-2x text-white font-weight-bold "></i>
                </span>
                <div class="d-flex flex-column text-white">
                  <span class="font-weight-bolder font-size-sm">Total Earnings</span>
                  <span class=" font-size-h5">
                    <span class="text-white font-weight-bold"></span><?php echo number_format($total_earnings_in_current_year,2); ?> </span>
                </div>
              </div>
            </div>
          </div>
          <!--end: Item-->
          <!--begin: Item-->
          <div class="align-items-center col-lg-2 col-md-4 col-sm-4 col-6 text-center">
            <div class="card card-blue m-b-20">
              <div class="card-body">
                <span class="mr-4">
                  <i class="flaticon-confetti icon-2x text-white font-weight-bold"></i>
                </span>
                <div class="d-flex flex-column text-white">
                  <span class="font-weight-bolder font-size-sm">Total Deductions</span>
                  <span class=" font-size-h5">
                    <span class="text-white font-weight-bold"></span><?php echo number_format($total_deductions_in_current_year,2); ?> </span>
                </div>
              </div>
            </div>
          </div>
          <!--end: Item-->
          <!--begin: Item-->
          <div class="align-items-center col-lg-2 col-md-4 col-sm-4 col-6 text-center">
            <div class="card card-green m-b-20">
              <div class="card-body">
                <span class="mr-4">
                  <i class="flaticon-pie-chart icon-2x text-white font-weight-bold"></i>
                </span>
                <div class="d-flex flex-column text-white">
                  <span class="font-weight-bolder font-size-sm">Perks</span>
                  <span class=" font-size-h5">
                    <span class="text-white font-weight-bold"></span><?php echo number_format($total_perks_in_current_year,2); ?> </span>
                </div>
              </div>
            </div>
          </div>
          <!--end: Item-->
          <div class="align-items-center col-lg-2 col-md-4 col-sm-4 col-6 text-center">
            <div class="card card-yellow m-b-20">
              <div class="card-body">
                <span class="mr-4">
                  <i class="flaticon-piggy-bank icon-2x text-white font-weight-bold"></i>
                </span>
                <div class="d-flex flex-column text-white">
                  <span class="font-weight-bolder font-size-sm">Total Annual Tax</span>
                  <span class=" font-size-h5">
                    <span class="text-white font-weight-bold"></span><?php echo number_format($cal_intax,2); ?> </span>
                </div>
              </div>
            </div>
          </div>
          <!--end: Item-->
          <!--begin: Item-->
          <div class="align-items-center col-lg-2 col-md-4 col-sm-4 col-6 text-center">
            <div class="card card-red m-b-20">
              <div class="card-body">
                <span>
                  <i class="flaticon-confetti icon-2x text-white font-weight-bold"></i>
                </span>
                <div class="d-flex flex-column text-white">
                  <span class="font-weight-bolder font-size-sm">Tax Paid</span>
                  <span class=" font-size-h5">
                  <span class="text-white font-weight-bold"></span><?php echo number_format($total_tax_paid_in_current_year,2); ?> </span>
                </div>
              </div>
            </div>
          </div>
          <!--end: Item-->
          <!--begin: Item-->
          <div class="align-items-center col-lg-2 col-md-4 col-sm-4 col-6 text-center">
            <div class="card card-blue m-b-20">
              <div class="card-body">
                <span class="mr-4">
                  <i class="flaticon-pie-chart icon-2x text-white font-weight-bold"></i>
                </span>
                <div class="d-flex flex-column text-white">
                  <span class="font-weight-bolder font-size-sm">Tax Due</span>
                  <span class=" font-size-h5">
                  <span class="text-white font-weight-bold"></span><?php echo number_format($total_tax_due_in_current_year,2); ?> </span>
                </div>
              </div>
            </div>
          </div>



                    <!--end: Item-->

                        <!--end: Item-->
                      </div>
                      <!--end::Bottom-->
                    </div>
                  </div>

                </div>
              </div>

              <div class="card-body">
                 <!--begin::Container-->
                 <div class="">
                   <!--begin::Row-->
                   <div class="row">





                     <!--begin::Column-->
                     <?php
                     for ($row = 0; $row < count((array)$payslip_array); $row++) {
                       if( isset($saypayslip['assessment_year'][$payslip_array[$row][0]]) || $check_payslip=='1'){


                       $chart_month = date('F', mktime(0,0,0,$payslip_array[$row][0], 1, date('Y')));
                       $paid_month_year = $chart_month. " - ".$payslip_array[$row]['4'];
                       $payslip_month_number = $payslip_array[$row][0];
                       $month_array = array($payslip_array[$row][0]);
                       $chart_months = implode(',',(array)$month_array);
                       $take_home = $payslip_array[$row][1];
                       $deductions= $payslip_array[$row][2];
                       $gross = $payslip_array[$row][3];
                       $chart_data = array("GROSS PAY:".round($take_home),"DEDUCTION:".round($deductions),"NET PAY:".round($gross));

                       $chart_fill_data = implode('||',(array)$chart_data);
                       $chart_id = "chart_payslip_m".$payslip_month_number;
                       $chart_data= "chart_data_m".$payslip_month_number;
                       $chart_month = "chart_months".$payslip_month_number;
                       //BASIC:27||HRA:11||SPECIAL ALLOWANCE:48||FOOD COUPON:3||GIFT COUPON:1||xyz:0||xyz:0
                       //14516.13,6200,8316.13
                      // Gross Pay:14516||Deduction:6200||Net Pay:8316
                        $disvsze = (isset($_GET['option']) && $_GET['option'] == 'COM_INSERT_PAYSLIP') ? "4" : "6";
?>
                       <div class="col-xl-<?php echo $disvsze; ?> col-lg-6 col-md-6 col-sm-12 col-12">
                        <!--begin::Card-->


                        <div class="card card-custom gutter-b card-stretch">
                          <!--begin::Body-->
                          <div class="card-body text-center pt-4">


                          <div id="chart_Donut<?php echo $row; ?>" style="width: 100%; height: 200px;"></div>

                            <div class="mt-7"><?php //echo $chart_id; ?>

                            <div id="<?php echo $chart_id; ?>" class="d-flex justify-content-center"></div>
                            <input type="hidden" id="<?php echo $chart_data; ?>" class="chertvalues" data-id="chart_Donut<?php echo $row; ?>" value="<?php echo $chart_fill_data?>">
                            <input type="hidden" id="<?php echo $chart_month; ?>" value="<?php echo $chart_months;?>">
                          </div>
                          <!--end::User-->
                          <!--begin::Name-->
                          <div class="my-4">
                            <a href="#" class="text-dark font-weight-bold text-hover-primary font-size-h4"><?php echo $paid_month_year; ?> </a>
                          </div>
                          <!--end::Name-->
                          <!--begin::Label-->

                          <button type="button" class="btn btn-danger mr-2 mb-2" data-container="body" data-offset="20px 20px" data-toggle="popover" data-placement="top" data-content="Gross Pay">
                            <?php echo number_format($take_home,2); ?>
                          </button>
                          <button type="button" class="btn btn-warning mb-2" data-container="body" data-offset="60px 0px" data-toggle="popover" data-placement="top" data-content="Deductions">
                           <?php echo number_format($deductions,2); ?>
                         </button>
                         <button type="button" class="btn btn-primary mr-2 mb-2" data-container="body" data-offset="-20px -20px" data-toggle="popover" data-placement="top" data-content="Net Pay">
                           <?php echo number_format($gross,2); ?>
                         </button>

                         <!--end::Label-->

                         <div class="mt-9">
                          <a href="index.php?option=COM_LIST_PAYSLIP_TEMPLATE3&employe_id=<?php echo $employee_id; ?>&month=<?php echo $payslip_array[$row]['0']; ?>&year=<?php echo $payslip_array[$row]['4']; ?>" target="_blank" class="btn btn-primary waves-effect waves-light mt-2 text-uppercase">view payslip</a>
                        </div>

                      </div>
                      <!--end::Body-->
                    </div>
                    <!--end::Card-->
                  </div>
                  <?php
                }
                }
                ?>
                <!--end::Column-->
              </div>
            </div>
          </div>
        </div>

<?php if(isset($_POST['emp_id']) && $_POST['emp_id']!="" || $_POST['ajaxpage'] == 'Y'){ ?>
    <script type="text/javascript" src="assets/pages/chart/google/js/com_insert_payslip_charts.js"></script>
<?php }  ?>
