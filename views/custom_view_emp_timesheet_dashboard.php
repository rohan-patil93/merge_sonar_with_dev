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

     $vurl = API."?mode=gettimesheetsummarydashboard&company_id=".$_SESSION['company_id']."&is_active=0&emp_id=".$empp_id."&fromdate=".date('Y-m-d',$startingdate)."&todate=".date('Y-m-d',$endingdate);
    $configesponse = callAPI($vurl);
    $requestData = json_decode($configesponse,1);
    $total_requests = $requestData['summary'];
    $summarydetails = $requestData['summarydetails'];


}

$currentyear = $_GET['PAY_YEAR']!=""?$_GET['PAY_YEAR']:date('Y');
$currentmonth = $_GET['PAY_MONTH']!=""?$_GET['PAY_MONTH']:date('m');
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

<div class="d-flex flex-column-fluid">
    <div class="container-fluid j-contain">
        <div class="row j-pro">
            <div class="col-lg-12">
                <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                    <div class="card card-custom " id="kt_page_sticky_card">
                        <div class="card-header table-card-header mb-2">
                            <h5><?php echo "Employee Timesheet Summary";  ?></h5>
                            <div class="card-header-right table-card-header ">
                                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                    <i class="icofont icofont-reply"></i>
                                    Back
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class=" card-custom gutter-b card-stretch">
                                <div class=" d-flex flex-column">
                                    <div class="row col-lg-12">
                                        <div class="col-lg-9">
                                            <div class="row col-lg-12">

                                                <?php
                                                $colorindex = 0;
                                                if(count((array)$total_requests)>0) {

                                                    foreach($total_requests as $rkey=>$request) {
                                                        ?>
                                                        <button type="button" class="btn circle-circle-xl m-l-10  border-<?php echo $bgcolorlist[$colorindex]; ?>  "><div class="text-center font-size-h3"><?php echo $request==''?'0':$request; ?></div> <div class="text-center font-size-h6"><?php echo $summary_data[$rkey];?></div></button>
                                                        <?php $colorindex++;
}
                                                    } ?>    </div>
                                                </div>
                                                <div class="col-lg-3">

                                                    <form method="post" action="#" >
                                                        <input type="hidden" name="emp_id" value="<?php echo $empp_id; ?>">
                                                        <label for="example-date-input-to" class="col-3 col-form-label">Month</label>
                                                        <div class="input-group input-group-button">
                                                            <input aria-label="Fromdate" type="month" name="fromdate" value="<?php echo $currentyear."-".$currentmonth;?>" id="example-date-input-from_2"  class="form-control form-control-sm width1" style="font-size: 14px;height: 40px !important;padding: 10px 15px;">  <button type="submit"  name="datefilter" value="exit" class="btn btn-info waves-effect waves-light input-group-text mt-0">
                                                                Get Details
                                                            </button> </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">

                                            <table class="table table-separate table-head-custom table-checkable bordered dataTable px-6" id="kt_datatable1">

                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">Employee  </th>
                                                            <th scope="col">Email id  </th>
                                                            <th scope="col">Timesheet Hrs </th>

                                                            <th scope="col">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $sno= 0;

                                            foreach($summarydetails as $loc) { $sno++;


                                                ?>


                                                <tr>
                                                    <td> <?php echo $sno; ?> </td>
                                                    <td>

                                                        <div class="align-middle">
                                                            <?php if($loc['image']!=""){
                                                                $loc_img_src = !empty($loc['image']) ? 'uploads/all/' . $loc['image'] : 'uploads/all/no-image.jpg'; ?>
                                                                <img src="<?php echo $loc_img_src; ?>" class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                                            <?php } else { ?>
                                                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($loc['display_name']); ?></span>
                                                            <?php } ?>
                                                            <div class="d-inline-block width73">
                                                                <h6><?php echo $loc['display_name']; ?></h6>
                                                                <span class="form-label label label-primary"><?php echo $loc['emp_code']; ?></span>
                                                            </div>
                                                        </div>

                                                    </td>
                                                    <td> <?php echo $loc['email_id']; ?></td>
                                                    <td>
                                                        <span class="label label-xl font-weight-bolder label-rounded label-success w-50px"  data-toggle="tooltip" title="Total Billable Hours" style="vertical-align: middle;">
                                                            <?php if($_SESSION['employee']['billable'] == '0'){ echo $loc['billable_hrs'];
}else{ echo $loc['nonbillable_hrs'];
} ?>
                                                        </span>
                                                    </td>
                                                    <td> <button type="button" onclick="getEmployeeDetails('<?php echo $currentyear; ?>','<?php echo $currentmonth; ?>','<?php echo $loc['id']; ?>')"> <span class="btn btn-primary "  data-toggle="tooltip" title="View Details" style="vertical-align: middle;">  View </span></button>  </td>
                                                </tr>

                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

