<?php
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
foreach((array)$datalocations as $dalocations){
    $locations[$dalocations['id']] = $dalocations;
}
$loc  = $_GET['LOC']!=""?$_GET['LOC']:0;
$admin_type  = $_SESSION['admin_type'];
$displaylocations  = array();
if($_SESSION['admin_type'] ==1){ $displaylocations = $locations;
}
if(in_array(REVIWER_ROLE,(array)$_SESSION['role_ids'])){
 $admin_type = '1';
}
$employee_id = $_SESSION['emp_id'];
$application_user_id = $_SESSION['userid'];
if($admin_type == '1'){
$vdata1["where"] = array('is_active'=> 0, 'user_id'=>$_SESSION['userid']);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=user_report_access_locations&data=".$vdata;
$configesponse = callAPI($vurl);
$locaccess = json_decode($configesponse,1);
foreach((array)$locaccess  as $access){
    $displaylocations[$access['location_id']] = $locations[$access['location_id']];
    $loca_ids[] = $access['location_id'];
}
    $admin_type =2;
}
$locstr = "";
if($_SESSION['admin_type'] !=1 || $_GET['LOC']!=""){
     if($_SESSION['admin_type'] == 1){ $admin_type =2;
}
      if($_GET['LOC']!="" && $_GET['LOC']!="0"){
     $locstr= "&location_id=1&location_id_value=".$_GET['LOC'];
      }else{
    $locstr= "&location_id=0&location_id_value=".implode(",",(array)$loca_ids);
      }
}
$url = API."?mode=vendorstaqadmindashboard&admin_type=".$admin_type."&user_type=".$_SESSION['user_type']."&user_email=".$_SESSION['user_email']."&employee_id=".$employee_id."&application_user_id=".$application_user_id."&start_date=".$startyearmonth."&end_date=".$endyearmonth;
$data_details1 = callAPI($url);
$new_dashboard = json_decode($data_details1,1);

 $url1 = API."?mode=admindashboarddetails&admin_type=".$admin_type."&user_type=".$_SESSION['user_type']."&user_email=".$_SESSION['user_email']."&employee_id=".$employee_id."&application_user_id=".$application_user_id."&check=1".$locstr;
$data_details1 = callAPI($url1);
$emp_count_dashboard = json_decode($data_details1,1);
$budgetthisyear = 0;
$utilizationthisyear = 0;
$utilizationsixmonth = 0;
$utilizationsmonth = 0;
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

?>
<script>
    function changeLocation(){
        var locationvalueget = $("#locationvalueget").val();
        window.location.href = "index.php?LOC="+locationvalueget;
    }
</script>
<div class="p-0" id="dashdis">
    <?php if(count((array)$displaylocations)>0){ ?>
    <div class="row m-b-10 mt-3">
        <div class="col-lg-3">
            <select aria-label="Locationvalueget" class="form-select js-example-basic-single" id="locationvalueget" onchange="changeLocation()">
                <option <?php if($loc==0){ echo "SELECTED";
}  ?>  value="0">ALL</option>
                    <?php foreach((array)$displaylocations as $locat){ ?>

                <option <?php if($loc==$locat['id']){ echo "SELECTED";
}  ?> value="<?php echo $locat['id']; ?>"> <?php echo $locat['location_name']; ?> </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <?php } ?>
    <div class="row">

        <div class="col-xl-4 col-md-6 col-lg-6 col-md-6 col-sm-6 col-12">
            <input type="hidden" id="thismonthnkey" value="<?php echo $thismonthkey; ?>">
            <div class="card ticket-card">
                <div class="card-body">
                    <p class="m-b-30 bg-c-blue lbl-card"><i class="fa fa-group m-r-10"></i>Total Head Count </p>
                    <div class="text-center button-list">
                        <h2 class="mb-4 d-block text-c-blue"> 122</h2>

                        <div>
                        <button style="margin-right: 10px;font-size: 12px !important;" type="button" class="btn border waves-effect waves-light" data-bs-toggle="tooltip" data-placement="top" title="Billable">Billable
                                  <span class="badge bg-primary">80</span>
                              </button>
                              <button style="margin-right: 10px;font-size: 12px !important;" type="button" class="btn border waves-effect waves-light" data-bs-toggle="tooltip" data-placement="top" title="Non-Billable">Non-Billable
                                  <span class="badge bg-success">30</span>
                              </button>
                              <button style="margin-right: 10px;font-size: 12px !important;" type="button" class="btn border waves-effect waves-light" data-bs-toggle="tooltip" data-placement="top" title="Milestone">Milestone
                                  <span class="badge bg-warning">12</span>
                              </button>
              </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="card ticket-card">
                <div class="card-body">
                    <p class="m-b-50 bg-c-green lbl-card"><i class="fa fa-group m-r-10"></i>Total Vendor Count</p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-green"> 12</h2>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="card ticket-card">
                <div class="card-body">
                    <p class="m-b-50 bg-c-red lbl-card"><i class="fa fa-group m-r-10"></i>This Month joinings / Separation </p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-red"><?php echo customNanAndEmptyValidation($emp_count_dashboard['this_month_join'])." / ".customNanAndEmptyValidation($emp_count_dashboard['this_month_leaving']); ?></h2>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once 'vendor_employee_dash_board.php'; ?>
    <div class="row">
        <div class="col-md-12 col-xl-8">
            <div class="card sale-card">
                <input type="hidden" id="monthdata" value="<?php echo $monthdata;?>">
                <input type="hidden" id="estimationdata" value="<?php echo $estimationdata;?>">
                <input type="hidden" id="actualdata" value="<?php echo $actualdata;?>">
                <div class="card-header">
                    <h5>Overall Budget Utilization</h5>

                    <div class="card-header-right">
                        <span class="badge badge-inverse-primary bg-white"> <i class="fa fa-calendar-check-o text-primary"></i>  Financial Year <?php echo date('Y-M',strtotime($startyearmonth))." To ".date('Y-M',strtotime($endyearmonth)); ?></span>
                    </div>
                </div>
                <?php $countvaluempty = $estimationdata==""?1:0;   ?>
                <div class="card-block">
                    <?php if($countvaluempty == "1"){
                        echo "<h4 class='p-40 text-center'><i class='fa fa-warning'></i> Budget Utilization Data Not Available</h4>";
                    }else{ ?>
                        <div id="deal-analytic-chart" class="chart-shadow" style="height:300px"></div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xl-4">
            <div class="card sale-card">
                <div class="card-header">
                    <h5>Total Utilization</h5>
                </div>
                <input type="hidden" id="hid_budget" value="<?php echo round($budgetthisyear); ?>">
                <input type="hidden" id="hid_utilization" value="<?php echo round($utilizationthisyear); ?>">

                <div class="card-block text-center">
                    <div id="tot-rev-chart" class="tot-rev-chart chart-shadow st-cir-chart" style="width:100px;height:100px">
                        <h3 class="f-12">
                            <?php echo convert_Rupee_Number(customNanAndEmptyValidation(round($utilizationthisyear))); ?>
                        </h3>
                    </div>
                    <h6 class="m-t-40">This Year's Budget</h6>
                    <h3 class="f-w-700 m-b-40"><?php echo convert_Rupee_Number(customNanAndEmptyValidation(round($budgetthisyear))); ?></h3>
                    <div class="row">
                        <div class="col-4">
                            <p class="m-b-5">Year</p>
                            <h3 class="f-w-700 text-c-yellow f-14"><?php echo convert_Rupee_Number(customNanAndEmptyValidation(round($utilizationthisyear))); ?></h3>
                        </div>
                        <div class="col-4">
                            <p class="m-b-5">Quater</p>
                            <h3 class="f-w-700 text-c-yellow f-14"><?php echo convert_Rupee_Number(customNanAndEmptyValidation(round($utilizationsixmonth))); ?></h3>
                        </div>
                        <div class="col-4">
                            <p class="m-b-5">Month</p>
                            <h3 class="f-w-700 text-c-yellow f-14"><?php echo convert_Rupee_Number(customNanAndEmptyValidation(round($utilizationsmonth))); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card bg-light">
                    <div class="card-header">
                        <h5>Usage</h5>
                    </div>
                    <div class="row p-4">
                        <div class="col-xl-6 col-md-6 col-lg-6 col-sm-6 col-12">
                            <input type="hidden" name="coscentervalue_this_month" id="coscentervalue_this_month" value="<?php echo implode(",",(array)$priviousmonthcostcenter['usage_vendor']); ?>">
                            <input type="hidden" name="coscentervalue_priview_month" id="coscentervalue_priview_month" value="<?php echo implode(",",(array)$priviousmonthcostcenter['emp_count']); ?>">
                            <div class="card prod-bar-card">
                                <div class="card-header">
                                    <h5>CostCenter</h5>
                                </div>
                                <div class="card-block">
                                    <p><?php echo $thismonthyeardisplay." & ".$priviousmonthdisplay?></p>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div id="pbc-1" class="pbc-chart" style="height:50px"></div>
                                            <h6><?php echo number_format($totat_coscentervalue_this_month); ?>
                                            <?php echo $totat_coscentervalue_this_month >= $totat_coscentervalue_priview_month ?" <i class='fas fa-caret-up m-r-10 f-18 text-c-blue'></i>":" <i class='fas fa-caret-down m-r-10 f-18 text-c-red'></i>"; ?>
                                        </h6>
                                    </div>
                                    <div class="col-6">
                                        <div id="pbc-2" class="pbc-chart" style="height:50px"></div>
                                        <h6><?php echo number_format($totat_coscentervalue_priview_month); ?>
                                        <?php echo $totat_coscentervalue_priview_month >= $totat_coscentervalue_last_month ?" <i class='fas fa-caret-up m-r-10 f-18 text-c-blue'></i>":" <i class='fas fa-caret-down m-r-10 f-18 text-c-red'></i>"; ?>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            <div class="col-xl-6 col-md-6 col-lg-6 col-sm-6 col-12">
                    <input type="hidden" name="vendor_this_month" id="vendor_this_month" value="<?php echo implode(",",(array)$thismonthvendoremployecount['usage_vendor']); ?>">
                    <input type="hidden" name="vendor_priview_month" id="vendor_priview_month" value="<?php echo implode(",",(array)$priviousmonthvendoremployecount['usage_vendor']); ?>">
                    <div class="card prod-bar-card">
                        <div class="card-header">
                            <h5>Vendor</h5>
                        </div>
                        <div class="card-block">
                            <p><?php echo $thismonthyeardisplay." & ".$priviousmonthdisplay?></p>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div id="pbc-3" class="pbc-chart" style="height:50px"></div>
                                    <h6><?php echo convert_Rupee_Number(customNanAndEmptyValidation($thismonthvendoremployecount['usage_month_total'])); ?><?php echo $thismonthvendoremployecount['usage_month_total'] >= $priviousmonthvendoremployecount['usage_month_total'] ?" <i class='fas fa-caret-up m-r-10 f-18 text-c-blue'></i>":" <i class='fas fa-caret-down m-r-10 f-18 text-c-red'></i>"; ?></h6>
                                </div>
                                <div class="col-6">
                                    <div id="pbc-4" class="pbc-chart" style="height:50px"></div>
                                    <h6><?php echo convert_Rupee_Number(customNanAndEmptyValidation($priviousmonthvendoremployecount['usage_month_total'])); ?>
                                    <?php echo $thismonthvendoremployecount['usage_month_total'] <= $priviousmonthvendoremployecount['usage_month_total'] ?" <i class='fas fa-caret-up m-r-10 f-18 text-c-blue'></i>":" <i class='fas fa-caret-down m-r-10 f-18 text-c-red'></i>"; ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
</div>
    <div class="col-lg-6">
    <div class="card bg-light">
        <div class="card-header">
            <h5>Employee Count</h5>
        </div>
        <div class="row p-4">
            <div class="col-xl-6 col-md-6 col-lg-6 col-sm-6 col-12">
                <input type="hidden" name="coscentervalueemp_this_month" id="coscentervalueemp_this_month" value="<?php echo implode(",",(array)$thismonthcostcenter['emp_count']); ?>">
                <input type="hidden" name="coscentervalueemp_priview_month" id="coscentervalueemp_priview_month" value="<?php echo implode(",",(array)$thismonthcostcenter['emp_count']); ?>">

                <div class="card prod-bar-card">
                    <div class="card-header">
                        <h5>CostCenter</h5>
                    </div>
                    <div class="card-block">
                        <p><?php echo $thismonthyeardisplay." & ".$priviousmonthdisplay?></p>
                        <div class="row text-center">
                            <div class="col-6">
                                <div id="pbc-5" class="pbc-chart" style="height:50px"></div>
                                <h6><?php echo number_format(customNanAndEmptyValidation($total_coscentervalueemp_this_month));?>
                                <?php echo $total_coscentervalueemp_this_month >= $total_coscentervalueemp_priview_month ?" <i class='fas fa-caret-up m-r-10 f-18 text-c-blue'></i>":" <i class='fas fa-caret-down m-r-10 f-18 text-c-red'></i>"; ?>
                            </h6>
                        </div>
                        <div class="col-6">
                            <div id="pbc-6" class="pbc-chart" style="height:50px"></div>
                            <h6><?php echo number_format(customNanAndEmptyValidation($total_coscentervalueemp_priview_month)); ?>
                            <?php echo $total_coscentervalueemp_priview_month >= $total_coscentervalueemp_last_month ?" <i class='fas fa-caret-up m-r-10 f-18 text-c-blue'></i>":" <i class='fas fa-caret-down m-r-10 f-18 text-c-red'></i>"; ?>
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-6 col-md-6 col-lg-6 col-sm-6 col-12">
        <input type="hidden" name="coscentervalue_this_month" id="vendorvalueemp_this_month" value="<?php echo implode(",",(array)$thismonthvendoremployecount['emp_count']); ?>">
        <input type="hidden" name="coscentervalue_priview_month" id="vendorvalueemp_priview_month" value="<?php echo implode(",",(array)$priviousmonthvendoremployecount['emp_count']); ?>">
        <div class="card prod-bar-card">
            <div class="card-header">
                <h5>Vendor</h5>
            </div>
            <div class="card-block">
                <p><?php echo $thismonthyeardisplay." & ".$priviousmonthdisplay; ?></p>
                <div class="row text-center">
                    <div class="col-6">
                        <div id="pbc-7" class="pbc-chart" style="height:50px"></div>
                        <h6><?php echo number_format(customNanAndEmptyValidation($thismonthvendoremployecount['month_total']));?><?php echo $thismonthvendoremployecount['month_total'] >= $priviousmonthvendoremployecount['month_total'] ?" <i class='fas fa-caret-up m-r-10 f-18 text-c-blue'></i>":" <i class='fas fa-caret-down m-r-10 f-18 text-c-red'></i>"; ?></h6>
                    </div>
                    <div class="col-6">
                        <div id="pbc-8" class="pbc-chart" style="height:50px"></div>
                        <h6><?php echo  number_format(customNanAndEmptyValidation($priviousmonthvendoremployecount['month_total']));?>
                        <?php echo $thismonthvendoremployecount['month_total'] <= $priviousmonthvendoremployecount['month_total'] ?" <i class='fas fa-caret-up m-r-10 f-18 text-c-blue'></i>":" <i class='fas fa-caret-down m-r-10 f-18 text-c-red'></i>"; ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>

</div>
</div>

</div>
</div>

</div>
