<?php
$bgcolorlist = array('0'=>'success','1'=>'warning','2'=>'primary','3'=>'info','4'=>'dark','5'=>'secondary');
$stausresult = ViewStatusData();
$stausprocessresult = gettableStatusArray('project_timesheets');
$stausprocessresult = $stausprocessresult['full'];
if(isset($_POST['datefilter'])){
    $dateexplo = explode('-', $_POST['fromdate']);
    header("Location: index.php?option=COM_VIEW_EMP_TIMESHEET_DASHBOARD&PAY_YEAR=".$dateexplo[0]."&PAY_MONTH=".$dateexplo[1]);
}else{

    if($_GET['PAY_MONTH']!=""){
        $current_from_date = $_GET['PAY_YEAR']."-".$_GET['PAY_MONTH']."-01";
        $current_to_date = date("Y-m-t", strtotime($current_from_date));
    }else{
        $current_from_date = date('Y-m-01');
        $current_to_date = date('Y-m-d');
    }

    $startingdate = strtotime($current_from_date);
    $endingdate = strtotime('last day of this month', strtotime($current_from_date));

    $empp_id = $_GET['EMP']!="" ? $_GET['EMP'] : $_SESSION['emp_id'];
    $summary_data = array('Yearly','Quaterly','Monthly','Hours');

     $vurl = API."?mode=gettimesheetsummarydashboard_new&admin_type=".$_SESSION['admin_type']."&user_type=".$_SESSION['user_type']."&user_email=".$_SESSION['user_email']."&company_id=".$_SESSION['company_id']."&is_active=0&emp_id=".$empp_id."&fromdate=".date('Y-m-d',$startingdate)."&todate=".date('Y-m-d',$endingdate);
    $configesponse = callAPI($vurl);
    $requestData = json_decode($configesponse,1);
    $total_requests = $requestData['summary'];
    $summarydetails = $requestData['summarydetails'];


}
$emp_data = $requestData['emp_data'];
$currentyear = $_GET['PAY_YEAR']!=""?$_GET['PAY_YEAR']:date('Y');
$currentmonth = $_GET['PAY_MONTH']!=""?$_GET['PAY_MONTH']:date('m');
$head_data = $requestData['head_cc_data'];
$total_count = $requestData['total_head'];
?>
<script type="text/javascript">
    function getEmployeeDetails(pay_year,pay_month,emp_id){
        jQuery.ajax({
            url: "views/custom_view_emp_timesheets.php",
            data: "PAY_YEAR="+pay_year+"&PAY_MONTH="+pay_month+"&EMP="+emp_id+"&popup=Y",
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
            }
        });
    }

</script>

<div class="row">
    <div class="col-lg-7">
        <div class="row">
            <div class="col-lg-5" style="">
                <div class="card">
                        <div class="card-body d-flex flex-center flex-column pt-12 p-2">
                                <div class="symbol symbol-65px symbol-circle mb-2">


                                    <div class="m-b-10 mt-3" style="text-align: -webkit-center;">

                                        <div class="circle_gradiant" style="--p1: 0deg; --p2_color: #ff5370;--p1_color: #2ed8b6;">
                                            <?php if($emp_data['image']!=""){ ?>
                                               <img src="uploads/all<?php echo $emp_data['image']; ?>" class="percentag-img-radius img-90" alt="Details" style="border-radius: 50%;border: 8px solid #ffffff;height: 90px;">
                                               <?php }else{ ?>
                                                <span class="percentag-img-radius img-90 f-w-600 custom_name f-24" alt="User">
                                                    <?php echo initials($emp_data['display_name']); ?>
                                                </span>
                                                <?php } ?>
                                          </div>
                                     </div>
                                     <div class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n3 mt-n3">

                                        </div>
                                </div>
                            <span class="fs-6 text-primary text-hover-primary fw-bold mb-0" style="font-size: 16px !important;"><?php echo ucwords($emp_data['display_name']) ?></span>
                                <div class="fw-semibold text-muted mb-6">
                                    Employee Code : <?php if($emp_data['emp_code']!=""){    echo ucwords($emp_data['emp_code']);
}else{ echo "-";
}  ?>
                                </div>
                                <div class="fw-semibold text-muted mb-6 f-12">
                                    <?php echo ucwords($emp_data['email_id']) ?>
                                </div>

                                <div class="d-flex flex-center flex-wrap mt-3">
                                    <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                        <div class="fs-6 fw-bold text-gray-700"
                                        ><?php
                                            echo $requestData['hr_dis_data']['previous_month'] !=0 ?round($requestData['hr_dis_data']['previous_month']/8)." Days" : "-";
                                         ?>
                                        </div>
                                        <div class="fw-semibold text-gray-500">Previous Month</div>
                                    </div>
                                    <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                        <div class="fs-6 fw-bold text-gray-700">
                                            <?php
                                                echo $requestData['hr_dis_data']['current_month'] !=0 ?round($requestData['hr_dis_data']['current_month']/8)." Days" : "-";
                                            ?>
                                        </div>
                                        <div class="fw-semibold text-gray-500">This Month</div>
                                    </div>
                                </div>
                             </div>
                      </div>
            </div>
            <div class="col-lg-7" style="">
                <input type="hidden" id="thismonthnkey" value="">
                <div class="card ticket-card mb-3">
                    <div class="card-body">
                        <p class="m-b-10 bg-c-blue lbl-card"><i class="fa fa-group m-r-10"></i>Total Head Count </p>
                        <div class="text-center">
                            <h2 class="m-b-0 d-inline-block text-c-blue"><?php echo $total_count; ?></h2>
                            <div class="d-flex flex-center flex-wrap mt-3">
                                <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-3 mx-2 mb-3 text-start" style="width:130px;">
                                    <span class="fs-6 fw-bold text-gray-700"><?php echo $head_data['0']!="" ? $head_data['0'] : "-" ; ?></span>
                                    <div class="fw-semibold text-gray-500">Billable</div>
                                </div>
                                <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-3 mx-2 mb-3 text-start" style="width:130px;">
                                    <span class="fs-6 fw-bold text-gray-700"><?php echo $head_data['2']!="" ? $head_data['2'] : "-" ; ?> </span>
                                    <div class="fw-semibold text-gray-500">Non-Billable</div>
                                </div>
                                <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-3 mx-2 mb-3 text-start" style="width:130px; min-height: 78px;">
                                    <span class="fs-6 fw-bold text-gray-700"><?php echo $head_data['1']!="" ? $head_data['1'] : "-" ; ?> </span>
                                    <div class="fw-semibold text-gray-500">Milestone</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card prod-p-card card-red mb-0">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h7 class="m-b-5 text-white">Last Month Vendors</h7>
                                        <h3 class="m-b-0 f-w-700 text-white f-32 mt-3">3</h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user text-c-red f-18"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card prod-p-card card-green mb-0">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h7 class="m-b-5 text-white">This Month Vendors</h7>
                                        <h3 class="m-b-0 f-w-700 text-white f-32 mt-3">3</h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user text-c-green f-18"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mt-4">
                <div class="card prod-p-card card-success">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-b-5 text-white f-14">Approved Days-Year</h6>
                                <h3 class="m-b-0 f-w-700 text-white f-24 mt-3">
                                    <?php echo $requestData['year_details']['app_hr'] !="" ? round($requestData['year_details']['app_hr']/8): "-"; ?>

                                </h3>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-clock-o text-c-green f-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mt-4">
                <div class="card prod-p-card card-warning">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-b-5 text-white f-14">Un Approved Days-Year</h6>
                                <h3 class="m-b-0 f-w-700 text-white mt-3 f-24"><?php echo $requestData['year_details']['unapp_hr'] !="" ? round($requestData['year_details']['unapp_hr']/8): "-"; ?> </h3>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-clock-o text-c-yellow f-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mt-4">
                <div class="card prod-p-card card-red">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-b-5 text-white f-14">Yet to Submit-Year</h6>
                                <h3 class="m-b-0 f-w-700 text-white mt-3 f-24">
                                    <?php echo $requestData['year_details']['billable_hrs']/8; ?>
                                </h3>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-clock-o text-c-red f-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 ">
                <div class="card prod-p-card card-success">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-b-5 text-white f-14">Approved Days-Month</h6>
                                <h3 class="m-b-0 f-w-700 text-white f-24 mt-3"><?php echo $requestData['month_details']['app_hr'] !="" ? round($requestData['month_details']['app_hr']/8): "-"; ?> </h3>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-clock-o text-c-green f-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card prod-p-card card-warning">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-b-5 text-white f-14">Un Approved Days-Month</h6>
                                <h3 class="m-b-0 f-w-700 text-white mt-3 f-24"><?php echo $requestData['month_details']['unapp_hr'] !="" ? round($requestData['month_details']['unapp_hr']/8): "-"; ?>  </h3>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-clock-o text-c-yellow f-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card prod-p-card card-red">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-b-5 text-white f-14">Yet to Submit-Month</h6>
                                <h3 class="m-b-0 f-w-700 text-white mt-3 f-24"><?php echo ($requestData['total_head']*21)-($requestData['month_details']['billable_hrs']/8); ?>  </h3>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-clock-o text-c-red f-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5" style="" id="replaceiddata">
        <?php include_once 'vendorstaq_manage_dashboarad_steps.php'; ?>
    </div>

    <div class="col-lg-6" id="listapprovalpeding">
        <?php include_once 'vendorstaq_manage_dashboarad_project_aproval_pending.php'; ?>
    </div>
    <div class="col-lg-6" id="listprojecttimesheetdata">
        <?php include_once 'vendorstaq_manage_dashboarad_projecttimesheet_data.php'; ?>

    </div>
    <div class="col-lg-6" id="listleave">
        <?php include_once 'vendorstaq_manage_dashboarad_leaves.php'; ?>
    </div>
    <div class="col-lg-6" id="listtermination">
        <?php include_once 'vendorstaq_manage_dashboarad_terminations.php'; ?>
    </div>
</div>

