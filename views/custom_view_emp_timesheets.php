<?php
if($_POST['popup'] == "Y"){
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
    $_GET['EMP'] = $_POST['EMP'];
    $_GET['PAY_YEAR'] = $_POST['PAY_YEAR'];
    $_GET['PAY_MONTH'] = $_POST['PAY_MONTH'];
}
$bgcolorlist = array('0'=>'success','1'=>'warning','2'=>'primary','3'=>'info','4'=>'dark','5'=>'secondary');
$stausresult = ViewStatusData();
$stausprocessresult = gettableStatusArray('project_timesheets');
$stausprocessresult = $stausprocessresult['full'];

if(isset($_POST['datefilter'])){
    $dateexplo = explode('-', $_POST['fromdate']);
    header("Location: index.php?option=COM_VIEW_EMP_TIMESHEETS&PAY_YEAR=".$dateexplo[0]."&PAY_MONTH=".$dateexplo[1]."&EMP=".$_POST['emp_id']);


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
    $summary_data = array('Yearly','Quaterly','Monthly','Non-Billable');
    $selectedMonday = date("Y-m-d", strtotime('monday this week', strtotime($current_from_date)));
    $data11["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0','emp_id'=>$empp_id,'timesheet_date'=>$selectedMonday);
    $datw = urlencode(json_encode($data11));
    $curl1 = API."?mode=gettabledetailswithconditions&table=project_timesheets&data=".$datw;
    $cudata1 = callAPI($curl1);
    $result = json_decode($cudata1,1);
    foreach ($result as $key => $row){
        $rowid = "id:".$row['id'];
    }
    $rid =  urlencode($rowid);
    $rid =  deocdeId($rid);

    $vurl = API."?mode=gettimesheetsummary&company_id=".$_SESSION['company_id']."&is_active=0&emp_id=".$empp_id."&fromdate=".date('Y-m-d',$startingdate)."&todate=".date('Y-m-d',$endingdate);
    $configesponse = callAPI($vurl);
    $requestData = json_decode($configesponse,1);
    $total_requests = $requestData['summary'];
    $summarydetails = $requestData['summarydetails'];
    $tasklist = $requestData['tasks'];
    $taskhrs = implode('|', (array)$tasklist);
    $tasknames = implode('|', array_keys((array)$tasklist));
}

$currentyear = $_GET['PAY_YEAR']!=""?$_GET['PAY_YEAR']:date('Y');
$currentmonth = $_GET['PAY_MONTH']!=""?$_GET['PAY_MONTH']:date('m');
?>
<?php if($_POST['popup'] == "Y"){ ?>
<div class="modal-header">
    <h4 class="modal-title">Employee Timesheet Summary</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<?php } ?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid j-contain">
        <div class="row j-pro border-0">
            <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                <div class="card card-custom" id="kt_page_sticky_card">
                    <?php if($_POST['popup'] != "Y"){ ?>
                    <div class="card-header table-card-header mb-2">
                        <h5><?php echo "Employee Timesheet Summary";  ?></h5>
                        <div class="card-header-right table-card-header ">
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="card-body">
                        <div class="card-custom gutter-b card-stretch">
                            <div class="d-flex flex-column">
                                <div class="row">
                                    <div class="col-lg-9 col-md-12 col-sm-12 col-12">
                                        <?php
                                        $colorindex = 0;
                                        if(count((array)$total_requests)>0) {

                                        foreach($total_requests as $rkey=>$request) {
                                            ?>
                                            <button type="button" class="btn circle-circle-xl m-l-10 mb-3  border-<?php echo $bgcolorlist[$colorindex]; ?>  ">
                                                <div class="text-center font-size-h3"><?php echo $request==''?'0':$request; ?></div>
                                                <div class="text-center font-size-h6"><?php echo $summary_data[$rkey];?></div>
                                            </button>
                                            <?php $colorindex++;
}
                                        } ?>
                                    </div>
                                    <div class="col-lg-3 col-md-12 col-sm-12 col-12">

                                        <?php if($_POST['popup'] != "Y"){ ?>
                                        <form method="post" action="#" >
                                            <input type="hidden" name="emp_id" value="<?php echo $empp_id; ?>">
                                            <label for="example-date-input-to" class="col-3 col-form-label">Month</label>
                                            <div class="input-group input-group-button">
                                                <input aria-label="Fromdate" type="month" name="fromdate" value="<?php echo $currentyear."-".$currentmonth;?>" id="example-date-input-from_2"  class="form-control form-control-sm width1" style="font-size: 14px;height: 40px !important;padding: 10px 15px;">
                                                <button type="submit"  name="datefilter" value="exit" class="btn btn-info waves-effect waves-light input-group-text mt-0 p-2">
                                                    Get Details
                                                </button>
                                            </div>
                                        </form>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-separate table-head-custom table-checkable bordered dataTable px-6" id="kt_datatable1">

                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Timesheet From Date </th>
                                        <th scope="col">Timesheet To Date </th>

                                        <th scope="col">Billable Hrs </th>
                                        <?php if($_SESSION['employee']['billable'] == '1'  && TIMESHEET_BILLABLE_CHECk == 'Y'){ ?>
                                            <th scope="col">Non Billable Hrs </th>
                                        <?php } ?>
                                        <th scope="col">Status </th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno= 0;

                                    foreach($summarydetails as $loc) {  $sno++;


                                    $weekfromdate = ($startingdate <= strtotime($loc['weekfrom']))?$loc['weekfrom']:date('d-m-Y',$startingdate);
                                    $weektodate = ($endingdate >= strtotime($loc['weekto']))?$loc['weekto']:date('d-m-Y',$endingdate);

                                    ?>


                                    <tr>
                                        <td> <?php echo $sno; ?> </td>
                                        <td>
                                            <?php echo fromDate($weekfromdate); ?>
                                        </td>
                                        <td> <?php echo fromDate($weektodate); ?>
                                        </td>
                                        <td>
                                            <span class="label label-xl font-weight-bolder label-rounded label-success w-50px"  data-toggle="tooltip" title="Total Billable Hours" style="vertical-align: middle;"> <?php echo $loc['billhrs']; ?>
                                            </span>
                                        </td>
                                        <?php if($_SESSION['employee']['billable'] == '1'  && TIMESHEET_BILLABLE_CHECk == 'Y'){ ?>
                                        <td>
                                            <span class="label label-xl font-weight-bolder label-rounded label-success w-50px"  data-toggle="tooltip" title="Total Non-Billable Hours" style="vertical-align: middle;"><?php echo $loc['nonbillhrs']; ?>
                                            </span>
                                        </td>
                                        <?php } ?>
                                        <td>
                                            <?php if($loc['status']!=""){ ?>
                                                <button class="btn btn-primary" style="color: <?php echo "#". $stausresult[$loc['status']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$loc['status']]['background_color'] ); ?>; border-color: <?php echo "#".strval($stausresult[$loc['status']]['background_color']); ?>;" type="button" ><?php echo $stausprocessresult[$loc['status']]['name']; ?>  </button>
                                            <?php }else{ echo "-";
} ?>
                                        </td>
                                        <td>
                                            <a href="index.php?option=COM_INSERT_PROJECT_TIMESHEETS&CHANGE_DATE=<?php echo $weekfromdate; ?>&EMP=<?php echo $empp_id; ?>" target="_blank">
                                                <span class="btn btn-primary btn-sm" data-toggle="tooltip" title="View Details" style="vertical-align: middle;">View</span>
                                            </a>
                                        </td>

                                    </tr>

                                    <?php }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
