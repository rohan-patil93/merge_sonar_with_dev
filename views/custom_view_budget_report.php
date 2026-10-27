<?php

$from_cur_year = date('Y');
$from_cur_month = '01';

$to_cur_year = date('Y');
$to_cur_month = '12';

$url = API."?mode=getbudgetlist&table=".$table."&company_id=".$_SESSION['company_id']."&is_active=0";
$response = callAPI($url);
$data = json_decode($response,1);
$financial_start = $data['financial_start'];
$departments = $data['departments'];
$locations = $data['locations'];
$job_titles = $data['job_titles'];

$vurl = API."?mode=getvmsbudgetutilization&company_id=".$_SESSION['company_id']."&filter=";
$hcdetails = callAPI($vurl);
$response = json_decode($hcdetails,1);
$totalbudget = $response['totalbudget'];
$totalutilized = $response['totalutilized'];
$budgetdisplay = $response['budgettable'];

$estimationresponse = $response['estimation'];
foreach($estimationresponse as $keys=>$values){
$monthlist[] = $keys;
$estimationlist[] = $values;
}
$actualresponse = $response['actual'];
foreach($actualresponse as $keys=>$values){
$actuallist[] = $values;
}
if(count((array)$monthlist) != 0 ){
    $monthdata = implode('|', (array)$monthlist);
}
if(count((array)$actuallist) != 0 ){
    $actualdata = implode('|', (array)$actuallist);
}
if(count((array)$estimationlist) != 0 ){
    $estimationdata = implode('|', (array)$estimationlist);
}
if(isset($_POST['applyform'])) {
    $filter_table_rowid = $_POST['filtertbl_rowid'];
     $actuallist = array();
    $estimationlist = array();
    $vurl = API."?mode=customadmdashboardmodule3&company_id=".$_SESSION['company_id']."&filterid=".$filter_table_rowid."&fromdate=".$_POST['from_date']."&todate=".$_POST['to_date'];
   $configesponse = callAPI($vurl);
    $mcats = json_decode($configesponse,1);
    $from_cur_year = date('Y', strtotime($_POST['from_date']));
    $from_cur_month = date('m', strtotime($_POST['from_date']));

    $to_cur_year = date('Y', strtotime($_POST['to_date']));
    $to_cur_month = date('m', strtotime($_POST['to_date']));

     $defalut_fields = $_POST['displayattributes'];
}else{
    $vurl = API."?mode=customadmdashboardmodule3";
    $configesponse = callAPI($vurl);
    $mcats = json_decode($configesponse,1);
}

    $mon_key =0;
    foreach($mcats as $mkey=>$mca){
        $maingra['months'][]=date('M',strtotime($mkey));
        $budget = 0; $bilable =0;
        foreach($mca as $madmin){
            $vendor_admin[$madmin['vendor_id']][$mkey][] = $madmin;
            $cc_admin[$madmin['cc_id']][$mkey][] = $madmin;
            $vertical_admin[$madmin['vertical_id']][] = $madmin;
            $department_admin[$madmin['department_id']][] = $madmin;
            $budget =$budget+$madmin['budgeted_amount'];
            $bilable =$bilable+$madmin['billable_amount'];
        }
        $maingra['bdget'][]=$budget;
        $maingra['billable'][]=$bilable;
        if($mon_key ==0){
            $mon_key = date('m',strtotime($mkey));
        }
    }
    if(count((array)$maingra)<12){
        $maingra['months'] = array();
        for($i=$mon_key;$i<$mon_key+12;$i++){
            $i = $i>12 ? $i-12 :$i;
            $maingra['months'][] = date("M",strtotime('2021-'.$i));
        }
    }
    if(count((array)$maingra['months']) != 0 ){
    $monthdata = implode('|', (array)$maingra['months']);
}
if(count((array)$maingra['billable']) != 0 ){
    $actualdata = implode('|', (array)$maingra['billable']);
}
if(count((array)$maingra['bdget']) != 0 ){
    $estimationdata = implode('|', (array)$maingra['bdget']);
}
$employees_fieldlist = array('employee_name'=>'Employee Name','month'=>'Month','vendor_name'=>'Vendor','vertical_name'=>'Vertical','cost_center_name'=>'Cost Center','department_name'=>'Department','billable_hrs'=>'Billable Hours','nonbillable_hrs'=>'Nonbillable Hours','leave_days_from_to'=>'Leave','budgeted_amount'=>'Budgeted Amount','billable_amount'=>'Billable Amount','nonbillable_amount'=>'Nonbillable Amount','exp'=>'Experiences','job_title'=>'Job Title');
if(count((array)$defalut_fields)<1){
    $defalut_fields = array('employee_name','vertical_name','vendor_name','cost_center_name','job_title','budgeted_amount','billable_amount');
}
    $defalut_sub_fields = array('month','vertical_name','vendor_name','cost_center_name','job_title','budgeted_amount','billable_amount');

foreach($mcats as $mm=>$mtatss){
    foreach ($mtatss as $budgetkey => $mtats) {
    if(isset($maindata[$mtats['employee_id']])){
        $maindata[$mtats['employee_id']]['budgeted_amount']= $maindata[$mtats['employee_id']]['budgeted_amount']+$mtats['budgeted_amount'];
        $maindata[$mtats['employee_id']]['billable_amount']= $maindata[$mtats['employee_id']]['billable_amount']+$mtats['billable_amount'];
    }else{
        $maindata[$mtats['employee_id']] = $mtats;
    }
    $mtats['month'] = $mm;
    $maindata[$mtats['employee_id']]['data'][] = $mtats;
    }
}

?>
<script type="text/javascript">
    function showpopup(){
        var filter_id = document.getElementById('filtertbl_rowid').value;
        jQuery.ajax({
            url: "views/custom_filter_vms_budgetreport_popup.php",
            data:'filter_id='+filter_id,
            type: "GET",
            success:function(data){
                $('#dynamic_popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal({ show: true});
            },
            error:function (){}
        }).done(function(){
        });
    }

</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
  <style>
    tr{}
    td, th{border-bottom: 1px solid #EBEDF3; padding: 10px;}
    table.data-table tr th{color: #B5B5C3; font-weight: 500; font-size: 14px; letter-spacing: 1px;}
    table.data-table .custom-check{position: relative;}
    table.data-table .custom-check input[type="checkbox"]{width: 17px; height: 17px;}
    table.data-table .dropdown .btn{padding: 0px 8px 2px 8px;}

    span.online-text{width: 8px; height: 8px; display: inline-block; background: #dc3545; border-radius: 50%;}
    span.direct-text{width: 8px; height: 8px; display: inline-block; background: #007bff; border-radius: 50%;}
    table.data-table .dropdown-toggle:after{display: none;}
    table.data-table a.edit{display: inline-block; vertical-align: middle;}
 </style>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">

            <form class="kt-form" id="kt_form" method="post" action="" >
                <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>" >
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-custom " id="kt_page_sticky_card">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label col-lg-3">&nbsp; Budget Utilization  </h3>
                                      From<div class="col-lg-3">
                                            <input aria-label="From Date" class="form-control" type="month" name="from_date" value="<?php echo $from_cur_year."-".$from_cur_month?>"  id="att_month_year" />
                                    </div>

                                      To<div class="col-lg-3">
                                          <input aria-label="To Date" class="form-control" type="month" name="to_date" value="<?php echo $to_cur_year."-".$to_cur_month?>"  id="att_month_year_2" />
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-outline-secondary" onclick="showpopup();"><i class="flaticon2-hourglass-1"></i>Filter</button>
                                    </div>

                                    <div class="col-lg-2">
                                        <input type="hidden" id="filterby" name="filterby">
                                        <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"  name="applyform" value="apply">Apply</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body row">
                                <div class="col-lg-10">

                                    <input type="hidden" id="monthdata" value="<?php echo $monthdata;?>">
                                    <input type="hidden" id="estimationdata" value="<?php echo $estimationdata;?>">
                                    <input type="hidden" id="actualdata" value="<?php echo $actualdata;?>">

                                    <div class="card-body">

                                        <div id="kt_amcharts_vms_dashbaord_overall" style="height: 500px;"></div>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <h3 class="card-label">&nbsp;  Budget Vs Utilized </h3>

                                    <div class="d-flex mt-10">
                                        <span class="font-weight-bold mr-4">Total Budget </span>
                                        <div class="progress progress-xs mt-2 mb-2 flex-shrink-0 w-30px w-xl-70px">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 63%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="font-weight-bolder text-dark ml-4"><?php echo number_format($totalbudget,2);?></span>
                                    </div>
                                    <div class="d-flex mt-5">
                                        <span class="font-weight-bold mr-4">Total Utilized</span>
                                        <div class="progress progress-xs mt-2 mb-2 flex-shrink-0 w-30px w-xl-70px">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 63%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="font-weight-bolder text-dark ml-4"><?php echo number_format($totalutilized,2);?></span>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </form>

         <div class="card card-custom col-lg-12">
             <div class="card-body">

            <table class="table table-separate table-head-custom table-checkable data-table" id="kt_datatable1_2">
                <tr>
                    <th scope="col" style="width: 3%;"></th>
                    <th scope="col" style="width: 5%;">SL.NO.</th>
                    <?php foreach($employees_fieldlist as $fkey=>$fieldlist){
                        if(in_array($fkey,(array)$defalut_fields)){ ?>
                        <th scope="col"><?php echo $fieldlist; ?> </th>
                    <?php } ?>
                    <?php } ?>
                </tr>

                <?php $k=1; foreach ($maindata as  $budget) { ?>
                <tr class="main">
                    <td>
                        <a href="javascript:void(0)" class="main">
                            <svg xmlns="http:
                              <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                            </svg>
                        </a>
                    </td>

                    <td><?php echo $k; ?></td>
                    <?php foreach($employees_fieldlist as $fkey=>$fieldlist){
                        if(in_array($fkey,(array)$defalut_fields)){ ?>
                        <td   <?php if(is_numeric($budget[$fkey])){ ?> class="text-right" <?php
} ?> >
 <?php if(!is_numeric($budget[$fkey])){ echo $budget[$fkey];
}else{ echo customeummarmonyformate($budget[$fkey]);
}  ?> </td>
                    <?php } ?>
                    <?php } ?>
                </tr>
                <tr class="data">
                <td colspan="9">
                    <table class="mb-3" style="width: 100%">
                        <tr>
                            <th scope="col" style="width: 3%;"></th>
                            <th scope="col" style="width: 5%;">SL.NO</th>
                             <?php foreach($employees_fieldlist as $fkey=>$fieldlist){
                                if(in_array($fkey,$defalut_sub_fields)){ ?>
                                <th scope="col"><?php echo $fieldlist; ?> </th>
                            <?php } ?>
                            <?php } ?>
                        </tr>
                        <?php $l=1; foreach($budget['data'] as $data){ ?>
                        <tr>
                            <td></td>
                            <td><?php echo $l; ?></td>
                            <?php foreach($employees_fieldlist as $fkey=>$fieldlist){
                                if(in_array($fkey,(array)$defalut_sub_fields)){ ?>
                                <td <?php if(is_numeric($data[$fkey])){ ?> class="text-right" <?php
} ?>>
 <?php if(!is_numeric($data[$fkey])){ echo $data[$fkey];
}else{ echo customeummarmonyformate($data[$fkey]);
}  ?> </td>
                            <?php } ?>
                            <?php } ?>
                        </tr>
                        <?php $l++;
} ?>
                    </table>

                </td>
            </tr>
                <?php $k++;
} ?>
            </table>
        </div>
    </div>
</div>
</div>

 <script type="text/javascript">
    jQuery(document).ready(function() {
    jQuery(".data").hide();
    jQuery(".main").click(function() {
        jQuery(this).parent().parent().next(".data").toggle("fast");
    });
});
</script>

<script src="//www.amcharts.com/lib/3/amcharts.js" integrity="sha384-UEqG4MxxojOqYymI4f4JMtQisP2oLC8Qv2T62fFLQWCY4QrfS+WpVBS+wLHSg0Mb" crossorigin="anonymous"></script>
<script src="//www.amcharts.com/lib/3/serial.js" integrity="sha384-aviC14tId/1xfGWLH+1T6A3as8A9WRxrwqDVuoCDuGdJuCjhWaFHusuRTUdVFgow" crossorigin="anonymous"></script>

<script src="assets/js/pages/features/charts/amcharts/charts.js"></script>
<script src="//www.amcharts.com/lib/3/themes/light.js" integrity="sha384-bAy0EqjOA5jCsPNjmqSa8YmefFrUauJx0jKLRdSMBsbVsZOMmIzTmqtnCuwwAsd3" crossorigin="anonymous"></script>

<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable"  >
        <div class="modal-content"  id="dynamic_popup2">

        </div>
    </div>
</div>

