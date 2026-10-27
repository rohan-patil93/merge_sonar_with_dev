<?php
$_SESSION['apply_js']['vendorstaq_admin_dashboarad_new']=1;
$startyearmonth = date('Y-m-d', strtotime('-1 year'));
$sixmonthmonth = date('Ym', strtotime('-6 month'));
$priviousmonth = date('Ym', strtotime('-1 month'));
$thismonthyear = date('Ym');
$priviousmonthdisplay = date('Y M', strtotime('-1 month'));
$thismonthyeardisplay = date('Y M');
$endyearmonth = date('Y-m-d');
$vdata1["where"] = array('is_active'=> 0);
$vdata = urlencode(json_encode($vdata1));

$vurl = API."?mode=gettabledetailswithconditions&table=locations&data=".$vdata;
$configesponse = callAPI($vurl);
$datalocations = json_decode($configesponse,1);
foreach($datalocations as $dalocations){
    $locations[$dalocations['id']] = $dalocations;
}
$loc  = $_GET['LOC']!=""?$_GET['LOC']:0;
$admin_type  = $_SESSION['admin_type'];
$displaylocations  = array();
if($_SESSION['admin_type'] ==1){ $displaylocations = $locations;
}
if(in_array(8,$_SESSION['role_ids'])){ $admin_type = '1';
}
$employee_id = $_SESSION['emp_id'];
$application_user_id = $_SESSION['userid'];
if($admin_type == '1'){
    $vdata1["where"] = array('is_active'=> 0, 'user_id'=>$_SESSION['userid']);
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=user_report_access_locations&data=".$vdata;
    $configesponse = callAPI($vurl);
    $locaccess = json_decode($configesponse,1);
    foreach($locaccess  as $access){
        $displaylocations[$access['location_id']] = $locations[$access['location_id']];
        $loca_ids[] = $access['location_id'];
    }
}
$locstr = "";
if($_SESSION['admin_type'] !=1 || (isset($_GET['LOC']) && $_GET['LOC']!="")){
     if(isset($_GET['LOC']) && $_GET['LOC']!="" && $_GET['LOC']!="0"){
        $locstr="&location_id=1&location_id_value=".$_GET['LOC'];
     }else{
        $locstr="&location_id=0&location_id_value=".implode(",",(array)$loca_ids);
     }
}
$url1 = API."?mode=admindashboarddetails_newdashboard&admin_type=".$admin_type."&user_type=".$_SESSION['user_type']."&user_email=".$_SESSION['user_email']."&employee_id=".$employee_id."&emp_id=".$employee_id."&application_user_id=".$application_user_id."&check=1".$locstr;
$data_details1 = callAPI($url1);
$emp_count_dashboard = json_decode($data_details1,1);

$url = API."?mode=vendorstaqadmindashboard&admin_type=".$admin_type."&user_type=".$_SESSION['user_type']."&user_email=".$_SESSION['user_email']."&employee_id=".$employee_id."&application_user_id=".$application_user_id."&start_date=".$startyearmonth."&end_date=".$endyearmonth;
$data_details1 = callAPI($url);
$new_dashboard = json_decode($data_details1,1);
$budgetthisyear = 0;
$utilizationthisyear = 0;
$utilizationsixmonth = 0;
$utilizationsmonth = 0;
$type_billable = $emp_count_dashboard['type_billable'];
$thismonthvendoremployecount = array();
foreach((array)$new_dashboard['mainemp'] as $mkey=>$mca){
    $po_total = 0; $actual_total =0;
    $budget = 0;
    $bilable= 0;
    $maingra['months'][]=date('Y-m',strtotime($mkey));
    $yearmonth = date('Ym',strtotime($mkey));
    foreach((array)$mca as $madmin){
        $budget +=$madmin['po_total'];
        $bilable +=$madmin['actual_total'];
        if($yearmonth==$thismonthyear && $madmin['vendor_name']!=""){
            $thismonthvendoremployecount['emp_count'][$madmin['vendor_id']] = $thismonthvendoremployecount['emp_count'][$madmin['vendor_id']]+1;
            $thismonthvendoremployecount['month_total']++;
            $thismonthvendoremployecount['usage_vendor'][$madmin['vendor_id']] += round(($madmin['actual_total']/100)/10);
            $thismonthvendoremployecount['usage_month_total'] += round($madmin['actual_total']);

            $thismonthcostcenter['emp_count'][$madmin['cost_center_id']] = $thismonthcostcenter['emp_count'][$madmin['cost_center_id']]+1;
            $thismonthcostcenter['month_total']++;
            $thismonthcostcenter['usage_vendor'][$madmin['cost_center_id']] += round(($madmin['actual_total']/100)/10);
            $thismonthcostcenter['usage_month_total'] += round($madmin['actual_total']);

        }
        if($yearmonth==$priviousmonth && $madmin['vendor_name']!=""){
            $priviousmonthvendoremployecount['emp_count'][$madmin['vendor_id']] = $priviousmonthvendoremployecount['emp_count'][$madmin['vendor_id']]+1;
            $priviousmonthvendoremployecount['month_total']++;
            $priviousmonthvendoremployecount['usage_vendor'][$madmin['vendor_id']] += round(($madmin['actual_total']/100)/10);
            $priviousmonthvendoremployecount['usage_month_total'] += round($madmin['actual_total']);

            $priviousmonthcostcenter['emp_count'][$madmin['cost_center_id']] = $priviousmonthcostcenter['emp_count'][$madmin['cost_center_id']]+1;
            $priviousmonthcostcenter['month_total']++;
            $priviousmonthcostcenter['usage_vendor'][$madmin['cost_center_id']] += round(($madmin['actual_total']/100)/10);
            $priviousmonthcostcenter['usage_month_total'] += round($madmin['actual_total']);
        }
    }
    $maingra['bdget'][]=round($budget);
    $maingra['billable'][]=round($bilable);
    $getyearsplite  = explode("-",$mkey);
    if($sixmonthmonth <= $yearmonth){    //sixmonth calculation
        $utilizationsixmonth += $bilable;
    }
    if($getyearsplite[0]==date('Y')){   //Yearly calculation
        $budgetthisyear += $budget;
        $utilizationthisyear += $bilable;
    }
    if($yearmonth==$thismonthyear){  //this month calculation
        $utilizationsmonth += $bilable;
    }
}
$monthdata = implode('|', (array)$maingra['months']);
$actualdata = implode('|', (array)$maingra['billable']);
$estimationdata = implode('|', (array)$maingra['bdget']);
$emp_data = $emp_count_dashboard['emp_data'];

$venurl = API."?mode=vendorlistforadminmanag&admin_type=".$admin_type."&user_type=".$_SESSION['user_type']."&user_email=".$_SESSION['user_email']."&employee_id=".$employee_id."&emp_id=".$employee_id."&application_user_id=".$application_user_id."&check=1".$locstr;
$eerw = callAPI($venurl);
$vendoralkadetails = json_decode($eerw,1);
function numberToLakh($num){
    return number_format($num / 100000, 1)." L";
}
?>
<script>
    function changeLocation(){
        var locationvalueget = $("#locationvalueget").val();
        window.location.href = "index.php?LOC="+locationvalueget;
    }
</script>
<div class="p-0">
    <div class="col-lg-9" ></div>
    <?php if(count((array)$displaylocations)>0){ ?>
    <div class="col-lg-3 m-b-30 text-end text-custom-left">
        <select aria-label="Locationvalueget" class="form-select js-example-basic-single" id="locationvalueget" onchange="changeLocation()">
            <option <?php if($loc==0){ echo "SELECTED";
}  ?>  value="0">ALL</option>
                <?php foreach((array)$displaylocations as $locat){ ?>

            <option <?php if($loc==$locat['id']){ echo "SELECTED";
}  ?> value="<?php echo $locat['id']; ?>"> <?php echo $locat['location_name']; ?> </option>
            <?php } ?>
        </select>
    </div>
    <?php } ?>

    <div class="row">
        <div class="col-lg-7" style="">
            <div class="row" style="">
                <!----------------  Started ---->
                <div class="col-lg-5" style="">
                    <div class="card mb-2">
                        <div class="card-body p-4 text-center">
                                <div class="symbol symbol-65px symbol-circle mb-2 d-block">



                                    <div class="m-b-10 mt-0" style="text-align: -webkit-center;">

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
                                <div class="fw-semibold text-muted mb-6 f-10">
                                    <?php echo ucwords($emp_data['email_id']) ?>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-lg-6 p-1">
                                            <div class="border border-gray-300 border-dashed rounded w-100 py-3 px-3">
                                                <div class="fs-7 fw-bold text-gray-700">
                                                    <?php
                                                    echo $emp_count_dashboard['hr_dis_data']['previous_month'] !=0 ?round($emp_count_dashboard['hr_dis_data']['previous_month']/8)." Days" : "-";
                                                     ?>
                                                </div>
                                                <div class="fw-semibold text-gray-500 mt-1">Last Month</div>
                                            </div>
                                    </div>
                                    <div class="col-lg-6 p-1">
                                            <div class="border border-gray-300 border-dashed rounded w-100 py-3 px-3">
                                                <div class="fs-7 fw-bold text-gray-700">
                                                <?php
                                                    echo $emp_count_dashboard['hr_dis_data']['current_month'] !=0 ?round($emp_count_dashboard['hr_dis_data']['current_month']/8)." Days" : "-";
                                                ?>
                                                </div>
                                                <div class="fw-semibold text-gray-500 mt-1">This Month</div>
                                        </div>
                                       </div>
                                </div>
                        </div>
                      </div>
                      <div class="mt-1 mb-1">
                        <div class="card-body p-2">
                            <div class="row">
                                <div class="col-lg-12 p-1">
                                          <div class="border border-gray-300 border-dashed rounded w-100 py-3 px-3 bg-white">
                                          <span class="fs-7 fw-bold text-gray-700"><?php if($emp_data['reporting_manager']!=""){ echo ucwords($emp_data['reporting_manager']);
}else{ echo "-";
}  ?> </span>
                                              <div class="fw-semibold text-gray-500">Reports to</div>
                                          </div>
                                  </div>
                             </div>
                         </div>
                    </div>
                </div>
                <!----------------  End ---->
                <!----------------  Started ---->
                <div class="col-lg-7" style="">
                    <div class="card mb-3">
                        <div class="card-body p-2">
                            <div class="border border-gray-300 border-dashed rounded w-100 py-3 px-3">
                                  <span class="fs-7 fw-bold text-gray-700"><?php echo $type_billable['0']; ?> </span>
                                      <div class="fw-semibold text-gray-500">Billable</div>
                                  </div>
                        </div>
                    </div>
                    <input type="hidden" id="thismonthnkey" value="<?php echo $thismonthkey; ?>">
                    <div class="card ticket-card mb-3">
                        <div class="card-body">
                            <p class="m-b-10 bg-c-blue lbl-card"><i class="fa fa-group m-r-10"></i>Total Head Count </p>
                            <div class="text-center">
                                <h2 class="m-b-0 d-inline-block text-c-blue"><?php echo customNanAndEmptyValidation($emp_count_dashboard['total_emp']); ?></h2>
                                <div class="row mt-3">
                                    <div class="col-lg-4 p-1">
                                              <div class="border border-gray-300 border-dashed rounded w-100 py-3 px-3">
                                              <span class="fs-7 fw-bold text-gray-700"><?php echo $type_billable['0']; ?> </span>
                                                  <div class="fw-semibold text-gray-500">Billable</div>
                                              </div>
                                      </div>
                                      <div class="col-lg-4 p-1">
                                              <div class="border border-gray-300 border-dashed rounded w-100 py-3 px-3" style="">
                                              <span class="fs-7 fw-bold text-gray-700"><?php echo $type_billable['1']!="" ? $type_billable['1'] : 0; ?> </span>
                                              <div class="fw-semibold text-gray-500">Non-Billable</div>
                                              </div>
                                      </div>
                                      <div class="col-lg-4 p-1">
                                              <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-3" style="">
                                              <span class="fs-7 fw-bold text-gray-700"><?php echo $type_billable['2']!="" ? $type_billable['2']:0; ?> </span>
                                              <div class="fw-semibold text-gray-500">Milestone</div>
                                              </div>
                                      </div>
                                  </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card prod-p-card card-red mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col p-1">
                                            <h6 class="m-b-5 text-white f-14">Last Month Vendors</h6>
                                            <h3 class="m-b-0 f-w-700 text-white f-22 mt-3"><?php echo customNanAndEmptyValidation($emp_count_dashboard['total_ven']); ?></h3>
                                        </div>
                                        <div class="col-auto p-1">
                                            <i class="fas fa-user text-c-red f-14" style="width:30px;height:30px;padding:9px 0;font-size:12px;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card prod-p-card card-green mb-0">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col p-1">
                                            <h6 class="m-b-5 text-white f-14">This Month Vendors</h6>
                                            <h3 class="m-b-0 f-w-700 text-white f-22 mt-3"><?php echo customNanAndEmptyValidation($emp_count_dashboard['total_ven']); ?></h3>
                                        </div>
                                        <div class="col-auto p-1">
                                            <i class="fas fa-user text-c-green f-18" style="width:30px;height:30px;padding:9px 0;font-size:12px;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!----------------  end ---->

                <div class="col-lg-4">
                    <div class="card prod-p-card card-primary">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h7 class="m-b-5 text-white">Year to Month</h7>
                                    <h3 class="m-b-0 f-w-700 text-white f-22 mt-3">
                                        <?php echo $emp_count_dashboard['hr_dis_data']['previous_month_bill'] !=0 ?round($emp_count_dashboard['hr_dis_data']['previous_month_bill']/100000,2)."L" : "-"; ?>
                                             </h3>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-alt text-c-blue f-18" style="width:30px;height:30px;padding:9px 0;font-size:12px;"></i>
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
                                    <h7 class="m-b-5 text-white">Last Month Billing</h7>
                                    <h3 class="m-b-0 f-w-700 text-white mt-3 f-22"><?php
                                            echo $emp_count_dashboard['hr_dis_data']['current_month_bill'] !=0 ?round($emp_count_dashboard['hr_dis_data']['current_month_bill']/100000,2)."L" : "-";
                                        ?> </h3>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-alt text-c-red f-18" style="width:30px;height:30px;padding:9px 0;font-size:12px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card prod-p-card card-green">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h7 class="m-b-5 text-white">This Month Billing</h7>
                                    <h3 class="m-b-0 f-w-700 text-white mt-3 f-22"><?php
                                            echo $emp_count_dashboard['hr_dis_data']['current_month_bill'] !=0 ?round($emp_count_dashboard['hr_dis_data']['current_month_bill']/100000,2)."L" : "-";
                                        ?> </h3>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-alt text-c-green f-18" style="width:30px;height:30px;padding:9px 0;font-size:12px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5" id="replaceiddata">
            <?php include_once 'vendorstaq_admin_dashboarad_steps.php'; ?>
        </div>
    </div>


    <?php include_once 'vendor_employee_dash_board.php' ?>
    <div class="row">
        <div class="col-md-12 col-xl-8">
            <div class="card sale-card">

                <input type="hidden" id="monthdata" value="2025-05|2025-06|2025-07|2025-08|2025-09|2025-10|2025-11">
                <input type="hidden" id="estimationdata" value="2486420|2839351|2020241|668362|750437|691841|617500">
                <input type="hidden" id="actualdata" value="2486420|2839351|2020241|668362|750437|691841|617500">

                <div class="card-header">
                    <h5>Overall Budget Utilization</h5>

                    <div class="card-header-right">
                        <span class="badge badge-inverse-primary bg-white"> <i class="fa fa-calendar-check-o text-primary"></i>  Financial Year <?php echo date('Y-M',strtotime($startyearmonth))." To ".date('Y-M',strtotime($endyearmonth)); ?></span>
                    </div>
                </div>
                <?php $countvaluempty = $estimationdata==""?1:0;   ?>
                <div class="card-block">
                    <?php if($countvaluempty != "1"){
                        echo "<h4 class='p-40 text-center'><i class='fa fa-warning'></i> Budget Utilization Data Not Available</h4>";
                    }else{ ?>
                        <div id="deal-analytic-chart-new" class="chart-shadow" style="height:300px"></div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xl-4">
            <div class="card sale-card">
                <div class="card-header">
                    <h5>Total Utilization</h5>
                </div>
                <input type="hidden" id="hid_budget" value="2900000">
                <input type="hidden" id="hid_utilization" value="2600000">
                <?php
                        $utilizationthisyear = 10444000;
                        $utilizationsixmonth = 6483000;
                        $utilizationsmonth = 1861000;
                        $budgetthisyear = 25932000;
                     ?>
                <div class="card-block text-center">
                    <div class="m-b-20" style="text-align: -webkit-center;">
                      <div class="circle_gradiant mb-2" style="--p1: 164.24deg; --p1_color: #dc3545;--p2_color: #2ed8b6;">
                  <span class="text-dark f-16 f-w-600 d-block">44.4% </span>
            </div>
          </div>

                    <h6 class="m-t-40">This Year's Budget</h6>
                    <h3 class="f-w-700 m-b-40"><?php echo numberToLakh(convert_Rupee_Nomber(customNanAndEmptyValidation(round($budgetthisyear)))); ?></h3>

                    <div class="row">
                        <div class="col-4">
                            <p class="m-b-5">Year</p>
                            <h3 class="f-w-700 text-c-yellow f-14"><?php echo   numberToLakh(convert_Rupee_Nomber(customNanAndEmptyValidation(round($utilizationthisyear))) ); ?></h3>
                        </div>
                        <div class="col-4">
                            <p class="m-b-5">Quater</p>
                            <h3 class="f-w-700 text-c-yellow f-14"><?php echo numberToLakh(convert_Rupee_Nomber(customNanAndEmptyValidation(round($utilizationsixmonth))) ); ?></h3>
                        </div>
                        <div class="col-4">
                            <p class="m-b-5">Month</p>
                            <h3 class="f-w-700 text-c-yellow f-14"><?php echo numberToLakh(convert_Rupee_Nomber(customNanAndEmptyValidation(round($utilizationsmonth))) ); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="row">

    <div class="col-lg-6" id="listtermination">
        <?php include_once 'vendorstaq_manage_dashboarad_terminations.php'; ?>
    </div>
    <div class="col-lg-6" id="listleave">
        <?php include_once 'vendorstaq_manage_dashboarad_leaves.php'; ?>
    </div>
    <div class="col-lg-6" id="listvendorsview">
        <?php include_once 'vendorstaq_manage_dashboarad_vendors.php'; ?>
    </div>
    <div class="col-lg-6" id="listinactivevendorsview">
        <?php include_once 'vendorstaq_manage_dashboarad_inactivevendors.php'; ?>
    </div>
</div>


</div>
</div>
</div>
</div>
