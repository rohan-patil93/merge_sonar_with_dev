<?php
$bgcolorlist = array('0'=>'success','1'=>'warning','2'=>'primary','3'=>'info','4'=>'dark','5'=>'secondary');
$current_from_date = date('Y-m-d');
$current_to_date = date('Y-m-d');

$jobtypes = array('0'=>'Open','2'=>'In-Progress','2'=>'Close','3'=>'Total');

$data12["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
$datw12 = urlencode(json_encode($data12));
$curl12 = API."?mode=gettabledetailswithconditions&table=vendors&data=".$datw12;
$cudata12 = callAPI($curl12);
$vendors = json_decode($cudata12,1);

$vurl = API."?mode=getjobpostingsreport&company_id=".$_SESSION['company_id']."&is_active=0";
$configesponse = callAPI($vurl);
$requestData = json_decode($configesponse,1);
$total_requests = $requestData['summary'];
$job_type_data = $requestData['job_type_data'];
$vendor_jobs = $requestData['vendor_jobs'];

if(isset($_POST['datefilter'])){
    $current_from_date = $_POST['fromdate'];
    $current_to_date = $_POST['todate'];
    $vurl = API."?mode=getjobpostingsreport&company_id=".$_SESSION['company_id']."&is_active=0&fromdate=".$current_from_date."&todate=".$current_to_date;
    $configesponse = callAPI($vurl);
    $requestData = json_decode($configesponse,1);
    $total_requests = $requestData['summary'];
    $job_type_data = $requestData['job_type_data'];
    $vendor_jobs = $requestData['vendor_jobs'];

}

?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                    <div class="card card-custom " id="kt_page_sticky_card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">&nbsp; Job Postings Summary </h3>
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
                                                    $req_types_count_list[] = $request==''?'0':$request;
                                                    $req_types_names_list[] = $job_type_data[$rkey];
                                                    ?>
                                                    <button type="button" class="btn circle-circle-xl border-<?php echo $bgcolorlist[$colorindex]; ?> ml-4 "><div class="text-center font-size-h3"><?php echo $request==''?'0':$request; ?></div> <div class="text-center font-size-h6"><?php echo $job_type_data[$rkey];?></div></button>
                                                    <?php $colorindex++;
}
                                                }else{
                                                        foreach($jobtypes as $rkey=>$rval) {
                                                    $req_types_names_list[] = $rval;
                                                    $req_types_count_list[] = "0";
                                                            ?>
                                                    <button type="button" class="btn circle-circle-xl border-<?php echo $bgcolorlist[$colorindex]; ?> ml-4 "><div class="text-center font-size-h3"><?php echo "0"; ?></div> <div class="text-center font-size-h6"><?php echo $rval;?></div></button>
                                                    <?php $colorindex++;
}
                                                }


                                                ?>

                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                    <form method="post" action="#">
                                                        <div class="form-group row">
                                                            <label for="example-date-input-from" class="col-3 col-form-label">From</label>
                                                            <div class="col-10">
                                                                <input aria-label="Fromdate" class="form-control" type="date" name="fromdate" value="<?php echo $current_from_date;?>" id="example-date-input-from" />
                                                            </div>
                                                            <label for="example-date-input-to" class="col-3 col-form-label">To</label>
                                                            <div class="col-10">
                                                                <input aria-label="Todate" class="form-control" type="date" name="todate" value="<?php echo $current_to_date;?>" id="example-date-input-to" />
                                                            </div>
                                                        </div>

                                                        <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="datefilter" value="exit">Apply</button>
                                                    </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-lg-8 form-group">

                                <table class="table table-separate table-head-custom table-checkable bordered dataTable px-6" id="kt_datatable1">

                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 5%;">#</th>
                                                <th scope="col" style="width: 20%;">Vendor </th>
                                                <?php foreach($job_type_data as $jobtype){ ?>
                                                    <th scope="col" style="width: 15%;"> <?php echo $jobtype; ?></th>
                                                <?php } ?>
                                                <th scope="col" style="width: 15%;">Action </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $sno= 0;
                                            foreach($vendors as $ven) {  $sno++;   ?>
                                                <tr>
                                                    <td> <?php echo $sno; ?> </td>
                                                    <td> <?php echo $ven['display_name']; ?> </td>
                                                    <?php foreach($job_type_data as $jkey =>$jobtype){ $vendorid = $ven['id'];  ?>
                                                    <td>  <span class="font-weight-bolder label label-xl label-success "  data-toggle="tooltip" title="Number of Jobs" style="vertical-align: middle;">  <?php echo $vendor_jobs[$vendorid][$jkey]==''?'0':$vendor_jobs[$vendorid][$jkey]; ?> </span> </td>

                                                <?php } ?>

                                                <td> <a href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATES&VENDOR=<?php echo $vendorid;?>" target="_blank"> <span class="btn btn-primary "  data-toggle="tooltip" title="View Details" style="vertical-align: middle;">  View </span></a> </td>
                                            </tr>

                                        <?php }
                                        ?>
                                    </tbody>
                                </table>
                                    </div>
                                    <?php
                                        $req_types_count = implode('|',(array)$req_types_count_list);
                                        $req_types_names = implode('|',(array)$req_types_names_list);
                                    ?>

                                <div class="form-group col-lg-4" >
                                        <input type="hidden" id="survey_qtypes" value="<?php echo $req_types_count; ?>">
                                        <input type="hidden" id="survey_qtype_names" value="<?php echo $req_types_names; ?>">
                                        <div id="survey_overview_chart" class="d-flex justify-content-center"></div>
                                        <h3 class="font-weight-bolder text-center"> <?php echo "Request Types"; ?></h3>

                                    </div>
                                    </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

