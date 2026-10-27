<?php
    $url = API."?mode=employeedashboard";
    $response = callAPI($url);
    $data = json_decode($response,1);
    $newemp = $data['nemp'];
    foreach($data['fgraph'] as $fgrap){
        $employee_graph[] = $fgrap['name'];
        $employee_val[] = $fgrap['val'];
        $employee_val2[] = $fgrap['val2'];
    }
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

        }
        if(val==2){
            $("#ain_renve").hide("slow");
            $("#revenue").show("slow");
            $("#act_emp").hide("slow");
            $("#emp_active").show("slow");
            $("#month_display1").show("slow");
            $("#month_display2").hide("slow");
        }
    }
</script>
<div class="row">
    <div class="col-xl-4">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">

                    <div id="month_display1"> <h3 class="card-label" >Month Wise - Employees </h3> </div>
                    <div style="display: none;" id="month_display2"> <h3 class="card-label" >Month Wise - Salaries </h3> </div>
                    <div class="card-toolbar ml-15">
                        <div onclick="changegraph('1')" id="revenue" class="btn btn-primary font-weight-bolder" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">Salaries</div>
                        <div  class="btn btn-primary font-weight-bolder" onclick="changegraph('2')" id="act_emp" style="display: none;" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">Count</div>
                    </div>
                </div>
            </div>
            <div class="card-body" id="emp_active">
                <input type="hidden" name="emp_dashboard1" id="emp_dashboard11" value="<?php echo implode('|',(array)$employee_graph); ?>">
                <input type="hidden" name="emp_dashboard1" id="emp_dashboard12" value="<?php echo implode('|',(array)$employee_val); ?>">
                <input type="hidden" name="emp_dashboard1" id="emp_dashboard13" value="<?php echo implode('|',(array)$employee_val2); ?>">
                <div id="kt_amcharts_dashboard4_active_employee" style="height: 260px;"></div>
            </div>
            <input type="hidden" name="emp_dashboard1" id="emp_dashboard11_2" value="<?php echo implode('|',(array)$employee_graph); ?>">
            <input type="hidden" name="dash_paroll" id="dash_paroll" value="<?php echo implode('|',(array)$data['payroll']); ?>">
            <div class="card-body" style="display: none;" id="ain_renve">
                <div id="kt_amcharts_dashboard4_revenue" style="height: 260px;"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Full time Employee Ratio</h3>
                </div>
            </div>
            <input type="hidden" id="kt_mixed_widget_fultime_dashbord4_chart_value" name="kt_mixed_widget_fultime_dashbord4_chart_value" value="<?php echo ($newemp['regular']/$newemp['em_count'])*100; ?>">
            <div id="kt_mixed_widget_fultime_dashbord4_chart" style="height: 280px"></div>
            <div class="mb-3" style="margin-left: auto; margin-right: auto;">
                <div style="float: left; " class="mr-3">
                    <div style="width: 15px;height: 15px;background-color: #2b9f9a;float: left;"></div>
                    <div style="float: left;"><b>&nbsp; Regular </b></div>
                </div>
                <div style="float: left;">
                    <div style="width: 15px;height: 15px;background-color: #d1f8f6;float: left;"></div>
                    <div style="float: left;"><b>&nbsp; Contract </b></div>
                </div>
             </div>


        </div>
    </div>
    <div class="col-xl-4">
        <div class="row">
            <div class="col-xl-6">
            <!--begin::Stats Widget 16-->
                <a href="#" class="card card-custom card-stretch gutter-b">
                    <!--begin::Body-->
                    <div class="card-body">
                        <span class="svg-icon svg-icon-info svg-icon-3x ml-n1">

                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <path d="M12,4.56204994 L7.76822128,9.6401844 C7.4146572,10.0644613 6.7840925,10.1217854 6.3598156,9.76822128 C5.9355387,9.4146572 5.87821464,8.7840925 6.23177872,8.3598156 L11.2317787,2.3598156 C11.6315738,1.88006147 12.3684262,1.88006147 12.7682213,2.3598156 L17.7682213,8.3598156 C18.1217854,8.7840925 18.0644613,9.4146572 17.6401844,9.76822128 C17.2159075,10.1217854 16.5853428,10.0644613 16.2317787,9.6401844 L12,4.56204994 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                    <path d="M3.5,9 L20.5,9 C21.0522847,9 21.5,9.44771525 21.5,10 C21.5,10.132026 21.4738562,10.2627452 21.4230769,10.3846154 L17.7692308,19.1538462 C17.3034221,20.271787 16.2111026,21 15,21 L9,21 C7.78889745,21 6.6965779,20.271787 6.23076923,19.1538462 L2.57692308,10.3846154 C2.36450587,9.87481408 2.60558331,9.28934029 3.11538462,9.07692308 C3.23725479,9.02614384 3.36797398,9 3.5,9 Z M12,17 C13.1045695,17 14,16.1045695 14,15 C14,13.8954305 13.1045695,13 12,13 C10.8954305,13 10,13.8954305 10,15 C10,16.1045695 10.8954305,17 12,17 Z" fill="#000000"></path>
                                </g>
                            </svg>
                            <!--end::Svg Icon-->
                        </span>
                        <div class="text-inverse-white font-weight-bolder font-size-h5 mb-2 mt-5">Promotion - 2021</div>
                        <div class="font-weight-bold text-inverse-white font-size-sm"><?php echo $data['averge_employee']."%"; ?></div>
                    </div>            <!--end::Body-->
                </a>
            </div>

            <div class="col-xl-6">

                <!--begin::Stats Widget 16-->
                <a href="#" class="card card-custom card-stretch gutter-b">
                    <!--begin::Body-->
                    <div class="card-body">
                        <span class="svg-icon svg-icon-info svg-icon-3x ml-n1">

                             <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <path d="M12,22 C7.02943725,22 3,17.9705627 3,13 C3,8.02943725 7.02943725,4 12,4 C16.9705627,4 21,8.02943725 21,13 C21,17.9705627 16.9705627,22 12,22 Z" fill="#000000" opacity="0.3"/>
        <path d="M11.9630156,7.5 L12.0475062,7.5 C12.3043819,7.5 12.5194647,7.69464724 12.5450248,7.95024814 L13,12.5 L16.2480695,14.3560397 C16.403857,14.4450611 16.5,14.6107328 16.5,14.7901613 L16.5,15 C16.5,15.2109164 16.3290185,15.3818979 16.1181021,15.3818979 C16.0841582,15.3818979 16.0503659,15.3773725 16.0176181,15.3684413 L11.3986612,14.1087258 C11.1672824,14.0456225 11.0132986,13.8271186 11.0316926,13.5879956 L11.4644883,7.96165175 C11.4845267,7.70115317 11.7017474,7.5 11.9630156,7.5 Z" fill="#000000"/>
    </g>
</svg>
                            <!--end::Svg Icon-->
                        </span>
                        <div class="text-inverse-white font-weight-bolder font-size-h5 mb-2 mt-5">Absenteeism - 2021</div>
                        <div class="font-weight-bold text-inverse-white font-size-sm"><?php echo $data['absent']." Days"; ?></div>
                    </div>            <!--end::Body-->
                </a>
            </div>
            <div class="col-xl-6">

                <!--begin::Stats Widget 16-->
                <a href="#" class="card card-custom card-stretch gutter-b">
                    <!--begin::Body-->
                    <div class="card-body">
                        <span class="svg-icon svg-icon-info svg-icon-3x ml-n1">

                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <polygon points="0 0 24 0 24 24 0 24"/>
        <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
        <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
    </g>
</svg>
                            <!--end::Svg Icon-->
                        </span>
                        <div class="text-inverse-white font-weight-bolder font-size-h5 mb-2 mt-5">New Joinee - 2021</div>
                        <div class="font-weight-bold text-inverse-white font-size-sm"><?php echo $data['joining']." Members"; ?></div>
                    </div>            <!--end::Body-->
                </a>
            </div>

            <div class="col-xl-6">

                <!--begin::Stats Widget 16-->
                <a href="#" class="card card-custom card-stretch gutter-b">
                    <!--begin::Body-->
                    <div class="card-body">
                        <span class="svg-icon svg-icon-info svg-icon-3x ml-n1">

                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <path d="M14.0069431,7.00607258 C13.4546584,7.00607258 13.0069431,6.55855153 13.0069431,6.00650634 C13.0069431,5.45446114 13.4546584,5.00694009 14.0069431,5.00694009 L15.0069431,5.00694009 C17.2160821,5.00694009 19.0069431,6.7970243 19.0069431,9.00520507 L19.0069431,15.001735 C19.0069431,17.2099158 17.2160821,19 15.0069431,19 L3.00694311,19 C0.797804106,19 -0.993056895,17.2099158 -0.993056895,15.001735 L-0.993056895,8.99826498 C-0.993056895,6.7900842 0.797804106,5 3.00694311,5 L4.00694793,5 C4.55923268,5 5.00694793,5.44752105 5.00694793,5.99956624 C5.00694793,6.55161144 4.55923268,6.99913249 4.00694793,6.99913249 L3.00694311,6.99913249 C1.90237361,6.99913249 1.00694311,7.89417459 1.00694311,8.99826498 L1.00694311,15.001735 C1.00694311,16.1058254 1.90237361,17.0008675 3.00694311,17.0008675 L15.0069431,17.0008675 C16.1115126,17.0008675 17.0069431,16.1058254 17.0069431,15.001735 L17.0069431,9.00520507 C17.0069431,7.90111468 16.1115126,7.00607258 15.0069431,7.00607258 L14.0069431,7.00607258 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.006943, 12.000000) scale(-1, 1) rotate(-90.000000) translate(-9.006943, -12.000000) "/>
        <rect fill="#000000" opacity="0.3" transform="translate(14.000000, 12.000000) rotate(-270.000000) translate(-14.000000, -12.000000) " x="13" y="6" width="2" height="12" rx="1"/>
        <path d="M21.7928932,9.79289322 C22.1834175,9.40236893 22.8165825,9.40236893 23.2071068,9.79289322 C23.5976311,10.1834175 23.5976311,10.8165825 23.2071068,11.2071068 L20.2071068,14.2071068 C19.8165825,14.5976311 19.1834175,14.5976311 18.7928932,14.2071068 L15.7928932,11.2071068 C15.4023689,10.8165825 15.4023689,10.1834175 15.7928932,9.79289322 C16.1834175,9.40236893 16.8165825,9.40236893 17.2071068,9.79289322 L19.5,12.0857864 L21.7928932,9.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(19.500000, 12.000000) rotate(-90.000000) translate(-19.500000, -12.000000) "/>
    </g>
</svg>
                            <!--end::Svg Icon-->
                        </span>
                        <div class="text-inverse-white font-weight-bolder font-size-h5 mb-2 mt-5">Separation - 2021</div>
                        <div class="font-weight-bold text-inverse-white font-size-sm"><?php echo $data['relive']." Members"; ?></div>
                    </div>            <!--end::Body-->
                </a>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Manager vs Employee Ratio - 2021</h3>
                </div>
            </div>
            <input type="hidden" id="kt_mixed_widget_empmgr_chart_vlaue" name="kt_mixed_widget_empmgr_chart_vlaue" value="<?php  echo ($newemp['manger_count']/$newemp['em_count'])*100;  ?>">
            <div id="kt_mixed_widget_empmgr_chart" style="height: 220px"></div>


        </div>
    </div>

    <div class="col-xl-4">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Employees by Gender - 2021</h3>
                </div>
            </div>
            <!--begin::Body-->
            <div  class="card-body d-flex align-items-center py-0 mt-8">
                <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                    <a href="#" class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary">Male </a>
                    <span class="font-weight-bold text-muted font-size-lg"><?php echo $newemp['male']; ?></span>
                </div>
                <img src="assets/media/svg/avatars/004-boy-1.svg" alt="" class="align-self-end h-100px" />
            </div>
            <!--end::Body-->
            <!--begin::Body-->
            <div  class="card-body d-flex align-items-center py-0 mt-8">
                <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                    <a href="#" class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary">Female </a>
                    <span class="font-weight-bold text-muted font-size-lg"><?php echo $newemp['female']; ?></span>
                </div>
                <img src="assets/media/svg/avatars/014-girl-7.svg" alt="" class="align-self-end h-100px" />
            </div>
            <!--end::Body-->
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Absenteeism Rate -2021</h3>
                </div>
            </div>
            <input type="hidden" name="kt_mixed_widget_absent_chart_value" id="kt_mixed_widget_absent_chart_value" value="<?php echo 100-$data['averge_absent']; ?>">

            <div id="kt_mixed_widget_absent_chart" style="height: 220px"></div>


        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-10">

    </div>

</div>
<script src="//www.amcharts.com/lib/3/amcharts.js" integrity="sha384-UEqG4MxxojOqYymI4f4JMtQisP2oLC8Qv2T62fFLQWCY4QrfS+WpVBS+wLHSg0Mb" crossorigin="anonymous"></script>
<script src="//www.amcharts.com/lib/3/serial.js" integrity="sha384-aviC14tId/1xfGWLH+1T6A3as8A9WRxrwqDVuoCDuGdJuCjhWaFHusuRTUdVFgow" crossorigin="anonymous"></script>
<script src="//www.amcharts.com/lib/3/radar.js" integrity="sha384-Y3yMwKuU3oPSVKO9cG/F5e2aVsw7opYamS0YHWNoMrN1jBxSi2Zb+ggMaLDenPkj" crossorigin="anonymous"></script>
<script src="//www.amcharts.com/lib/3/pie.js" integrity="sha384-NWaQSI2feygYxKH+TJAPBAoi3cCLPvRDpWP46IiF4T0yYWUknoEQ7UAqysstuCSX" crossorigin="anonymous"></script>
<script src="//www.amcharts.com/lib/3/plugins/tools/polarScatter/polarScatter.min.js" integrity="sha384-hFr2FfRf9/0o+SFEEtcW+O9onQOF37fE10/A9GKugQf4k5dVfptwiPCcitetOQDM" crossorigin="anonymous"></script>
<script src="//www.amcharts.com/lib/3/plugins/animate/animate.min.js" integrity="sha384-nJsCseQ2aF9nOW1xqEQqFvTtb2E6cYVMTl7A+qFkFv16PZ5sKLMks0ruD15Yzv+I" crossorigin="anonymous"></script>
<script src="//www.amcharts.com/lib/3/plugins/export/export.min.js" integrity="sha384-LS6OTASJLXDszPqY2q2+LWFz2o3TzQk/O7QEUExu+34YGiIiLEuH2d0O+hCjiPBR" crossorigin="anonymous"></script>
<script src="//www.amcharts.com/lib/3/themes/light.js" integrity="sha384-bAy0EqjOA5jCsPNjmqSa8YmefFrUauJx0jKLRdSMBsbVsZOMmIzTmqtnCuwwAsd3" crossorigin="anonymous"></script>
<link href="//www.amcharts.com/lib/3/plugins/export/export.css" rel="stylesheet" type="text/css"  integrity="sha384-D1OneyvTDI0lTuDPwi7QbI86LBUOfjeMPNf2VRxKNEtUOeY6gJnzGIYRS8lnHUo9" crossorigin="anonymous"/>
