<?php
$_SESSION['apply_js']['hr_admin_dashboard'] = '1';
$_SESSION['apply_js']['multiselest'] = '1';
function percnetagetwovalue($num_amount, $num_total) {
  $count1 = $num_amount / $num_total;
  $count2 = $count1 * 100;
  $count = round($count2);
  return $count;
}

$admin_type = $_SESSION['admin_type'];
if(in_array('HR_ADMIN',(array)$_SESSION['role_ids'])){
    $admin_type = 1;
}
$url = API."?mode=employeedashboard&emp_id=".$_SESSION['emp_id']."&admin_type=".$admin_type."&user_email=".$_SESSION['user_email'];
$response = callAPI($url);
$data = json_decode($response,1);

$absentisamration = $data['absentisamration'];
$newemp = $data['nemp'];
$daterangenemp = $data['daterangenemp'];
$newlistemp = $data['nemplist'];

$todayatten = $data['todayattendants'];
$graphjoinandleave = $data['graphjoinandleave'];
$salarygraphres = $data['salarygraphres'];

$startyeardate = date("Y-"."01"."-d");
$mk=1; for($i=0;$i<12;$i++){
  $employee_graph[] = date("Y-m-t",strtotime("+".$i."month", strtotime($startyeardate)));
  $employee_val[] = $graphjoinandleave['j'.$mk] !=""?$graphjoinandleave['j'.$mk]:0;
  $employee_val2[] = $graphjoinandleave['r'.$mk] !=""?$graphjoinandleave['r'.$mk]:0;
  $salarygraphresary[] = $salarygraphres['j'.$mk] !=""?$salarygraphres['j'.$mk]:0;
  $mk++;
}
$uimg = $reviewdataw[0]['image']!=""?$reviewdataw[0]['image']:"no-image.jpg";

$reviewurl = API."?mode=getriviewsdata&response_type=0";
$reviewdata = callAPI($reviewurl);
$reviewdata = json_decode($reviewdata, 1);
$emp_miss_punch_list = $reviewdata['emp_miss_punch_list'] ?? array();

?>
    <script type="text/javascript">
        function changegraph(val){
            if(val==1){
                $("#ain_renve").show("slow");
                $("#revenue").hide("slow");

                $("#act_emp").show("slow");
                $("#emp_active").hide("slow");

                $("#month_display2").show("slow");
                $("#month_display1").hide("slow");
                 $("#count_btn").removeClass("Active");
                  $("#salary_btn").addClass("Active");

            }
            if(val==2){
                $("#ain_renve").hide("slow");
                $("#revenue").show("slow");
                $("#act_emp").hide("slow");
                $("#emp_active").show("slow");
                $("#month_display1").show("slow");
                $("#month_display2").hide("slow");
                 $("#salary_btn").removeClass("Active");
                  $("#count_btn").addClass("Active");

            }
        }
    function moreemployees(id_name) {
        console.log ('inserted to filtertable'+id_name);
        var employee_names_list = document.getElementById(id_name).value;
         console.log ('inserted to filtertable'+employee_names_list);

        jQuery.ajax({
            url: "views/custom_view_employees_popup.php",
            data:'empids='+employee_names_list,
            type: "POST",
            success:function(data){
      console.log("popup open ok");
                $('#dynamic_popupxxl').html(data);
                $('#kt_modal_dynamic_popupxxl').modal("show");
            },
            error:function (){}
        });

      }
        function  getBasicInfoPopUp(emp_id) {
            jQuery.ajax({
                  url: "views/custom_insert_basic_info.php",
                  data:'emp_id='+emp_id,
                  type: "POST",
                  success:function(data){
                    $('#dynamic_popupxxl').html(data); //popup1
                   $('#kt_modal_dynamic_popupxxl').modal("show");
                    $(".js-example-basic-single").select2();
                    $(".js-example-disabled-results").select2();
                  },
                  error:function (){}
            });
        }
        function useractivethedashboard(emp_id,name){
            console.log('Testtttt');
            swal({
                title: "Are you sure?",
                text: "To create a user for this employee "+name,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#2ed8b6",
                confirmButtonText: "Yes, please",
                cancelButtonText: "No, cancel !",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function(isConfirm){
                console.log('Chekkkkkk');
                if (isConfirm) {
                    console.log("Yesssssssss");
                    jQuery.ajax({
                        url: "ajax.php",
                        data:'page=createuser&empid='+emp_id,
                        type: "POST",
                        success:function(data){

                            $("#useractive_"+emp_id).html(' <i class="fa fa-user-plus float-right text-success" data-bs-toggle="tooltip" data-placement="top" title="" data-bs-original-title="User activated" ></i>');
                            swal("Yes", "User created successfully", "success");
                        }
                    });

                } else {
                    console.log("Noooooooooo");
                }
            });
        }
        function userdeactive(emp_id,name){
            swal({
                title: "Are you sure?",
                text: "To deactivate a user for this employee "+name,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#2ed8b6",
                confirmButtonText: "Yes, please",
                cancelButtonText: "No, cancel !",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function(isConfirm){
                console.log('Chekkkkkk');
                if (isConfirm) {
                    console.log("Yesssssssss");
                    jQuery.ajax({
                        url: "ajax.php",
                        data:'page=setupcomponents&is_active=1&table_name=application_users&id='+emp_id,
                        type: "POST",
                        success:function(data){

                            $("#deactive_"+emp_id).html(' <i class="fa fa-user-times float-right text-success" data-bs-toggle="tooltip" data-placement="top" title="" data-bs-original-title="User Deactivated" ></i>');
                            swal("Yes", "User is deactivated successfully", "success");

                        }
                    });

                } else {
                    console.log("Noooooooooo");
                }
            });
        }
    </script>
    <div class="p-2">

        <button class="artificial_intelligence_btn bg-primary border-0" onclick="getArtificialIntelligence('HR_ADMIN_DASHBOARD')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Dashboard Summary With AI">
            <i class="ti-info spin-infinite"></i>
        </button>
    </div>
        <div class="row mt-4">

                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card social-card">

                        <?php if($todayatten['Todayday']!=""){  ?>
                            <div class="ribbon ribbon-top-right"><span class="color-danger">Holiday</span></div>
                            <?php }else{ ?>
                                <div class="ribbon ribbon-top-right"><span class="color-success">Working</span></div>
                                <?php } ?>
                                    <div class="card-body text-center">
                                        <div class="text-linkedin m-b-20"><i class="fa fa-users"></i></div>
                                        <h3 class="text-linkedin f-w-700"><?php echo customNanAndEmptyValidation($todayatten['total_emp']); ?></h3>
                                        <p>Total Employees</p>
                                        <input type="hidden" id="view_total_emp" value="<?php echo urlencode(json_encode($todayatten['total_emp_ids'])); ?>">
                                        <p class="m-b-0 m-t-15">
                                            <button class="badge badge-inverse-primary bg-white" onclick="moreemployees('view_total_emp')">View
                                            </button>
                                        </p>
                                    </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card social-card">


                        <div class="card-body text-center">
                            <div class="text-facebook m-b-20"><i class="fa fa-clock"></i></div>
                            <h3 class="text-facebook f-w-700"><?php echo customNanAndEmptyValidation($todayatten['present']); ?></h3>
                            <p>Presents</p>
                            <input type="hidden" id="view_present_emp" value="<?php echo urlencode(json_encode($todayatten['present_ids'])); ?>">
                            <p class="m-b-0 m-t-15">
                                <button class="badge badge-inverse-info bg-white" onclick="moreemployees('view_present_emp')">View
                                </button>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card social-card">

                        <div class="card-body text-center">
                            <div class="text-danger m-b-20"><i class="fa fa-clock"></i></div>
                            <h3 class="text-danger f-w-700"><?php echo customNanAndEmptyValidation($todayatten['absent']); ?></h3>
                            <p>Absents</p>
                            <input type="hidden" id="view_absent_emp" value="<?php echo urlencode(json_encode($todayatten['absent_ids'])); ?>">
                            <p class="m-b-0 m-t-15">
                                <button class="badge badge-inverse-danger bg-white" onclick="moreemployees('view_absent_emp')">View
                                </button>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card social-card">

                        <div class="card-body text-center">
                            <div class="text-linkedin m-b-20"><i class="fa fa-plane text-success"></i></div>
                            <h3 class="text-success f-w-700"><?php echo customNanAndEmptyValidation($todayatten['planned_leave']); ?></h3>
                            <p>Planned Leaves</p>
                            <input type="hidden" id="view_planned_leave_emp" value="<?php echo urlencode(json_encode($todayatten['planned_leave_ids'])); ?>">
                            <p class="m-b-0 m-t-15">
                                <button class="badge badge-inverse-success bg-white" onclick="moreemployees('view_planned_leave_emp')">View
                                </button>
                            </p>
                        </div>
                    </div>
                </div>

                <?php
                $countvaluempty = $employee_graph==""?1:0;
                $salaryvaluempty = $data['payroll']==""?1:0;

                ?>
                <!-- sale revenue card start -->
                <div class="col-lg-8 col-md-12">
                    <div class="card sale-card">
                        <input type="hidden" name="emp_dashboard1" id="emp_dashboard11" value="<?php echo implode('|',(array)$employee_graph); ?>">
                        <input type="hidden" name="emp_dashboard1" id="emp_dashboard12" value="<?php echo implode('|',(array)$employee_val); ?>">
                        <input type="hidden" name="emp_dashboard1" id="emp_dashboard13" value="<?php echo implode('|',(array)$employee_val2); ?>">
                        <input type="hidden" name="emp_dashboard1" id="emp_dashboard14" value="<?php echo implode('|',(array)$employee_graph); ?>">
                        <input type="hidden" name="dash_paroll" id="dash_paroll" value="<?php echo implode('|',(array)$salarygraphresary); ?>">
                        <div class="card-header">

                            <div id="month_display1">
                                <h5 class="card-label">Month Wise - Employees </h5> </div>
                            <div style="display: none;" id="month_display2">
                                <h3 class="card-label">Month Wise - Salaries </h3>
                            </div>
                            <div class="card-header-right">
                                <div class="badge badge-inverse-primary font-weight-bolder Active cursor-pointer" onclick="changegraph('2')" id="count_btn" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">Count</div>
                                <div onclick="changegraph('1')" class="badge badge-inverse-primary font-weight-bolder cursor-pointer" id="salary_btn" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">Salaries</div>

                            </div>
                        </div>

                        <div class="card-block" id="emp_active">
                            <?php if($countvaluempty == "1"){
                             echo "<h4 class='p-40 text-center'><i class='fa fa-warning'></i>Month Wise - Employees Not Available</h4>";

                            }else{?>
                            <div id="deal-analytic-chart-dashboard4" class="chart-shadow" style="height:325px"></div>
                            <?php } ?>
                        </div>
                        <div class="card-block" id="ain_renve" style="display:none">
                            <?php if($countvaluempty == "1"){
                              echo "<h4 class='p-40 text-center'><i class='fa fa-warning'></i>Month Salaries - Employees Not Available</h4>";
                            }else{ ?>
                            <div id="salaray-chart-dashboard4" class="chart-shadow" style="height:325px"></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="card sale-card">
                        <div class="card-header">
                            <h5>Full time Employee Ratio</h5>
                        </div>
                        <?php
                            $regularemp = 0;
                            if($newemp['regular'] !=0 && $newemp['regular'] !="" && $newemp['em_count'] !=0 && $newemp['em_count'] !="" ){
                              $regularemp = ($newemp['regular']/$newemp['em_count'])*100;
                            }
                              ?>
                            <input type="hidden" id="kt_mixed_widget_fultime_dashbord4_chart_value" name="kt_mixed_widget_fultime_dashbord4_chart_value" value="<?php echo $regularemp; ?>">
                            <input type="hidden" id="total_employee_cnt" name="kt_mixed_widget_fultime_dashbord4_chart_value" value="<?php echo $newemp['em_count']; ?>">
                            <input type="hidden" id="regular_employee_cnt" name="kt_mixed_widget_fultime_dashbord4_chart_value" value="<?php echo round($newemp['regular']); ?>">
                            <div class="card-block text-center">
                                <div class="card-block text-center">


                                    <div class="m-b-20" style="text-align: -webkit-center;">
                                        <?php
                                            $avgemp = 0;
                                           if($newemp['regular'] !=0 && $newemp['regular'] !="" && $newemp['em_count'] !=0 && $newemp['em_count'] !="" ){
                                            $avgemp = customNanAndEmptyValidation(number_format(($newemp['regular']/$newemp['em_count'])*100,1));
                                          }
                                          ?>
                                            <div class="circle_gradiant mb-2" style="--p1: <?php echo ($avgemp*3.6).'deg';?>; --p1_color: #dc3545;--p2_color: #2ed8b6;">
                                                <span class="text-dark f-16 f-w-600 d-block"><?php echo $avgemp; ?>% </span>
                                            </div>
                                    </div>

                                    <h6 class="m-t-40">Total Employees</h6>
                                    <h3 class="f-w-700 m-b-40"><i class="fa fa-group"></i> <?php echo customNanAndEmptyValidation($newemp['em_count']); ?></h3>
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="m-b-5 f-12">Regular</p>
                                            <input type="hidden" id="view_regular_emp" value="<?php echo urlencode(json_encode($todayatten['regular_emp_ids'])); ?>">
                                            <button type="button" class="btn p-0 border-0 bg-transparent text-warning h3 f-w-700 close" onclick="moreemployees('view_regular_emp')"><?php echo customNanAndEmptyValidation($newemp['regular']); ?></button>
                                        </div>
                                        <div class="col-6">
                                            <p class="m-b-5 f-12">Contract</p>
                                            <input type="hidden" id="view_contact_emp" value="<?php echo urlencode(json_encode($todayatten['contract_emp_ids'])); ?>">
                                            <button type="button" class="btn p-0 border-0 bg-transparent text-danger h3 f-w-700 close" onclick="moreemployees('view_contact_emp')"><?php echo customNanAndEmptyValidation($newemp['contact']); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <!-- sale revenue card end -->
                </div>



                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card sale-card ">
                        <div class="card-header dontcopy">
                            <h5>MGR vs EMP</h5>
                        </div>
                            <div class="card-block text-center">
                                <?php
                                  $mngcnt = $daterangenemp['manger_count'] !=""?$daterangenemp['manger_count']:0;
                                  $empmngcnt = $daterangenemp['em_count'] !=""?$daterangenemp['em_count']:0;
                                  $percntagemngemp = 0;
                                  if($mngcnt != 0 && $empmngcnt !=0){
                                      $ttemp = $empmngcnt+$mngcnt;
                                      $percntagemngemp = round(($empmngcnt/$ttemp)*100);
                                      $mngandempavg = $daterangenemp['em_count']/$daterangenemp['manger_count'];
                                  }
                                  $mgcnt = $mngcnt !="0"?1:0;
                                  ?>
                                    <div class="m-b-20" style="text-align: -webkit-center;">
                                        <div class="circle_gradiant" style="--p1: <?php echo customNanAndEmptyValidation($percntagemngemp*3.6).'deg';?>; --p1_color: #dc3545;--p2_color: #2ed8b6;">
                                            <span class="text-dark f-16 f-w-600 d-block"><?php echo customNanAndEmptyValidation($mgcnt); ?> : <?php echo customNanAndEmptyValidation(round($mngandempavg)); ?></span>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="s-caption text-primary"><i class="fa fa-female f-20"></i></div>
                                            <div class="s-cont d-inline-block">
                                                <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($daterangenemp['manger_count']); ?></h5>
                                                <p class="m-b-0">Managers</p>
                                            </div>
                                        </div>
                                        <div class="col-6 b-l-default">
                                            <div class="s-caption text-danger "><i class="fa fa-users  "></i></div>
                                            <div class="s-cont d-inline-block">
                                                <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($daterangenemp['em_count']); ?></h5>
                                                <p class="m-b-0">Employees</p>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card sale-card ">
                        <div class="card-header">
                            <h5>By Gender</h5>
                        </div>


                        <div class="card-block text-center">
                            <?php $totalemp = $daterangenemp['female']+$daterangenemp['male'];
                                $totalemper = 0;
                                if($daterangenemp['male'] != 0 && $daterangenemp['male'] !="" && $totalemp != 0){
                                  $totalemper = round(($daterangenemp['male']/$totalemp)*100);
                                }
                                ?>
                                <div class="m-b-20" style="text-align: -webkit-center;">
                                    <div class="circle_gradiant" style="--p1: <?php echo customNanAndEmptyValidation(($totalemper*3.6)).'deg';?>; --p1_color: #dc3545;--p2_color: #2ed8b6;">
                                        <span class="text-dark f-16 f-w-600 d-block"><?php echo customNanAndEmptyValidation($totalemper)."%"; ?></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="s-caption  text-primary"><i class="fa fa-female f-20"></i></div>
                                        <div class="s-cont d-inline-block">
                                            <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($daterangenemp['female']); ?></h5>
                                            <p class="m-b-0">Female</p>
                                        </div>
                                    </div>
                                    <div class="col-6 b-l-default">
                                        <div class="s-caption  text-danger"><i class="fa fa-male f-20"></i></div>
                                        <div class="s-cont d-inline-block">
                                            <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($daterangenemp['male']); ?></h5>
                                            <p class="m-b-0">male</p>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card sale-card ">
                        <div class="card-header">
                            <h5>Absenteeism Rate </h5>
                        </div>
                        <div class="card-block text-center">

                            <?php
                                $presentcnt = $totalempdaycnt = $absentscnt = $pressents =$Absents =  0;
                                if($absentisamration['presentcnt'] !="" && $absentisamration['presentcnt'] != 0){
                                  $presentcnt = $absentisamration['presentcnt'];
                                }
                                if($absentisamration['totalempdaycnt'] !="" && $absentisamration['totalempdaycnt'] != 0){
                                  $totalempdaycnt = $absentisamration['totalempdaycnt'];
                                }
                                if($absentisamration['absentscnt'] !="" && $absentisamration['absentscnt'] != 0){
                                  $absentscnt = $absentisamration['absentscnt'];
                                }
                                if($presentcnt != 0 && $totalempdaycnt != 0){
                                    $pressents =  round(($presentcnt/$totalempdaycnt)*100);
                                }
                                if($absentscnt != 0 && $totalempdaycnt != 0){
                                    $Absents =  round(($absentscnt/$totalempdaycnt)*100);
                                }

                                ?>

                                <div class="m-b-20" style="text-align: -webkit-center;">
                                    <div class="circle_gradiant" style="--p1: <?php echo ($Absents*3.6).'deg';?>; --p1_color: #dc3545;--p2_color: #2ed8b6;">
                                        <span class="text-dark f-16 f-w-600 d-block"><?php echo customNanAndEmptyValidation($Absents)."%"; ?></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="s-caption "><i class="fa fa-thumbs-up text-primary "></i></div>
                                        <div class="s-cont d-inline-block">
                                            <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($pressents); ?></h5>
                                            <p class="m-b-0">Presents</p>
                                        </div>
                                    </div>
                                    <div class="col-6 b-l-default">
                                        <div class="s-caption "> <i class="fa fa-thumbs-down  text-danger"></i></div>
                                        <div class="s-cont d-inline-block">
                                            <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($Absents); ?></h5>
                                            <p class="m-b-0">Absents</p>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card sale-card ">
                        <div class="card-header">
                            <h5>Joining vs Exit</h5>
                        </div>
                        <?php
                            $Joining = customNanAndEmptyValidation($data['joining']);
                            $Relieving = customNanAndEmptyValidation($data['relive']);
                            $totaljoirel = $Joining +$Relieving;
                        $joinrelievprese = 0;
                        if($totaljoirel !=0 && $Joining !=0 && $Joining !=""){
                          $joinrelievprese = customNanAndEmptyValidation(round(($Joining/$totaljoirel)*100));
                        }
                        ?>
                            <div class="card-block text-center">

                                <div class="m-b-20" style="text-align: -webkit-center;">
                                    <div class="circle_gradiant mb-2" style="--p1: <?php echo ($joinrelievprese*3.6).'deg';?>; --p1_color: #dc3545;--p2_color: #2ed8b6;">
                                        <span class="text-dark f-16 f-w-600 d-block"><?php echo $joinrelievprese."%"; ?></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="s-caption "><i class="fa fa-user text-primary"></i></div>
                                        <div class="s-cont d-inline-block">
                                            <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($Joining); ?></h5>
                                            <p class="m-b-0">Joining</p>
                                        </div>
                                    </div>
                                    <div class="col-6 b-l-default">
                                        <div class="s-caption"><i class="fa fa-user-times text-danger"></i></div>
                                        <div class="s-cont d-inline-block">
                                            <h5 class="f-w-700 m-b-0"><?php echo $Relieving; ?></h5>
                                            <p class="m-b-0">Relieving</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>

                <!--- New  starts --->

                <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                    <div class="card new-cust-card custom_hover_card">
                        <div class="card-header">
                            <h5>New Employees </h5>
                            <span class="form-label label label-success">
                                <?php echo count($data['join_emp_data']); ?>
                            </span>
                            <div class="card-header-right">
                                <small> Last 3 Months </small>
                            </div>
                        </div>
                        <div class="card-body custom-scroll" style=" height: 470px;">
                            <?php
                            if(!empty($data['join_emp_data'])) {
                                foreach ($data['join_emp_data'] as  $joins) {
                                    if($joins['user_id']==""){
                                         $sidetittle = "User Not Active";
                                         $iccolor = "danger";
                                    }else{
                                        $sidetittle = "User Activated";
                                        $iccolor = "success";
                                    }
                            ?>
                            <div class="align-middle m-b-25">
                                <?php if($joins['image']!=""){ ?>
                                <img src="uploads/all/<?php echo $joins['image']; ?>" class="img-radius img-40 align-top m-r-15" alt="Attachment Preview">
                                <?php }else{ ?>
                                <span alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round p-1 f-12 text-center"><?php echo initials($joins['display_name']); ?></span>
                                <?php } ?>
                                <div class="d-inline-block width73">
                                    <a href="#!" class="text-primary text-capitalize" onclick="getBasicInfoPopUp(<?php echo $joins['id'];?>)">
                                        <h6 class="f-12 mb-1 text-uppercase f-w-600"><?php echo $joins['display_name']; ?></h6>
                                        <div id="useractive_<?php echo $joins['id']; ?>" >
                                            <i <?php  if($joins['user_id']==""){  ?> onclick="useractivethedashboard(<?php echo $joins['id'].",'".$joins['display_name']."'"; ?>)" <?php
} ?> class="fa fa-user-plus float-right text-<?php echo $iccolor; ?>" data-bs-toggle="tooltip" data-placement="top" title="" data-bs-original-title="<?php echo $sidetittle; ?>" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                        </div>
                                    </a>
                                    <p class="text-muted m-b-0 f-12"> Joining Date
                                        <?php echo date('dS M Y',strtotime($joins['joining_date'])); ?>
                                    </p>
                                </div>
                            </div>
                            <?php }
                            }else{ ?>
                            <p class="text-muted f-16 fw-bold m-b-30 text-center"><i class="fa fa-warning"></i> No Records</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>


                <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                    <div class="card new-cust-card custom_hover_card">
                        <div class="card-header">
                            <h5>Resigned Employees </h5>
                            <span class="form-label label label-success">
                                <?php echo count($data['resign_emp_data']); ?>
                            </span>
                            <div class="card-header-right">
                                <small> Last 3 Months </small>
                            </div>
                        </div>
                        <div class="card-body custom-scroll" style=" height: 470px;">
                            <?php
                                if(!empty($data['resign_emp_data'])) {
                                    foreach ($data['resign_emp_data'] as  $joins) {
                                        if($joins['is_active']=="0"){
                                             $sidetittle = "User Active";
                                             $iccolor = "danger";
                                        }else{
                                            $sidetittle = "User Deactivated";
                                            $iccolor = "success";
                                        }

                                ?>
                            <div class="align-middle m-b-25">
                                <?php if($joins['image']!=""){ ?>
                                <img src="uploads/all/<?php echo $joins['image']; ?>" class="img-radius img-40 align-top m-r-15" alt="Attachment Preview">
                                <?php }else{ ?>
                                <span alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round p-1 f-12 text-center"><?php echo initials($joins['display_name']); ?></span>
                                <?php } ?>
                                <div class="d-inline-block width73">
                                    <a href="#!" class="text-primary text-capitalize" onclick="getBasicInfoPopUp(<?php echo $joins['id'];?>)">
                                        <h6 class="f-12 mb-1 text-uppercase f-w-600"><?php echo $joins['display_name']; ?>
                                        </h6>
                                        <div id="deactive_<?php echo $joins['user_id']; ?>">
                                            <i <?php  if($joins['is_active']=="0"){ ?> onclick="userdeactive(<?php echo $joins['user_id'].",'".$joins['display_name']."'"; ?>)" <?php
} ?> class="fa fa-user-times float-right text-<?php echo $iccolor; ?>" data-bs-toggle="tooltip" data-placement="top" title="" data-bs-original-title="<?php echo $sidetittle; ?>" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                            </i>
                                        </div>
                                    </a>
                                    <p class="text-muted m-b-0 f-12"> Joining Date
                                    <?php echo date('dS M Y',strtotime($joins['date_of_leaving'])); ?>
                                </p>
                                </div>

                            </div>

                        <?php }
                            }else{ ?>
                            <p class="text-muted f-16 fw-bold m-b-30 text-center"><i class="fa fa-warning"></i> No Records</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>



                <div class="col-lg-6 col-md-12 col-sm-12 col-12">
                    <div class="card table-card">
                        <div class="card-header">
                            <h5>Reviews</h5>
                        </div>
                        <div class="card-body custom-scroll" style="height: 470px;">
                            <ul class="nav  md-tabs infisuite_tab mt-2 p-2">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#home7pending" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-hourglass-2"></i>  Pending  <span class="form-label badge bg-danger mb-0"><?php echo count((array)$reviewdata['pending']); ?></span></a>

                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#profile7approve" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-thumbs-up"></i> Approve  <span class="form-label badge bg-warning ml-2 mb-0"><?php echo count((array)$reviewdata['approve']); ?></span></a>

                                </li>
                            </ul>
                            <div class="tab-content card-body">
                                <div class="tab-pane active" id="home7pending" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover m-b-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Description</th>


                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                  if(!empty($reviewdata['pending'])){ $k = 1;   foreach ($reviewdata['pending'] as  $reviewda){
                                                     $Notification_DateTime = notificationdatesdiff($reviewda['request_date']) ." ago";
                                                     $Notification_Table = $reviewda['table'];
                                                     $Notification_Table_Id = $reviewda['transaction_id'];
                                                     if(date('Y-m-d') == date('Y-m-d',strtotime($reviewda['request_date']))){
                                                        $class_text = 'text-warning';
                                                     }else{
                                                        $class_text = 'text-danger';
                                                     }
                                                     $getpagename = getPagenametotable($reviewda['table'],'approval_page_name');
                                                     if($getpagename != ""){
                                                        $pagename = $getpagename;
                                                     }else{
                                                        $pagename = "COM_INSERT_".ucwords($reviewda['table']);
                                                     }
                                                     $nrid = deocdeId("id:".$reviewda['transaction_id']);
                                                     if($pagename=='COM_EDIT_ATTENDENACE_REGULARIZATION_REQUESTS'){
                                                        $pagename = 'COM_INSERT_ATTENDENACE_REGULARIZATION_REQUEST';
                                                    }
                                                     $link = "index.php?option=".$pagename."&Rid=".$nrid;
                                                     $icon_initials = initials($_SESSION['users'][$reviewda['created_by']]);
                                                     $icon_initials_name = $_SESSION['users'][$reviewda['created_by']];
                                                     $current_status = $reviewda['current_status'];
                                                     $Notification_Message = ucwords(str_replace("_"," ",($reviewda['table'])))." - ".$icon_initials_name;
                                                     ?>
                                                    <tr>

                                                        <td>
                                                            <?php echo $k; ?>
                                                        </td>
                                                        <td>
                                                            <div class="d-inline-block align-middle display-flex">
                                                                <div class="d-inline-block">
                                                                    <a href="<?php echo $link;?>" class="text-hover-primary mb-0 font-size-sm <?php echo $class_text; ?>"><h6 class="f-15 mb-1 f-w-600"><?php echo $Notification_Message; ?></h6></a>
                                                                    <p class="text-muted m-b-0 f-13 <?php echo $class_text; ?>">
                                                                        <?php echo $Notification_DateTime; ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </td>

                                                    </tr>
                                                    <?php  $k++;
}
} else{
                                                        echo "<tr><td colspan='3'><h4 class='p-40 text-center'><i class='fa fa-warning'></i> No Reviews Available</h4></td></tr>";
                                                     } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="profile7approve" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover m-b-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Description</th>


                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                 if(!empty($reviewdata['approve'])){ $k = 1; foreach ($reviewdata['approve'] as $reviewda){
                                                    $Notification_DateTime = notificationdatesdiff($reviewda['request_date']) ." ago";
                                                    $Notification_Table = $reviewda['table'];
                                                    $Notification_Table_Id = $reviewda['transaction_id'];
                                                    if(date('Y-m-d') == date('Y-m-d',strtotime($reviewda['request_date']))){
                                                       $class_text = 'text-warning';
                                                    }else{
                                                       $class_text = 'text-danger';
                                                    }
                                                    $getpagename = getPagenametotable($reviewda['table'],'approval_page_name');
                                                    if($getpagename != ""){
                                                       $pagename = $getpagename;
                                                    }else{
                                                       $pagename = "COM_INSERT_".ucwords($reviewda['table']);
                                                    }
                                                    $nrid = deocdeId("id:".$reviewda['transaction_id']);
                                                    if($pagename=='COM_EDIT_ATTENDENACE_REGULARIZATION_REQUESTS'){
                                                        $pagename = 'COM_INSERT_ATTENDENACE_REGULARIZATION_REQUEST';
                                                    }
                                                    $link = "index.php?option=".$pagename."&Rid=".$nrid;
                                                    $icon_initials = initials($_SESSION['users'][$reviewda['created_by']]);
                                                    $icon_initials_name = $_SESSION['users'][$reviewda['created_by']];
                                                    $current_status = $reviewda['current_status'];
                                                    $Notification_Message = ucwords(str_replace("_"," ",($reviewda['table'])))." - ".$icon_initials_name;
                                                    ?>
                                                    <tr>

                                                        <td>
                                                            <?php echo $k; ?>
                                                        </td>
                                                        <td>
                                                            <div class="d-inline-block align-middle display-flex">
                                                                <div class="d-inline-block">
                                                                    <a href="<?php echo $link;?>" class="text-hover-primary mb-0 font-size-sm <?php echo $class_text; ?>"><h6><?php echo $Notification_Message; ?></h6></a>
                                                                    <p class="text-muted m-b-0 <?php echo $class_text; ?>">
                                                                        <?php echo $Notification_DateTime; ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </td>

                                                    </tr>
                                                    <?php  $k++;
}
} else{
                                                       echo "<tr><td colspan='3'><h4 class='p-40 text-center'><i class='fa fa-warning'></i> No Reviews Available</h4></td></tr>";
                                                    } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="col-lg-6 col-md-12 col-sm-12 col-12">
                    <div class="card table-card">
                        <div class="card-header">
                            <h5>Employee Resignation - Asset Handover</h5>
                        </div>
                        <div class="card-body custom-scroll" style="height: 600px;">
                            <ul class="nav  md-tabs infisuite_tab mt-2 p-2">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#hnhome7pending" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Pending  <span class="form-label badge bg-danger mb-0"><?php echo count((array)$reviewdata['reg_emp_data']); ?></span></a>

                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#hnprofile7approve" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Handover  <span class="form-label badge bg-success ml-2 mb-0"><?php echo count((array)$reviewdata['reg_emp_com_data']); ?></span></a>

                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#hand7approve" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> With Out Resign - Handover  <span class="form-label badge bg-warning ml-2 mb-0"><?php echo count((array)$reviewdata['reg_emp_wo_data']); ?></span></a>

                                </li>
                            </ul>
                            <div class="tab-content mt-5" id="myTabContent">
                                <div class="tab-pane fade active show   " id="hnhome7pending" role="tabpanel" aria-labelledby="hnhome7pending">
                                    <?php foreach((array)$reviewdata['reg_emp_data'] as $dob){
                                        $display_name = $dob['name'];
                                        $display_photo = $dob['image'];
                                        $receiver_emp_id = $dob['id'];
                                    ?>
                                    <div class="align-middle m-b-25">
                                        <?php if($display_photo) {?>
                                        <img src="uploads/all/<?php echo $display_photo; ?>" class="img-radius img-40 align-top m-r-15" alt="User Profile">
                                        <?php } else { ?>
                                        <span alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round p-2 f-10 text-center"><?php echo initials($display_name); ?></span>
                                        <?php } ?>
                                        <div class="d-inline-block width73">
                                            <a href="#!" class=" text-capitalize">
                                                <h6 class="f-15 mb-1 text-uppercase f-w-600"> <?php echo $display_name; ?></h6>
                                            </a>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="tab-pane fade" id="hnprofile7approve" role="tabpanel" aria-labelledby="hnprofile7approve">
                                    <div>
                                        <?php foreach((array)$reviewdata['reg_emp_com_data'] as $dob){
                                            $display_name = $dob['name'];
                                            $display_photo = $dob['image'];
                                            $receiver_emp_id = $dob['id'];
                                        ?>
                                        <div class="align-middle m-b-25">
                                            <?php if($display_photo) {?>
                                            <img src="uploads/all/<?php echo $display_photo; ?>" class="img-radius img-40 align-top m-r-15" alt="User Profile">
                                            <?php } else { ?>
                                            <span alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round p-2 f-10 text-center"><?php echo initials($display_name); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73">
                                                <a href="#!" class=" text-capitalize">
                                                    <h6 class="f-15 mb-1 text-uppercase f-w-600"> <?php echo $display_name; ?></h6>
                                                </a>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="hand7approve" role="tabpanel" aria-labelledby="hand7approve">
                                    <?php foreach((array)$reviewdata['reg_emp_wo_data'] as $dob){
                                        $display_name = $dob['name'];
                                        $display_photo = $dob['image'];
                                        $receiver_emp_id = $dob['id'];
                                    ?>
                                    <div class="align-middle m-b-25">
                                        <?php if($display_photo) {?>
                                        <img src="uploads/all/<?php echo $display_photo; ?>" class="img-radius img-40 align-top m-r-15" alt="User Profile">
                                        <?php } else { ?>
                                        <span alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round p-2 f-10 text-center"><?php echo initials($display_name); ?></span>
                                        <?php } ?>
                                        <div class="d-inline-block width73">
                                            <a href="#!" class=" text-capitalize">
                                                <h6 class="f-15 mb-1 text-uppercase f-w-600"> <?php echo $display_name; ?></h6>
                                            </a>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12 col-sm-12 col-12">
                    <div class="card table-card">
                        <div class="card-header">
                            <h5>Employee Miss Punch <span class="label label-primary f-12"><?php echo count((array)$emp_miss_punch_list) ?? 0  ?></span></h5>
                        </div>
                        <div class="card-body custom-scroll" style="height: 600px;">
                            <div class="table-responsive">
                                <table class="table table-hover m-b-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Employee</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Clock In</th>
                                            <th scope="col">Clock Out</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count((array)$emp_miss_punch_list) > 0) { ?>
                                        <?php foreach ($emp_miss_punch_list as $key => $row) { ?>
                                        <tr>
                                            <td>
                                                <?php echo $key + 1; ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($row['employee_name'] ?? '-'); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($row['attendace_date'] ?? '-'); ?>
                                            </td>
                                            <td>
                                                <?php echo !empty($row['clock_in']) ? htmlspecialchars($row['clock_in']) : '-'; ?>
                                            </td>
                                            <td>
                                                <?php echo !empty($row['clock_out']) ? htmlspecialchars($row['clock_out']) : '-'; ?>
                                            </td>
                                            <?php  ?>
                                        </tr>
                                        <?php } ?>
                                        <?php } else { ?>
                                        <tr>
                                            <td colspan="6">
                                                <p class="text-muted f-16 fw-bold m-b-30 text-center"><i class="fa fa-warning"></i> No Records</p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
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

                <?php if($_POST['ajaxpage'] =='Y'){?>

                    <script src="assets/pages/widget/amchart/amcharts.js"></script>
                    <script src="assets/pages/widget/amchart/serial.js"></script>
                    <script src="assets/bower_components/chartist/js/chartist.js"></script>
                    <script type="text/javascript" src="assets/pages/widget/hr_admin_dashboard.js"></script>

                    <?php } ?>
