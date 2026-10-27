<?php
$filterdata = "";
$_SESSION['apply_js']['headcount']=1;
$monthlist = array('1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');

$cur_year = date('Y');
$cur_month = date('m');
$vurl = API."?mode=getheadcountreport&table=employees&month=".$cur_month."&year=".$cur_year."&filterby=".$filterdata;
$hcdetails = callAPI($vurl);
$headcount_data = json_decode($hcdetails,1);
$curyear_monthwise_headcount = $headcount_data['curyear_monthwise_headcount'];
$prevyear_monthwise_headcount = $headcount_data['prevyear_monthwise_headcount'];
$Total_Head_Count = $headcount_data['Total_Head_Count'];
$Total_Head_Count_CurrentYear = $headcount_data['Total_Head_Count_CurrentYear'];
$Total_Head_Count_PrevYear = $headcount_data['Total_Head_Count_PrevYear'];

$yearslist = $headcount_data['years'];
$years = implode('|', (array)$yearslist);
$prev_year_monthly_headcount = implode('|', (array)$prevyear_monthwise_headcount);
$cur_year_monthly_headcount =implode('|', (array)$curyear_monthwise_headcount);
$empidlist = $headcount_data['selected_employees'];
$employment_categories = $headcount_data['employment_categories'];
$designations = $headcount_data['designations'];
$departments = $headcount_data['departments'];
$locations = $headcount_data['locations'];
if(isset($_POST['applyform'])) {
    $filtertbl_rowid = $_POST['filtertbl_rowid'];
    $monyr = explode('-',$_POST['month_year_cal']);
    $cur_year = $monyr[0];
    $cur_month = $monyr[1];
    if($_POST['filterby']=='All Employees'){
        $filterdata = "";
    }else{
        $filterby = json_decode($_POST['filterby'],1);
        unset($filterby['matches_count']);
        unset($filterby['matches_names']);
        $filterdata = urlencode(json_encode($filterby));
    }
    $vurl = API."?mode=getheadcountreport&table=employees&month=".$cur_month."&year=".$cur_year."&filterby=".$filterdata;
    $hcdetails = callAPI($vurl);
    $headcount_data = json_decode($hcdetails,1);
    $curyear_monthwise_headcount = $headcount_data['curyear_monthwise_headcount'];
    $prevyear_monthwise_headcount = $headcount_data['prevyear_monthwise_headcount'];
    $Total_Head_Count = $headcount_data['Total_Head_Count'];
    $Total_Head_Count_CurrentYear = $headcount_data['Total_Head_Count_CurrentYear'];
    $Total_Head_Count_PrevYear = $headcount_data['Total_Head_Count_PrevYear'];
    $yearslist = $headcount_data['years'];
    $years = implode('|', (array)$yearslist);
    $prev_year_monthly_headcount = implode('|', (array)$prevyear_monthwise_headcount);
    $cur_year_monthly_headcount =implode('|', (array)$curyear_monthwise_headcount);
    $empidlist = $headcount_data['selected_employees'];
    $employment_categories = $headcount_data['employment_categories'];
    $designations = $headcount_data['designations'];
    $departments = $headcount_data['departments'];
    $locations = $headcount_data['locations'];

}
?>
<script type="text/javascript">
    function showpopup(){
        var filter_id = document.getElementById('filtertbl_rowid').value;
        jQuery.ajax({
            url: "views/custom_filter_headcountreport.php",
            data:'filter_id='+filter_id,
            type: "GET",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal({ show: true});
            },
            error:function (){}
        }).done(function(){
        });
    }

    function getnamelist(emplist){
        console.log(emplist);
        document.getElementById('filterby').value = emplist;
        $('#kt_modal_dynamic_popup').modal('hide');
    }

</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <form class="kt-form" id="kt_form" method="post" action="" >
            <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>" >

            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-custom " id="kt_page_sticky_card">
                        <div class="card-header">
                            <div class="card-title col-lg-6">
                                <h3 class="card-label">Head Count</h3>
                                <div class="col-lg-4">
                                    <input aria-label="Month Year Cal" class="form-control" type="month" name="month_year_cal" value="<?php echo $cur_year."-".$cur_month?>"  id="att_month_year" />
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
                                <!--begin::Chart-->
                                <input type="hidden" id="years" value="<?php echo $years;?>">
                                <input type="hidden" id="cur_year_monthly_headcount" value="<?php echo $cur_year_monthly_headcount;?>">
                                <input type="hidden" id="prev_year_monthly_headcount" value="<?php echo $prev_year_monthly_headcount;?>">
                                <div class="card-block">
                                    <canvas id="barChart" width="400" height="400"></canvas>
                                </div>
                                <div id="headcountchart"></div>
                            </div>
                            <!--end::Chart-->
                            <div class="col-lg-2">
                                <h6>&nbsp;  <?php echo $monthlist[(int)$cur_month]. ", ". $cur_year; ?>  </h6>
                                <div class="d-flex flex-grow-1 align-items-center bg-hover-light p-4 rounded">
                                    <div class="mr-4 flex-shrink-0 text-center" style="width: 40px;">
                                        <i class="icon-2x text-dark-50 flaticon-users"></i>
                                    </div>
                                    <h2 class="card-label"> <?php echo $Total_Head_Count; ?> </h2>
                                </div>
                                <h3 class="card-label">&nbsp; Total Employees </h3>

                                <div class="d-flex mt-10">
                                    <span class="font-weight-bold mr-4"><?php echo $yearslist[0];?></span>
                                    <div class="progress progress-xs mt-2 mb-2 flex-shrink-0 w-30px w-xl-70px">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 63%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="font-weight-bolder text-dark ml-4"><?php echo $Total_Head_Count_CurrentYear;?></span>
                                </div>
                                <div class="d-flex mt-5">
                                    <span class="font-weight-bold mr-4"><?php echo $yearslist[1];?></span>
                                    <div class="progress progress-xs mt-2 mb-2 flex-shrink-0 w-30px w-xl-70px">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 63%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="font-weight-bolder text-dark ml-4"><?php echo $Total_Head_Count_PrevYear    ;?></span>
                                </div>
                            </div>
                        </div>
                        <!--end::Charts Widget 2-->
                    </div>

                </div>
            </div>
        </form>
        <div class="card card-custom col-lg-12">
            <div class="card-body">
            <!--begin: Datatable -->
            <table class="table table-separate table-head-custom table-checkable" id="kt_datatable1">

                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name </th>
                            <th scope="col">Hire Date </th>
                            <th scope="col">Designation </th>
                            <th scope="col">Department </th>
                            <th scope="col">Location </th>
                            <th scope="col">Employment Status </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $serialno = 0;
                        foreach ($empidlist as $empkey => $empvalue) {
                            $serialno++;     ?>
                            <tr>
                                <td> <?php echo $serialno; ?> </td>
                                <td> <?php echo $empvalue['display_name']; ?> </td>
                                <td> <?php echo date('d-M-Y',strtotime($empvalue['joining_date'])); ?> </td>
                                <td> <?php echo $designations[$empvalue['designation_id']]; ?> </td>
                                <td> <?php echo $departments[$empvalue['department_id']]; ?> </td>
                                <td> <?php echo $locations[$empvalue['location_id']]; ?> </td>
                                <td> <?php echo $employment_categories[$empvalue['employment_category_id']]; ?> </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/pages/features/charts/apexcharts.js"></script>

