<?php
$_SESSION['apply_js']['vms_employee_dashbard']=1;

$details = array();
$emp_id = $_SESSION['emp_id'];
$url = API."?mode=vmssingleemployeedashoard_new&empid=".$emp_id;
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$timesheet = $details['timesheet'];
$statusprogress = gettableStatusArray('project_timesheets');
$sprogress = $statusprogress['full'];
$emp_data = $details['employee'];
$quet = array('1'=>array('1','2','3'),'2'=>array('1','2','3'),'3'=>array('1','2','3'),'4'=>array('4','5','6'),'5'=>array('4','5','6'),'6'=>array('4','5','6'),'7'=>array('7','8','9'),'8'=>array('7','8','9'),'9'=>array('7','8','9'),'10'=>array('10','11','12'),'11'=>array('10','11','12'),'12'=>array('10','11','12'));

?>
<script type="text/javascript">
    function toredriacte(data){
        window.location.href = data;
    }
</script>
<div class="main-body">
    <div class="page-wrapper p-0">
        <div class="page-body">
            <div class="row">
                <div class="col-lg-3">
                    <div class="card ">
                        <!--begin::Card body-->
                        <div class="card-body d-flex flex-center flex-column pt-12 p-2">

                            <div class="symbol symbol-65px symbol-circle mb-2">
                                <?php if($emp_data['image']!=""){ ?>
                                <img src="uploads/all/<?php echo $emp_data['image']; ?>" class="percentag-img-radius img-90" alt="Details" style="border-radius: 50%;border: 8px solid #ffffff;">
                                <?php }else{ ?>
                                    <span class="percentag-img-radius img-90 f-w-600 custom_name f-24 mt-3" alt="User">
                                        <?php echo initials($emp_data['display_name']); ?>
                                    </span>
                                <?php } ?>
                                <div class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n3 mt-n3"></div>
                            </div>
                            <span class="fs-6 text-primary text-hover-primary fw-bold mb-0" style="font-size: 16px !important;"><?php echo ucwords($emp_data['display_name']) ?></span>
                            <div class="fw-semibold text-muted mb-6"><?php echo ucwords($emp_data['designation_name']) ?></div>
                            <div class="fw-semibold text-muted mb-6"><?php echo ucwords($emp_data['email_id']) ?></div>
                            <div class="fw-semibold mb-6 text-info" style="font-style: italic;font-weight: 600;">Reports to: <?php echo ucwords($emp_data['reporting_manager']) ?></div>
                            <div class="d-flex flex-center flex-wrap mt-3">
                                <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                    <div class="fs-6 fw-bold text-gray-700"
                                    ><?php
                                        echo $details['hr_dis_data']['previous_month'] !=0 ?($details['hr_dis_data']['previous_month']/8)." Days" : "-";
                                     ?>
                                </div>
                                    <div class="fw-semibold text-gray-500">Previous Month</div>
                                </div>
                                <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                    <div class="fs-6 fw-bold text-gray-700">
                                        <?php
                                            echo $details['hr_dis_data']['current_month'] !=0 ?($details['hr_dis_data']['current_month']/8)." Days" : "-";
                                        ?>
                                    </div>
                                    <div class="fw-semibold text-gray-500">This Month</div>
                                </div>
                            </div>

                            <div class="d-flex flex-center flex-wrap mt-0">

                                <a class="d-inline-block m-r-5" href="index.php?option=COM_VIEW_MONTHLY_WORK_ATTENDANCES"><button class="btn waves-effect waves-light hor-grd btn-grd-primary mb-2"><i class="icofont icofont-plus icon-white f-14"></i> New Time Sheet </button></a>
                                <a class="d-inline-block m-r-5" href="index.php?option=COM_INSERT_PROJECT_TIMESHEETS"><button class="btn waves-effect waves-light hor-grd btn-grd-primary mb-2"><i class="icofont icofont-plus icon-white f-14"></i> Old Time Sheet </button></a>
                                <a class="d-inline-block" href="index.php?option=COM_VIEW_EMP_TIMESHEETS&PAY_YEAR=<?php echo date("Y")."&PAY_MONTH=".date("m")."&EMP=".$emp_id; ?>"> <button class="btn waves-effect waves-light hor-grd btn-grd-success mb-2">
                                    <i class="ti ti-timer"></i> View History</button>
                                </a>

                                <a class="d-inline-block" href="index.php?option=COM_LIST_EMPLOYEE_LEAVES_NEW"> <button class="btn waves-effect waves-light hor-grd btn-grd-warning mb-2 ml-2">
                                    <i class="icofont icofont-plus icon-white f-14"></i> Leaves</button>
                                </a>
                            </div>
                            <!--end::Info-->

                        </div>


                        <!--end::Card body-->
                    </div>


                    <div class="card sale-card mb-2">
                        <div class="card-header">
                            <h5><?php echo date("Y"); ?> - Holidays</h5>
                        </div>
                        <div class="card-block  custom-scroll" style="max-height: 300px;">
                            <?php foreach((array)$details['assmentholidays'] as $holiday){ ?>
                            <div class="mt-1 " style="border-bottom:1px dashed #cccccc;">

                                <div class="row mt-2 mb-2" style="padding: 5px 0;">
                                    <div class="f-12 f-w-600 col-lg-8"><?php echo $holiday['holiday_name']; ?> </div>
                                    <div class="text-muted f-12 col-lg-4"><?php echo date('d - M', strtotime($holiday['holiday_date'])); ?></div>
                                </div>

                            </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card mb-2">
                        <div class="p-2">
                            <ul class="nav md-tabs infisuite_tab">
                                <?php $i=1;
                                    $month_start = date('Y-m-01');
                                $mont_last = date('Y-m-t');
                                $last_start = date("Y-m-d", strtotime('-6 month', strtotime($month_start)));

                                for($z=1;strtotime($last_start)<=strtotime($month_start);$z++){
                                    $month = date("Y-n",strtotime($last_start));
                                    $sheet = $timesheet[$month];
                                   $last_start = date("Y-m-d", strtotime('+1 month', strtotime($last_start)));
                                 ?>
                                <li class="nav-item">
                                    <a class="nav-link f-12 f-w-600 text-uppercase <?php if($i==7){ echo "active";
} ?>" data-bs-toggle="tab" href="#check<?php echo $i; ?>" role="tab" aria-selected="true"><?php echo date('M',strtotime($month."-1")); ?></a>
                                </li>
                                <?php $i++;
} ?>

                            </ul>
                        </div>
                        <div class="tab-content tabs">
                            <?php $k=1;//foreach($timesheet as $month=>$sheet){
                                $month_start = date('Y-m-01');
                                $mont_last = date('Y-m-t');
                                $last_start = date("Y-m-d", strtotime('-6 month', strtotime($month_start)));

                                for($z=1;strtotime($last_start)<=strtotime($month_start);$z++){
                                    $month = date("Y-n",strtotime($last_start));
                                    $sheet = $timesheet[$month];
                                    $last_start = date("Y-m-d", strtotime('+1 month', strtotime($last_start)));
                            ?>
                            <div class="tab-pane m-r-10 m-l-10 <?php if($k==7){ echo "active";
} ?>" id="check<?php echo $k; ?>" role="tabpanel">
                                <?php if(count((array)$sheet)>0){ ?>
                                <table class="table f-12 hover-badge">
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col" class="text-end">With Me</th>
                                        <th scope="col" class="text-end">With Mgr</th>
                                        <th scope="col" class="text-end">Approved</th>
                                        <th scope="col" class="text-end">Total</th>
                                    </tr>
                                    <?php
                                    $i=1; $wk_active=0; $wk_process=0; $wk_approved=0; $wk_total=0;
                                        foreach((array)$sheet as $weekkey=>$week){
                                            $total=0; $process=0; $active=0; $approved=0;
                                            foreach((array)$week as $timkey=>$val){
                                                $total = $total+($val/8);
                                                if($timkey!=0 && $timkey!=199){
                                                    $process = $process+($val/8);
                                                }
                                            }
                                            $active = $week['0']!=""?($week['0']/8):0;
                                            $approved = $week['199']!=""?($week['199']/8):0;
                                            $wk_active = $wk_active+$active;
                                            $wk_process = $wk_process+$process;
                                            $wk_approved = $wk_approved+$approved;
                                            $wk_total = $wk_total+$total;
                                            $wkst = $details['weekdates'][$month][$weekkey]['start'];
                                            $wked = $details['weekdates'][$month][$weekkey]['end'];
                                            if($i==1){ $wkst =  $month."-01";
}
                                            if($i==count((array)$sheet)){ $wked =  date("Y-m-t",strtotime($wkst));
}
                                        ?>
                                        <tr>
                                            <th scope="col">
                                                <a class="f-14 label label-danger" href="index.php?option=COM_INSERT_PROJECT_TIMESHEETS&CHANGE_DATE=<?php echo $wked; ?>&EMP=<?php echo $_SESSION['emp_id']?>">
                                                <?php echo date("d M",strtotime($wkst))." - ".date("d M",strtotime($wked)); ?> </a></th>

                                            <td style="text-align: right;"><?php echo $active; ?></td>
                                            <td style="text-align: right;"><?php echo $process; ?></td>
                                            <td style="text-align: right;"><?php echo $approved; ?></td>
                                            <td style="text-align: right;"><?php echo $total; ?></td>
                                        </tr>

                                     <?php $i++;
} ?>

                                    <tr>
                                        <th scope="col" class="text-right">Total</th>
                                        <th scope="col" class="text-right"><?php echo $wk_active; ?></th>
                                        <th scope="col" class="text-right"><?php echo $wk_process; ?></th>
                                        <th scope="col" class="text-right"><?php echo $wk_approved; ?></th>
                                        <th scope="col" class="text-right"><?php echo $wk_total; ?></th>
                                    </tr>
                                </table>
                                <?php $cquqr[date('n',strtotime($month."-1"))] = $wk_total;
}else{ ?>
                                    <div class="text-center mt-3">
                                    <h6 class="text-center mt-3"> No Data</h6>
                                     <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                 </div>
                                <?php } ?>
                            </div>
                            <?php $k++;
} ?>

                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body mt-1">
                            <div class="row">
                                <div class="col-lg-12">
                                    <ul class="nav md-tabs infisuite_tab">
                                        <li class="nav-item">
                                            <a class="nav-link f-w-600 active" data-bs-toggle="tab" href="#current-quater" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Current Quater</a>
                                            <div class="slide"></div>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link f-w-600" data-bs-toggle="tab" href="#previous-quater" role="tab" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Previous Quater</a>
                                            <div class="slide"></div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-lg-6"></div>
                            </div>
                        </div>
                        <div class="tab-content tabs card-body">
                            <div class="tab-pane active" id="current-quater" role="tabpanel">
                                <div class="row border-bottom pb-3 mb-3">
                                    <div class="col-lg-5" style="margin-top: 35px;">
                                        <?php
                                        $att1=0; $ccqq = date('n');
                                        $base_total =0; $total_cc =0;
                                        foreach((array)$quet[$ccqq] as $utr){
                                            $utdt = date("Y-m", strtotime(date('Y-'.$utr."-01")));
                                            $acmh = $details['count_days'][$utdt];
                                            $cquqr[$utr] = $cquqr[$utr]!="" ? $cquqr[$utr] :0;
                                            $perc = 0;
                                            if($acmh!=0){
                                                $perc = round(($cquqr[$utr]/$acmh)*100,2);
                                            }
                                            if($perc>0 && $att1==0){
                                                $att1 = 1;
                                            }
                                            ?>
                                        <div class="p-3 b-radius-10 mb-3" style="border: 1px dashed #cccccc;">
                                            <div class="row align-items-center">
                                                <div class="col-lg-2">
                                                    <button class="btn waves-effect waves-light bg-gray btn-icon">
                                                        <i class="ti-calendar text-primary f-w-600"></i>
                                                    </button>
                                                </div>
                                                <div class="col-lg-5">
                                                    <h6 class="f-w-600"><?php echo date('M',strtotime(date("Y-".$utr."-01"))); ?></h6>
                                                    <span class="text-muted f-w-600 "></span>
                                                </div>
                                                <div class="col-lg-5 text-right">
                                                    <div class="d-flex align-items-center mt-3 float-right">
                                                        <span class="f-18 f-w-600 m-r-10"><?php echo $cquqr[$utr]."/".$acmh; ?></span>
                                                        <span class="form-label label label-success"><?php echo $perc; ?>%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php
                                             $base_total = $base_total+$cquqr[$utr];
                                            $total_cc = $total_cc+$acmh;
                                           $dataatt1[] = date('F',strtotime("2022-".$utr."-01")).":".($cquqr[$utr]!="" ? $cquqr[$utr]:0);
                                        } ?>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="card-header pt-0">
                                            <h5>Planned Vs Actual</h5>
                                        </div>
                                        <?php if($att1>0){ ?>
                                        <div id="chart_Donut_at1" style="width: 100%; height: 300px;"></div>
                                        <input type="hidden" name="dchart1" id="dchart1" value="<?php echo implode('||',$dataatt1); ?>">

                                          <div class="row mt-2">
                                            <div class="col-6">
                                                <div class="s-caption bg-default"></div>
                                                <div class="s-cont d-inline-block">
                                                    <h5 class="f-w-700 m-b-0 text-right"><?php echo $total_cc; ?></h5>
                                                    <p class="m-b-0">Planned Days</p>
                                                </div>
                                            </div>
                                            <div class="col-6 b-l-default">
                                                <div class="s-caption bg-c-red"></div>
                                                <div class="s-cont d-inline-block">
                                                    <h5 class="f-w-700 m-b-0 text-right"><?php echo $base_total; ?></h5>
                                                    <p class="m-b-0">Actual Days</p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php }else{ ?>
                                            <h6 class="text-center mt-3"> No Data</h6>
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="previous-quater" role="tabpanel">
                                <div class="row border-bottom pb-3 mb-3">
                                    <div class="col-lg-5">
                                        <?php
                                        $att2=0;
                                        $cckkdate =   date("Y-m-01", strtotime( date('Y-m-01')." -3 months"));
                                        $cckkyyy = date('Y', strtotime($cckkdate));
                                        $ccqq = date('n')-3;
                                        $base_total =0; $total_cc =0;
                                        if($ccqq<1){ $ccqq =12;
}
                                        foreach((array)$quet[$ccqq] as $utr){
                                            $cquqr[$utr] = $cquqr[$utr]!="" ? $cquqr[$utr] :0;
                                            $utdt = date("Y-m", strtotime(date($cckkyyy.'-'.$utr."-01")));
                                            $acmh = $details['count_days'][$utdt];

                                            $perc = 0;
                                            if($acmh!=0){
                                            $perc = round(($cquqr[$utr]/$acmh)*100,2);
                                            }

                                        if($perc>0 && $att2==0){
                                            $att2 = 1;
                                          } $graps= array();
                                          ?>
                                        <div class="p-3 b-radius-10 mb-3" style="border: 1px dashed #cccccc;">
                                            <div class="row align-items-center">
                                                <div class="col-lg-2">
                                                    <button class="btn waves-effect waves-light bg-gray btn-icon">
                                                        <i class="ti-calendar text-primary f-w-600"></i>
                                                    </button>
                                                </div>
                                                <div class="col-lg-5">
                                                    <h6 class="f-w-600"><?php echo date('F',strtotime("2022-".$utr."-01")); ?></h6>
                                                    <span class="text-muted f-w-600 f-12"></span>
                                                </div>
                                                <div class="col-lg-5 text-right">
                                                    <div class="d-flex align-items-center mt-3 float-right">
                                                        <span class="f-18 f-w-600 m-r-10"><?php echo $cquqr[$utr]."/".$acmh; ?></span>
                                                        <span class="form-label label label-success"><?php echo $perc; ?>%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                            $graps[] =  date('F',strtotime("2022-".$utr."-01"));
                                            $graps[] =  $cquqr[$utr]!=""?$cquqr[$utr]:0;
                                            $dataatt2[] =  implode(':',$graps);
                                            $base_total = $base_total+$cquqr[$utr];
                                            $total_cc = $total_cc+$acmh;
                                        }

                                        ?>

                                    </div>
                                    <div class="col-lg-7">
                                        <div class="card-header pt-0">
                                        <h5 class="text-center">Planned Vs Actual</h5>
                                        </div>
                                        <?php if($att2>0){ ?>
                                        <div id="chart_Donut_at2" style="width: 70%; height: 150px;"></div>
                                         <input type="hidden" name="dchart2" id="dchart2" value="<?php echo implode('||',$dataatt2); ?>">

                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <div class="s-caption bg-default"></div>
                                                <div class="s-cont d-inline-block">
                                                    <h5 class="f-w-700 m-b-0 text-right"><?php echo $total_cc; ?></h5>
                                                    <p class="m-b-0">Planned Days</p>
                                                </div>
                                            </div>
                                            <div class="col-6 b-l-default">
                                                <div class="s-caption bg-c-red"></div>
                                                <div class="s-cont d-inline-block">
                                                    <h5 class="f-w-700 m-b-0 text-right"><?php echo $base_total; ?></h5>
                                                    <p class="m-b-0">Actual Days</p>
                                                </div>
                                            </div>
                                        </div>

                                        <?php }else{ ?>
                                            <h6 class="text-center mt-3"> No Data</h6>
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>With Me</h5>
                        </div>
                        <div class="card-body custom-scroll" style="max-height:275px;">

                            <?php if(count((array)$details['create'])>0){
                                foreach((array)$details['create'] as $submited){ ?>
                                  <div class="row mt-3 pb-3 cursor-pointer" onclick="toredriacte('index.php?option=COM_INSERT_PROJECT_TIMESHEETS&CHANGE_DATE=<?php echo $submited['lastofweek']; ?>&EMP=<?php echo $_SESSION['emp_id']?>')" style="border-bottom:1px dashed #cccccc;" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                       <div class="col-lg-3">
                                    <button data-bs-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;<?php echo $sprogress[$submited['current_status']]['name']; ?>&nbsp;&nbsp;" class="waves-effect waves-light btn-primary p-l-15 p-r-15 p-t-10 p-b-10 b-radius-5 border-0" style="background-color: <?php echo "#".$_SESSION['status'][$submited['current_status']]['background_color']; ?>"><i class="ti-calendar f-w-600"></i></button>
                                </div>
                                <div class="col-lg-6 p-0">
                                    <h6 class="f-w-600"><?php echo date('F', strtotime($submited['lastofweek'])); ?></h6>
                                    <span class="text-muted f-12">
                                    <?php echo fromDate(date('Y-m-d',strtotime("+1 day",strtotime($submited['startofweek']))))." - ".fromDate(date('Y-m-d',strtotime("+1 day",strtotime($submited['lastofweek'])))); ?>

                                    </span>
                                </div>
                                <div class="col-lg-3 text-right">
                                    <button class="btn waves-effect waves-light bg-gray btn-icon btn-sm">
                                        <a class="f-12" href="index.php?option=COM_INSERT_PROJECT_TIMESHEETS&CHANGE_DATE=<?php echo $submited['lastofweek']; ?>&EMP=<?php echo $_SESSION['emp_id']?>">
                                        <i class="ti-arrow-right"></i></a>
                                    </button>
                                </div>
                                    </div>
                            <?php }
                             }else{?>
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            <?php } ?>

                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>With Manager</h5>
                        </div>
                        <div class="card-body custom-scroll" style="max-height:275px;">

                            <?php
                                if(count((array)$details['submited'])>0){
                            foreach((array)$details['submited'] as $submited){ ?>
                            <div class="row mt-3 pb-3 cursor-pointer"  onclick="toredriacte('index.php?option=COM_INSERT_PROJECT_TIMESHEETS&CHANGE_DATE=<?php echo $submited['lastofweek']; ?>&EMP=<?php echo $_SESSION['emp_id']?>')" style="border-bottom:1px dashed #cccccc;" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <div class="col-lg-3">
                                    <button data-bs-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;<?php echo $sprogress[$submited['current_status']]['name']; ?>&nbsp;&nbsp;" class="waves-effect waves-light btn-primary p-l-15 p-r-15 p-t-10 p-b-10 b-radius-5 border-0" style="background-color: <?php echo "#".$_SESSION['status'][$submited['current_status']]['background_color']; ?>"><i class="ti-calendar f-w-600"></i></button>
                                </div>
                                <div class="col-lg-6 p-0">
                                    <h6 class="f-w-600"><?php echo date('F', strtotime($submited['timesheet_date'])); ?></h6>
                                    <span class="text-muted f-12">
                                    <?php echo fromDate(date('Y-m-d',strtotime("+1 day",strtotime($submited['startofweek']))))." - ".fromDate(date('Y-m-d',strtotime("+1 day",strtotime($submited['lastofweek'])))); ?>
                                </span>
                                </div>
                                <div class="col-lg-3 text-right">
                                    <button class="btn waves-effect waves-light bg-gray btn-icon btn-sm">
                                        <a  href="index.php?option=COM_INSERT_PROJECT_TIMESHEETS&CHANGE_DATE=<?php echo $submited['lastofweek']; ?>&EMP=<?php echo $_SESSION['emp_id']?>">
                                        <i class="ti-arrow-right"></i></a>
                                    </button>
                                </div>
                            </div>
                            <?php } ?>
                            <?php } else { ?>
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            <?php } ?>

                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>Recently Approved</h5>
                        </div>
                        <div class="card-body custom-scroll" style="max-height:275px;">

                            <?php
                             if(count((array)$details['approved'])>0){
                            foreach((array)$details['approved'] as $submited){ ?>
                            <div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">
                                <div class="col-lg-3">
                                    <button data-bs-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;<?php echo $sprogress[$submited['current_status']]['name']; ?>&nbsp;&nbsp;" class="waves-effect waves-light btn-primary p-l-15 p-r-15 p-t-10 p-b-10 b-radius-5 border-0"><i class="ti-calendar f-w-600" style="background-color: <?php echo "#".$_SESSION['status'][$submited['current_status']]['background_color']; ?>"></i></button>
                                </div>
                                <div class="col-lg-6 p-0">
                                    <h6 class="f-w-600"><?php echo date('F', strtotime($submited['year']."-".$submited['month']."-01")); ?></h6>
                                    <span class="text-muted f-12"><?php echo date('M Y', strtotime($submited['year']."-".$submited['month']."-01")); ?></span>
                                </div>

                            </div>
                            <?php } ?>
                            <?php } else { ?>
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
