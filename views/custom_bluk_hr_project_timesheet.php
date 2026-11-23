<?php
defined('COLOR_PRIMARY_DEFAULT') || define('COLOR_PRIMARY_DEFAULT', COLOR_PRIMARY_DEFAULT);
    if(!isset($_GET['PAY_MONTH']) || $_GET['PAY_MONTH']==''){ $_GET['PAY_MONTH'] = date('m');
    }
    if(!isset($_GET['PAY_YEAR']) || $_GET['PAY_YEAR']==''){ $_GET['PAY_YEAR'] = date('Y');
    }
    $emp_id = isset($_SESSION['emp_id']) ? $_SESSION['emp_id'] : '';
    $approval_url = API."?mode=toGetblukHrProjectTimesheet&month=".$_GET['PAY_MONTH']."&year=".$_GET['PAY_YEAR']."&emp_id=".$emp_id;
    $app_data = callAPI($approval_url);
    $app_data = json_decode($app_data,1);
    $lastapproval_url = API."?mode=toGetblukHrProjectTimesheetlastmonths&month=".$_GET['PAY_MONTH']."&year=".$_GET['PAY_YEAR']."&emp_id=".$emp_id;
    $last_data = callAPI($lastapproval_url);
    $last_data = json_decode($last_data,1);
    $apv = array();
    $sba = array();
    $employees = array();
    $vendors = array();
    $billables_employee = array();
    $dismonth = array();
    $disuitlize = array();
    $lmonth = array(
        'billable_amount' => 0,
        'budgeted_amount' => 0,
        'vendor_ids' => 0,
        'employee_ids' => 0,
        'period' => date('Y-m-d')
    );
    $fid = 0;
    $apv_billable_amount = 0;
    $sba_billable_amount = 0;
    $total_budget_amount = 0;
    $total_billabe_hrs = 0;
    $total_budget_hrs = 0;
    $total_leave_days = 0;

    if (is_array($app_data)) {
        foreach($app_data as $apps){
            if(isset($apps['pt_status_id']) && $apps['pt_status_id']=="199"){
                $sba[]=$apps;
                $fid = $apps['id'] ?? 0;
                $sba_billable_amount = $sba_billable_amount+($apps['billable_amount'] ?? 0);
            }
            if(isset($apps['pt_status_id']) && $apps['pt_status_id']=="120"){
                $apv[]=$apps;
                $apv_billable_amount = $apv_billable_amount+($apps['billable_amount'] ?? 0);
            }
            $total_leave_days = $total_leave_days+($apps['leave_days'] ?? 0);
            $total_budget_amount = $total_budget_amount+($apps['budgeted_amount'] ?? 0);
            $total_billabe_hrs = $total_billabe_hrs+($apps['billable_hrs'] ?? 0);
            $total_budget_hrs = $total_budget_hrs+176;
            if(isset($apps['employee_id'])){
                $employees[$apps['employee_id']] = $apps['employee_id'];
            }
            if(isset($apps['vendor_id'])){
                $vendors[$apps['vendor_id']] = $apps['vendor_id'];
                if($apps['vendor_id'] !="0" && isset($apps['employee_id'])){
                    $billables_employee[$apps['employee_id']]= $apps['employee_id'];
                }
            }
        }
    }
    $billables_count = count((array)$billables_employee);
    $employee_count = count((array)$employees);
    $sba_count = count((array)$sba);
    $apv_count = count((array)$apv);
    $table = "project_timesheets";
    $stausresult = ViewStatusData();
    $k=1; $ltotal=0;
    if (is_array($last_data)) {
        foreach($last_data as $key=>$last){
            $dismonth[] = date('M-Y',strtotime($key));
            $la_billable_amount = 0;
            $la_budgeted_amount = 0;
            $la_employee = 0;
            $la_vendor = 0;
            if (is_array($last)) {
                foreach($last as $lastt){
                    $la_billable_amount = $la_billable_amount+($lastt['billable_amount'] ?? 0);
                    $la_budgeted_amount = $la_budgeted_amount+($lastt['budgeted_amount'] ?? 0);
                    $la_vendor = $lastt['vendor_ids'] ?? 0;
                    $la_employee = $la_employee+($lastt['employee_ids'] ?? 0);
                }
            }
            if($k==1){
                $lmonth['billable_amount'] = $la_billable_amount;
                $lmonth['budgeted_amount'] = $la_budgeted_amount;
                $lmonth['vendor_ids'] = $la_vendor ;
                $lmonth['employee_ids'] = $la_employee;
                $lmonth['period']=$key;
            }
            $disuitlize[] = $la_billable_amount!="" ? number_format($la_billable_amount/100000,2):'0' ;
            $ltotal =$ltotal+$la_budgeted_amount;
            $k++;
        }
    }
    krsort($dismonth);
    krsort($disuitlize);
    $ttcount =  $apv_billable_amount+$sba_billable_amount;
?>
<script type="text/javascript">
    function statuspopup(status,id){
        var favorite = [];
        $.each($("input[name='checkbox1']:checked"), function(){
            favorite.push($(this).val());
        });
        alert(favorite);
        alert(<?php echo json_encode(__('My favourite sports are').': ', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?> + favorite.join(", "));

      //alert("data");
     var table_name = 'project_timesheets';
      jQuery.ajax({
            url: "views/custom_insert_status_popup.php",
            data:'table='+table_name+'&status='+status+'&rid='+favorite,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal({ show: true});
            },
            error:function (){}
        });
    }
    function displaystausdrop(id,table,status_id){
        //alert("id");
        $.ajax({
            url: "ajax.php",
            data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#dropdownlist_"+id).html(data);
            },
            error:function (){}
        });
    }
    /*$(document).ready(function(){
        $('#select_all').on('click',function(){

            if(this.checked){
                $('.checkbox1').each(function(){
                    this.checked = true;
                });
            }else{
                 $('.checkbox1').each(function(){
                    this.checked = false;
                });
            }
        });
    });*/
    function tocheckallboxes(){
        console.log("Data");
        var val = $('#val_select_all').val();
        console.log(val);
        if(val==0){
            $('#val_select_all').val('1');
             $('.checkbox1').each(function(){
                this.checked = true;
            });
         }else{
             $('#val_select_all').val('0');
            $('.checkbox1').each(function(){
                this.checked = false;
            });
        }
        tocheckdrodown();
    }
    function tocheckdrodown(){
        console.log("Data");
        var len=0;
        $.each($("input[name='checkbox1']:checked"), function(){
            len=len+1;
        });
        if(len>0){
            $("#status_dropdown").show();

        }else{
            $("#status_dropdown").hide();
        }
    }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-lg-12 mb-4" >
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="row">
                            <div class="row col-lg-12" style="padding:0px !important;">
                                <div class="col-lg-10">
                                    <div class=" mb-5">
                                    <?php
                                    $month = strtotime(date('Y').'-'.date('m').'-'.date('j').' - 11 months');
                                      $end = strtotime(date('Y').'-'.date('m').'-'.date('j').' + 0 months');
                                      while($month <= $end){
                                        $disss = date('M Y',$month);
                                        $dis_year = date('Y',$month);
                                        $dis_month = date('m',$month);
                                        if(isset($_GET['PAY_YEAR']) && isset($_GET['PAY_MONTH']) && $_GET['PAY_YEAR'] == $dis_year && $_GET['PAY_MONTH'] == $dis_month ){
                                             $class = 'btn btn-primary';
                                         }else{
                                            //$class = 'text-dark text-hover-primary'; btn-light
                                            $class = 'btn btn-light';
                                        }
                                        $month = strtotime("+1 month", $month);
                                    ?>
                                    <div class="float-left mr-1 ">
                                       <a class="<?php echo $class; ?>" href="index.php?option=COM_BLUK_HR_PROJECT_TIMESHEET&PAY_YEAR=<?php echo $dis_year; ?>&PAY_MONTH=<?php echo $dis_month; ?>"> <?php echo $disss; ?></a>
                                    </div>
                                    <?php  } ?>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="d-flex align-items-center flex-lg-fill mb-2 float-left">
                                        <div class="btn-group" id="status_dropdown" style="display:none;">
                                             <button class="btn btn-primary dropdown-toggle" style="color: <?php echo "#".($stausresult['10']['color'] ?? 'ffffff'); ?>;    background:  <?php echo "#".strval($stausresult['10']['background_color'] ?? COLOR_PRIMARY_DEFAULT); ?>; border-color: <?php echo "#".strval($stausresult['10']['background_color'] ?? COLOR_PRIMARY_DEFAULT); ?>;" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $stausresult['10']['name'] ?? 'Approved'; ?> </button>
                                            <div class="dropdown-menu" id="dropdownlist_<?php echo $fid; ?>">
                                                <button type="button" class="dropdown-item" onclick="statuspopup('199','2')" name="status" id="status" value="199"><?= __("Approved") ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                </div> -->
                <div class="col-lg-2">
                    <div class="card card-custom gutter-b" style="height: 130px">
                        <!--begin::Body-->
                        <div class="card-body d-flex flex-column">
                            <!--begin::Stats-->
                            <div class="flex-grow-1">
                                <div class="text-dark-50 font-weight-bold"><?= __('Utilization'); ?></div>
                                <div class="font-weight-bolder font-size-h3"><?php echo customeummarmonyformate($ttcount/100000); ?> L</div>
                            </div>
                            <!--end::Stats-->
                            <!--begin::Progress-->
                            <?php $total_p = $total_budget_amount > 0 ? number_format((($ttcount/$total_budget_amount)*100),2) : '0.00'; ?>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-muted font-weight-bold mr-2"><?= __('Utilized'); ?></div>
                                <div class="text-muted font-weight-bold"><?php echo $total_p; ?>%</div>
                            </div>
                            <div class="progress progress-xs">
                                <progress class="progress-bar bg-primary" value="<?php echo $total_p; ?>;" aria-valuenow="<?php echo $total_p; ?>" max="100" style="width: <?php echo $total_p; ?>%;" aria-valuenow="<?php echo $total_p; ?>;"></progress>
                            </div>
                            <!--end::Progress-->
                        </div>
                        <!--end::Body-->
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="card card-custom gutter-b" style="height: 130px">
                        <!--begin::Body-->
                        <div class="card-body d-flex flex-column">
                            <!--begin::Stats-->
                            <div class="flex-grow-1">
                                <div class="text-dark-50 font-weight-bold"><?= __('Efforts'); ?></div>
                                <div class="font-weight-bolder font-size-h3"><?php echo $total_billabe_hrs; ?> H</div>
                            </div>
                            <!--end::Stats-->
                            <!--begin::Progress-->
                            <?php $total_hors_p = number_format((($total_billabe_hrs/$total_budget_hrs)*100),2); ?>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-muted font-weight-bold mr-2"><?= __('Efforts'); ?></div>
                                <div class="text-muted font-weight-bold"><?php echo $total_hors_p; ?>%</div>
                            </div>
                            <div class="progress progress-xs">
                                <progress class="progress-bar bg-primary" value="<?php echo $total_hors_p; ?>;" aria-valuenow="<?php echo $total_hors_p; ?>" max="100" style="width: <?php echo $total_hors_p; ?>%;" aria-valuenow="<?php echo $total_hors_p; ?>;"></progress>
                            </div>
                            <!--end::Progress-->
                        </div>
                        <!--end::Body-->
                    </div>
                </div>

                <div class="col-lg-2">
                    <div class="card card-custom gutter-b" style="height: 130px">
                        <!--begin::Body-->
                        <div class="card-body d-flex flex-column">
                            <!--begin::Stats-->
                            <div class="flex-grow-1">
                                <div class="text-dark-50 font-weight-bold"><?= __('Current Vs Previous'); ?></div>
                                <div class="font-weight-bolder font-size-h4"> <?php echo customeummarmonyformate($ttcount/100000); ?> <?= __('L Vs'); ?> <?php echo customeummarmonyformate(($lmonth['billable_amount']+$lmonth['nonbillable_amount'])/100000); ?> L</div>
                            </div>
                            <!--end::Stats-->
                            <!--begin::Progress-->
                            <?php $total_vs_p = number_format((($ttcount/($lmonth['billable_amount']+$lmonth['nonbillable_amount']) )*100),2); ?>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-muted font-weight-bold mr-2"><?= __('Utilized'); ?></div>
                                <div class="text-muted font-weight-bold"><?php echo $total_vs_p; ?>%</div>
                            </div>
                            <div class="progress progress-xs">
                                <progress class="progress-bar bg-primary" value="<?php echo $total_vs_p; ?>;" aria-valuenow="<?php echo $total_vs_p; ?>" max="100" style="width: <?php echo $total_vs_p; ?>%;" aria-valuenow="<?php echo $total_vs_p; ?>;"></progress>
                            </div>
                            <!--end::Progress-->
                        </div>
                        <!--end::Body-->
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="card card-custom gutter-b" style="height: 130px">
                        <!--begin::Body-->
                        <div class="card-body d-flex flex-column">
                            <!--begin::Stats-->
                            <div class="flex-grow-1">
                                <div class="text-dark-50 font-weight-bold"><?= __('Approved'); ?></div>
                                <div class="font-weight-bolder font-size-h3"><?php echo customeummarmonyformate($apv_billable_amount/100000); ?> L</div>
                            </div>
                            <!--end::Stats-->
                            <!--begin::Progress-->
                            <?php $total_apv_p = number_format((($apv_billable_amount/$ttcount)*100),2); ?>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-muted font-weight-bold mr-2"><?= __('Approved'); ?></div>
                                <div class="text-muted font-weight-bold"><?php echo $total_apv_p; ?>%</div>
                            </div>
                            <div class="progress progress-xs">
                                <progress class="progress-bar bg-primary" value="<?php echo $total_apv_p; ?>;" aria-valuenow="<?php echo $total_apv_p; ?>" max="100" style="width: <?php echo $total_apv_p; ?>%;" aria-valuenow="<?php echo $total_apv_p; ?>;"></progress>
                            </div>
                            <!--end::Progress-->
                        </div>
                        <!--end::Body-->
                    </div>
                </div>
                <div class="col-lg-4" style="padding:0px !important;">
                    <div class="col-lg-4 float-left">
                        <div class="card card-custom gutter-b" style="height: 130px">
                            <div class="card-body">
                                <span class="svg-icon svg-icon-3x svg-icon-success">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Group.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24" />
                                            <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                            <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                                <div class="text-dark font-weight-bolder font-size-h4 mt-3"><?php echo count((array)$vendors); ?></div>
                                <a href="#" class="text-muted text-hover-primary font-weight-bold font-size-lg mt-1"> <?= __('Vendors'); ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 float-left">
                        <div class="card card-custom gutter-b" style="height: 130px">
                            <div class="card-body">
                                <span class="svg-icon svg-icon-3x svg-icon-success">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Group.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24" />
                                            <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                            <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                                <div class="text-dark font-weight-bolder font-size-h4 mt-3"><?php echo count((array)$employees); ?> </div>
                                <a href="#" class="text-muted text-hover-primary font-weight-bold font-size-lg mt-1"> <?= __('Mangers'); ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 float-left">
                        <div class="card card-custom gutter-b" style="height: 130px">
                            <div class="card-body">
                                <span class="svg-icon svg-icon-3x svg-icon-success">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Group.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M14,13.381038 L14,3.47213595 L7.99460483,15.4829263 L14,13.381038 Z M4.88230018,17.2353996 L13.2844582,0.431083506 C13.4820496,0.0359007077 13.9625881,-0.12427877 14.3577709,0.0733126292 C14.5125928,0.15072359 14.6381308,0.276261584 14.7155418,0.431083506 L23.1176998,17.2353996 C23.3152912,17.6305824 23.1551117,18.1111209 22.7599289,18.3087123 C22.5664522,18.4054506 22.3420471,18.4197165 22.1378777,18.3482572 L14,15.5 L5.86212227,18.3482572 C5.44509941,18.4942152 4.98871325,18.2744737 4.84275525,17.8574509 C4.77129597,17.6532815 4.78556182,17.4288764 4.88230018,17.2353996 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.000087, 9.191034) rotate(-315.000000) translate(-14.000087, -9.191034) "/>
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                                <div class="text-dark font-weight-bolder font-size-h4 mt-3"><?php echo "10/6"; ?> </div>
                                <a href="#" class="text-muted text-hover-primary font-weight-bold font-size-lg mt-1"> <?= __('Leaves'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--begin::Row-->
        <div class="row">
            <div class="col-lg-8">
                <!--begin::Advance Table Widget 2-->
                <div class="card card-custom card-stretch gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 pt-5"> <h3
                    class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder
                    text-dark"><?= __("Billing Info Details") ?></span> <span
                    class="text-muted mt-3 font-weight-bold
                    font-size-sm"><?= __("Billable :") ?> <?php echo $billables_count; ?> <?= __("Members, Nonbillable :") ?> <?php echo $employee_count-$billables_count; ?> <?= __("Members") ?></span> </h3>
                    <div class="card-toolbar">
                     <ul class="nav nav-pills nav-pills-sm nav-dark-75">
                    <li class="nav-item"> <a class="nav-link py-2 px-4"
                    data-toggle="tab" href="#kt_tab_pane_11_2"><?= __("Approved") ?> <span class="label label-lg font-size-sm label-white ml-2"><?php echo $apv_count; ?></span></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link py-2
                    px-4 active" data-toggle="tab"
                    href="#kt_tab_pane_11_3"><?= __("Approvals Pending") ?> <span class="label label-lg font-size-sm label-white ml-2"><?php echo $sba_count; ?></span></a> </li> </ul> </div> </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body pt-2 pb-0 mt-n3">
                        <div class="tab-content mt-5" id="myTabTables11">
                            <!--begin::Tap pane-->
                            <div class="tab-pane fade" id="kt_tab_pane_11_2" role="tabpanel" aria-labelledby="kt_tab_pane_11_2">
                                <!--begin::Table-->
                                <div class="table-responsive">
                                    <table class="table table-borderless table-vertical-center">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="p-0 w-40px"> </th>
                                                <th scope="col" class="p-0 min-w-200px"><?= __("Employee Details") ?></th>
                                                <th scope="col" class="p-0 min-w-100px text-right"><?= __("Billable Amount") ?></th>
                                                <th scope="col" class="p-0 min-w-125px text-right"><?= __("Billable Hours") ?></th>
                                                <th scope="col" class="p-0 min-w-110px text-center"><?= __("Status") ?></th>
                                                <th scope="col" class="py-0 min-w-50px text-center">
                                                    <div class="checkbox-inline">
                                                        <label for="select_all_approved" class="checkbox checkbox-success" aria-label="<?= __("Select all approved timesheets") ?>">
                                                        <input type="checkbox" checked id="select_all_approved" disabled class="" aria-label="<?= __("Select all approved timesheets") ?>">
                                                        <span></span><span class="sr-only"><?= __("Select all approved timesheets") ?></span></label>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $k=3; foreach($apv as $sb ){
                                                $sb_bp = (!empty($sb['budgeted_amount']) && $sb['budgeted_amount'] > 0) ? number_format((($sb['billable_amount']/$sb['budgeted_amount'])*100),2) : '0.00';
                                                $bbg_color= lableandbgcolor($sb_bp);
                                                $llablecolor = lableandleave($k);
                                                ?>
                                            <tr>
                                                <td class="pl-0 py-4">
                                                    <?php if($sb['emp_image']!=""){ ?>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-40">
                                                                    <img src="uploads/all/<?php echo $sb['emp_image']; ?>" class="h-50 align-self-center" alt="" />
                                                            </div>
                                                        </div>
                                                    <?php }else{ ?>
                                                        <div class="symbol symbol-40 mr-3 bg-success"> <span class="symbol-label font-size-h5"><?php echo initials($sb['reporting_manger']); ?></span> </div>
                                                    <?php } ?>
                                                </td>
                                                <td class="pl-0">
                                                    <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg"><?php echo $sb['reporting_manger']; ?></a>

                                                    <div>

                                                        <a class="text-muted font-weight-bold text-hover-primary" href="#"><?php echo $sb['job_title']; ?></a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column w-100 mr-2">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo $sb_bp; ?>%</span>
                                                            <span class="text-muted font-size-sm font-weight-bold"><?php echo customeummarmonyformate($sb['billable_amount']); ?></span>
                                                        </div>
                                                        <div class="progress progress-xs w-100">
                                                            <progress class="progress-bar <?php echo "bg-".$bbg_color; ?>" value="<?php echo $sb_bp; ?>" max="100" style="width: <?php echo $sb_bp; ?>%;"></progress>
                                                        </div>
                                                    </div>
                                                </td> -->
                                                <td class="text-right">
                                                    <span class="text-muted font-weight-500"><?php echo $sb['billable_hrs']; ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="label label-lg label-light-warning label-inline"><?php echo $sb['current_status']; ?></span>
                                                </td>
                                                <td class="text-center pr-0">
                                                    <div class="checkbox-inline">
                                                        <?php $fallback_approved_name = !empty($sb['employee_name']) ? $sb['employee_name'] : ('Employee ' . $sb['employee_id']); $approved_emp_name = !empty($sb['reporting_manger']) ? $sb['reporting_manger'] : $fallback_approved_name; ?>
                                                        <label for="approved_emp_<?php echo $sb['employee_id']; ?>" class="checkbox checkbox-success" aria-label="Select <?php echo htmlspecialchars($approved_emp_name, ENT_QUOTES); ?>">
                                                        <input type="checkbox" id="approved_emp_<?php echo $sb['employee_id']; ?>" class="" checked name="" disabled value="<?php echo $sb['employee_id']; ?>" aria-label="Select <?php echo htmlspecialchars($approved_emp_name, ENT_QUOTES); ?>">
                                                        <span></span><span class="sr-only"><?= __('Select'); ?> <?php echo htmlspecialchars($approved_emp_name, ENT_QUOTES); ?></span></label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $k= $k-1;
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!--end::Table-->
                            </div>
                            <!--end::Tap pane-->
                            <!--begin::Tap pane-->
                            <div class="tab-pane fade show active" id="kt_tab_pane_11_3" role="tabpanel" aria-labelledby="kt_tab_pane_11_3">
                                <!--begin::Table-->
                                <div class="table-responsive">
                                    <table class="table table-borderless table-vertical-center">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="p-0 w-40px"> </th>
                                                <th scope="col" class="p-0 min-w-200px"><?= __("Employee Details") ?></th>
                                                <th scope="col" class="p-0 min-w-100px text-right"><?= __("Billable Amount") ?></th>
                                                <th scope="col" class="p-0 min-w-125px text-right"><?= __("Billable Hours") ?></th>
                                                <th scope="col" class="p-0 min-w-110px text-center"><?= __("Status") ?></th>
                                                <th scope="col" class="py-0 min-w-50px text-center">
                                                    <div class="checkbox-inline">
                                                        <label for="select_all" class="checkbox checkbox-success" aria-label="<?= __("Select all pending approvals") ?>">
                                                        <input type="checkbox" id="select_all" class="itemRow" onclick="tocheckallboxes()" onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}" aria-label="<?= __("Select all pending approvals") ?>">
                                                        <span></span><span class="sr-only"><?= __("Select all pending approvals") ?></span></label>
                                                        <input type="hidden" name="" id="val_select_all" value="0">
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $k=3; foreach($sba as $sb ){
                                                    $sb_bp = (!empty($sb['budgeted_amount']) && $sb['budgeted_amount'] > 0) ? number_format((($sb['billable_amount']/$sb['budgeted_amount'])*100),2) : '0.00';
                                                    $bbg_color= lableandbgcolor($sb_bp);
                                                        $llablecolor = lableandleave($k);
                                                ?>
                                            <tr>
                                                <td class="pl-0 py-4">
                                                    <?php if($sb['emp_image']!=""){ ?>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-40">
                                                                    <img src="uploads/all/<?php echo $sb['emp_image']; ?>" class="h-50 align-self-center" alt="" />
                                                            </div>
                                                        </div>
                                                    <?php }else{ ?>
                                                        <div class="symbol symbol-40 mr-3 bg-success"> <span class="symbol-label font-size-h5"><?php echo initials($sb['reporting_manger']); ?></span> </div>
                                                    <?php } ?>
                                                </td>
                                                <td class="pl-0">
                                                    <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg"><?php echo $sb['reporting_manger']; ?></a>

                                                    <div>

                                                        <a class="text-muted font-weight-bold text-hover-primary" href="#"><?php echo $sb['job_title']; ?></a>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="d-flex flex-column w-100 mr-2">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo $sb_bp; ?>%</span>
                                                            <span class="text-muted font-size-sm font-weight-bold"><?php echo customeummarmonyformate($sb['billable_amount']); ?></span>
                                                        </div>
                                                        <div class="progress progress-xs w-100">
                                                            <progress class="progress-bar <?php echo "bg-".$bbg_color; ?>" value="<?php echo $sb_bp; ?>" max="100" style="width: <?php echo $sb_bp; ?>%;"></progress>
                                                        </div>
                                                    </div>
                                                </td> -->
                                                <td class="text-right">
                                                    <span class="text-muted font-weight-500"><?php echo $sb['billable_hrs']; ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="label label-lg label-light-warning label-inline"><?php echo $sb['current_status']; ?></span>
                                                </td>
                                                <td class="text-center pr-0">
                                                    <div class="checkbox-inline">
                                                        <?php $fallback_pending_name = !empty($sb['employee_name']) ? $sb['employee_name'] : ('Employee ' . $sb['employee_id']); $pending_emp_name = !empty($sb['reporting_manger']) ? $sb['reporting_manger'] : $fallback_pending_name; ?>
                                                        <label for="pending_emp_<?php echo $sb['employee_id']; ?>" class="checkbox checkbox-success" aria-label="Select <?php echo htmlspecialchars($pending_emp_name, ENT_QUOTES); ?>">
                                                        <input type="checkbox" id="pending_emp_<?php echo $sb['employee_id']; ?>" onclick="tocheckdrodown()" onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}" class="itemRow checkbox1" name="checkbox1" value="<?php echo $sb['employee_id']; ?>" aria-label="Select <?php echo htmlspecialchars($pending_emp_name, ENT_QUOTES); ?>">
                                                        <span></span><span class="sr-only"><?= __('Select'); ?> <?php echo htmlspecialchars($pending_emp_name, ENT_QUOTES); ?></span></label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $k= $k-1;
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!--end::Table-->
                            </div>
                            <!--end::Tap pane-->

                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Advance Table Widget 2-->
            </div>
            <div class="col-lg-4">
                <!--begin::Mixed Widget 14-->
                <div class="card card-custom gutter-b card-stretch">
                    <!--begin::Header-->
                    <div class="card-header border-0 pt-5">
                        <div class="card-title">
                            <div class="card-label">
                                <div class="font-weight-bolder"><?= __('Last 6 Months Utilization'); ?></div>
                                <div class="font-size-sm text-muted mt-2"><?php echo customeummarmonyformate($ltotal); ?></div>
                            </div>
                        </div>

                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body d-flex flex-column px-0">
                        <!--begin::Chart dismonth disuitlize last_data-->
                        <input type="hidden" name="" id="lamonths" value="<?php echo implode(",",(array)$dismonth); ?>">
                        <input type="hidden" name="" id="lauitlize" value="<?php echo implode(",",(array)$disuitlize); ?>">
                        <div id="kt_tiles_widget_1_chart" data-color="info" style="height: 125px"></div>
                        <!--end::Chart-->
                        <!--begin::Items-->
                        <div class="flex-grow-1 card-spacer-x mt-10">
                            <!--begin::Item-->
                            <div class="d-flex align-items-center justify-content-between mb-10">
                                <div class="d-flex align-items-center mr-2">
                                    <div>
                                        <a href="#" class="font-size-h6 text-dark-75 text-hover-primary font-weight-bolder"><?= __('Last Month Budget'); ?></a>
                                        <div class="font-size-sm text-muted font-weight-bold mt-1">
                                            <?php echo date('M-Y',strtotime($lmonth['period'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="label label-light label-inline font-weight-bold text-dark-50 py-4 px-3 font-size-base"><?php echo customeummarmonyformate($lmonth['budgeted_amount']); ?></div>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex align-items-center justify-content-between mb-10">
                                <div class="d-flex align-items-center mr-2">
                                    <div>
                                        <a href="#" class="font-size-h6 text-dark-75 text-hover-primary font-weight-bolder"><?= __("Last Month Approved Budget") ?></a>
                                        <div class="font-size-sm text-muted font-weight-bold mt-1"><?php echo date('M-Y',strtotime($lmonth['period'])); ?></div>
                                    </div>
                                </div>
                                <div class="label label-light label-inline font-weight-bold text-dark-50 py-4 px-3 font-size-base"><?php echo customeummarmonyformate($lmonth['billable_amount']); ?></div>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                                </div> -->
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Items-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Mixed Widget 14-->
            </div>
        </div>
        <!--end::Row-->
        </div> -->

    </div>
</div>
