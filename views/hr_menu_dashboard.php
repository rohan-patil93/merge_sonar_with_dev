<?php

$disview_array['employee']['step1'] = array(
    array('name'=>'MY Information','icon'=>'ti-user','url'=>'COM_LIST_NEW_EMPLOYEE&NI=560'),
    array('name'=>'MY LEAVES','icon'=>'ti-car','url'=>'COM_LIST_NEW_EMPLOYEE&NI=566'),
    array('name'=>'MY Pay','icon'=>'ti-time','url'=>'COM_LIST_NEW_EMPLOYEE&NI=674'),
    array('name'=>'MY TIMESHEETs','icon'=>'ti-time','url'=>'COM_LIST_NEW_EMPLOYEE&NI=564'),
    array('name'=>'MY Tasks','icon'=>'ti-time','url'=>'COM_LIST_MYTASK'),
    array('name'=>'MY Notes','icon'=>'ti-time','url'=>'COM_LIST_EMPLOYEE_NOTES'),
    array('name'=>'recognition','icon'=>'ti-time','url'=>'COM_LIST_NEW_EMPLOYEE&NI=679'),
     array('name'=>'Job Postings','icon'=>'ti-time','url'=>'COM_LIST_JOB_POSTING_CANDIDATES')
    );
$disview_array['employee']['step2'] = array(
    array('name'=>'CREATE TiME SHEET','icon'=>'ti-bag','bg_color'=>'danger','url'=>'COM_INSERT_PROJECT_TIMESHEETS'),
    array('name'=>'Apply  LEAVES','bg_color'=>'blue','icon'=>'ti-bag','url'=>'COM_INSERT_LEAVE_REQUEST'),
    array('name'=>'Self Services','bg_color'=>'green','icon'=>'ti-bag','url'=>'COM_LIST_NEW_EMPLOYEE&NI=673'),
    array('name'=>'performance','bg_color'=>'info','icon'=>'ti-bag','url'=>'COM_INSERT_LEAVE_REQUEST'));

$disview_array['payroll']['step1'] = array(
    array('name'=>'MY Information','icon'=>'ti-user','url'=>'COM_INSERT_BASIC_INFO'),
    array('name'=>'MY LEAVES','icon'=>'ti-car','url'=>'COM_LIST_EMPLOYEE_LEAVES'),
    array('name'=>'MY Pay','icon'=>'ti-time','url'=>'COM_INSERT_PAYSLIP'),
    array('name'=>'MY TIMESHEETs','icon'=>'ti-time','url'=>'COM_INSERT_PROJECT_TIMESHEET_CALENDER'),
    array('name'=>'MY Tasks','icon'=>'ti-time','url'=>'COM_LIST_MYTASK'),
    array('name'=>'MY Notes','icon'=>'ti-time','url'=>'COM_LIST_EMPLOYEE_NOTES'),
    array('name'=>'recognition','icon'=>'ti-time','url'=>'COM_INSERT_EMPLOYEE_RECOGNITION'),
     array('name'=>'Job Postings','icon'=>'ti-time','url'=>'COM_LIST_JOB_POSTING_CANDIDATES')
    );
$disview_array['payroll']['step2'] = array(
    array('name'=>'CREATE TiME SHEET','icon'=>'ti-bag','bg_color'=>'danger','url'=>'COM_INSERT_PROJECT_TIMESHEETS'),
    array('name'=>'Apply  LEAVES','bg_color'=>'blue','icon'=>'ti-bag','url'=>'COM_INSERT_LEAVE_REQUEST'),
    array('name'=>'Self Services','bg_color'=>'green','icon'=>'ti-bag','url'=>'CUSTOM_LIST_ESS'),
    array('name'=>'performance','bg_color'=>'info','icon'=>'ti-bag','url'=>'COM_INSERT_LEAVE_REQUEST'));

$disview_array['payroll']['step2'][] = array('name'=>'Salary setting','bg_color'=>'inverse','icon'=>'ti-bag','url'=>'COM_INSERT_LEAVE_REQUEST');
   $disview_array['payroll']['step2'][] =  array('name'=>'Generate Pyroll','bg_color'=>'warning','icon'=>'ti-bag','url'=>'COM_INSERT_LEAVE_REQUEST');

$disview_array['admin']['step1'] = array(
    array('name'=>'MY Information','icon'=>'ti-user','url'=>'COM_INSERT_BASIC_INFO'),
    array('name'=>'MY LEAVES','icon'=>'ti-car','url'=>'COM_LIST_EMPLOYEE_LEAVES'),
    array('name'=>'MY Pay','icon'=>'ti-time','url'=>'COM_INSERT_PAYSLIP'),
    array('name'=>'MY TIMESHEETs','icon'=>'ti-time','url'=>'COM_INSERT_PROJECT_TIMESHEET_CALENDER'),
    array('name'=>'MY Tasks','icon'=>'ti-time','url'=>'COM_LIST_MYTASK'),
    array('name'=>'MY Notes','icon'=>'ti-time','url'=>'COM_LIST_EMPLOYEE_NOTES'),
    array('name'=>'recognition','icon'=>'ti-time','url'=>'COM_INSERT_EMPLOYEE_RECOGNITION'),
     array('name'=>'Job Postings','icon'=>'ti-time','url'=>'COM_LIST_JOB_POSTING_CANDIDATES')
    );
$disview_array['admin']['step2'] = array(
    array('name'=>'CREATE TiME SHEET','icon'=>'ti-bag','bg_color'=>'danger','url'=>'COM_INSERT_PROJECT_TIMESHEETS'),
    array('name'=>'Apply  LEAVES','bg_color'=>'blue','icon'=>'ti-bag','url'=>'COM_INSERT_LEAVE_REQUEST'),
    array('name'=>'Self Services','bg_color'=>'green','icon'=>'ti-bag','url'=>'CUSTOM_LIST_ESS'),
    array('name'=>'performance','bg_color'=>'info','icon'=>'ti-bag','url'=>'COM_INSERT_LEAVE_REQUEST'));

   $disview_array['admin']['step2'][] = array('name'=>'recruitment ','bg_color'=>'inverse','icon'=>'ti-bag','url'=>'COM_INSERT_LEAVE_REQUEST');
   $disview_array['admin']['step2'][] =  array('name'=>'On Boarding','bg_color'=>'warning','icon'=>'ti-bag','url'=>'COM_INSERT_LEAVE_REQUEST');

$disview_admin['admin'] = array(
    array('name'=>'claims','icon'=>'ti-bag','bg_color'=>'green','url'=>''),
    array('name'=>'Skills','bg_color'=>'blue','icon'=>'ti-bag','url'=>'COM_LIST_EMP_SKILL'),
    array('name'=>' Survey','icon'=>'ti-bag','bg_color'=>'warning','url'=>'COM_LIST_EMP_SURVEY'),
    array('name'=>'Polls','bg_color'=>'info','icon'=>'ti-bag','url'=>'COM_LIST_EMP_SURVEY&CAT=2'),
 array('name'=>'Hr Srevices','bg_color'=>'inverse','icon'=>'ti-bag','url'=>'COM_LIST_HR_SERVICES'),
 array('name'=>'Bulk Actions','bg_color'=>'info','icon'=>'ti-bag','url'=>'COM_LIST_INBOX'),
array('name'=>'Tranings','bg_color'=>'danger','icon'=>'ti-bag','url'=>'COM_LIST_APPLICATION_MENU_SETTINGS'),
array('name'=>'announcements','bg_color'=>'blue','icon'=>'ti-bag','url'=>'COM_VIEW_ANNOUNCEMENTS'),
array('name'=>'Settings','bg_color'=>'danger','icon'=>'ti-bag','url'=>'COM_LIST_APPLICATION_MENU_SETTINGS'));
$key_dis ="employee";
?>
<script type="text/javascript">
  function dynamicinsertpopup(option){
    jQuery.ajax({
            url: "views/new_from.php",
            data:'option='+option,
            type: "POST",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal("show");
            },
            error:function (){}
        });
  }
   function dynamiclistpopup(option){
    alert("insert"+option);
    jQuery.ajax({
            url: "views/custom_insert_view_pop.php",
            data:'option='+option+'&popup=Y',
            type: "GET",
            success:function(data){
                $('#dynamic_popupxxl').html(data);
                $('#kt_modal_dynamic_popupxxl').modal("show");
            },
            error:function (){}
        });
  }
  </script>
<div class="pcoded-content">
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
                <div class="page-body">

                        <div class="col-lg-12">


                                <div class="row  col-lg-12">

                                    <div class="col-lg-3">
                                        <div class="card card-info"  style="overflow: visible;">
                                            <div class="card-header">
                                                <h5 class="text-white">KEY TRANSACTIONS</h5>
                                            </div>
                                            <div class="card-block">
                                                <?php foreach($disview_array[$key_dis]['step1'] as $step1){ ?>
                                                <div class="row m-b-10 align-items-center">
                                                    <div class="col-auto p-r-0">
                                                        <i class="<?php echo $step1['icon']; ?> text-white"></i>
                                                    </div>
                                                    <div class="col mt-2">
                                                        <a href="#" onclick="dynamiclistpopup(<?php echo "'".$step1['url']."'"; ?>)"><h6 class="m-b-5 text-white">
                                                                <?php echo ucwords(strtolower($step1['name'])); ?>
                                                            </h6></a>
                                                    </div>
                                                </div>
                                                <?php } ?>

                                            </div>
                                            <div class="card-footer">
                                                <a href="index.php?option=COM_LIST_NEW_EMPLOYEE&NI=555">
                                                <button class="btn waves-effect waves-light btn-grd-info">GO TO DASHBOARD</button></a>
                                                <img class="mw-100 mx-auto mb-lg-n18" src="assets\images\12.png" style="margin-bottom: -46px!important; height: 220px !important;z-index: 999999;" alt="Preview">
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="row">
                                            <?php foreach($disview_array[$key_dis]['step2'] as $step2){ ?>
                                            <div class="col-lg-6">
                                                <div class="card social-card card-<?php echo $step2['bg_color']; ?>" style="margin-bottom: 20px;">
                                                    <div class="card-body text-center">
                                                        <div class="text-facebook m-b-20">
                                                            <img class="img-100" src="assets\images\4.png" alt="Preview">
                                                        </div>
                                                        <button type="button" class="p-0 border-0 bg-transparent text-reset cursor-pointer" onclick="dynamiclistpopup(<?php echo "'".$step2['url']."'"; ?>)">
                                                                <h3 class="text-facebook f-16 text-white f-w-700">
                                                                    <?php echo ucwords(strtolower($step2['name'])); ?>
                                                                </h3>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                          <?php } ?>

                                            <div class="col-lg-12">
                                                <div class="card analytic-card card-danger ">
                                                    <div class="card-body">
                                                        <div class="row align-items-center m-b-10">
                                                            <div class="col-auto">
                                                                <img class="" src="assets\images\11.png" style="height: 200px!important" alt="Preview">
                                                            </div>
                                                            <div class="col">
                                                                <h3 class="f-18 text-white f-w-800">QUICK GUIDE</h3>
                                                                <h6 class="f-14 text-white m-t-20">Explore our powerful
                                                                documentation</h6>
                                                                <button class="btn waves-effect waves-light btn-grd-danger mt-4 btn-skew">LEARN MORE</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card latest-update-card">
                                                    <div class="card-header">
                                                        <h5 class="">Updates</h5>
                                                        <div class="card-header-right">
                                                            <button class="btn dropdown-toggle waves-effect waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
                                                                <a class="dropdown-item waves-light waves-effect" href="#">Action</a>
                                                                <a class="dropdown-item waves-light waves-effect" href="#">Another action</a>
                                                                <a class="dropdown-item waves-light waves-effect" href="#">Something else</a>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item waves-light waves-effect" href="#">Something else</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-block mt-4" style="height: 380px;">
                                                        <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 510px;">
                                                            <div class="scroll-widget" style="overflow: hidden; width: auto; height: 510px;">
                                                                <div class="latest-update-box">
                                                                    <?php $aci = 0; foreach ($notificationlist as  $value) {
                                                                        $option_url = $value['option'];
                                                                        if($value['option']==""|| $value['option']==null ){
                                                                            $tabledata = toGetTableNameBaseApplicationPagesList('application_table_name',$value['Notification_Table'],1);
                                                                            $option_url = $tabledata['insert_page_name'];
                                                                        }
                                                                        $nrid = "id:".$value['option_id'];
                                                                        $link = "index.php?option=".$option_url."&Rid=". deocdeId($nrid)."&noti_id=".deocdeId($value['id']);
                                                                     ?>
                                                                    <div class="row p-t-20 p-b-30">
                                                                        <div class="col-auto text-end update-meta p-r-0">
                                                                            <span class="img-radius img-40 align-top m-r-15 update-icon text-dark f-w-700"><?php echo initials(str_replace('_', ' ',$value['Notification_Table'])); ?>RT</span>

                                                                        </div>
                                                                        <div class="col p-l-5">
                                                                            <a href="#!"><h6><?php echo $value['Notification_Message']; ?></h6></a>
                                                                            <p class="text-muted m-b-0"><?php echo notificationdatesdiff($value['Notification_DateTime']); ?></p>
                                                                        </div>
                                                                    </div>
                                                                    <?php } ?>

                                                                </div>

                                                            </div>
                                                            <div class="slimScrollBar" style="background: rgb(0, 0, 0); width: 5px; position: absolute; top: 104px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 186.475px;">
                                                            </div>
                                                            <div class="slimScrollRail" style="width: 5px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;">
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php foreach($disview_admin['admin'] as $step2){ ?>
                                            <div class="col-lg-3">
                                                <div class="card social-card card-<?php echo $step2['bg_color']; ?>" style="margin-bottom: 20px;">
                                                    <div class="card-body text-center">
                                                         <div class="text-facebook m-b-20">
                                                             <img class="img-50" src="assets\images\4.png" alt="Preview">
                                                         </div>
                                                        <button type="button" class="p-0 border-0 bg-transparent text-reset cursor-pointer" onclick="dynamiclistpopup(<?php echo "'".$step2['url']."'"; ?>)">
                                                        <h3 class="text-facebook f-14 text-white f-w-700">
                                                            <?php echo ucwords(strtolower($step2['name'])); ?>
                                                        </h3>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                          <?php } ?>
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
