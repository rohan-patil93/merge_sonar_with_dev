<?php
$display_id = $_GET['NI']!="" ? $_GET['NI'] : '2';
if($_GET['Rid']!=""){
    $rid =  encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $ex_rid = explode(':', $rid);
    $assessment_id = $ex_rid['1'];
}

$tag1= $_GET['CAT'] ==1 ?'Assigned' : '';
$display_tag = $_GET['CAT'] ==1 ?'Assessment' : '';

$vurl = API."?mode=getassessmentlist&company_id=".$company_id."&is_active=0"."&assessment_id=".$assessment_id."&category=".$_GET['CAT'];
$configesponse = callAPI($vurl);
$assessmentData = json_decode($configesponse,1);
$assessment_details = $assessmentData['overview'];
$employees = $assessmentData['employees'];
$no_of_responds = $assessmentData['no_of_responds'];
$rate = $assessmentData['rate'];
$response = $assessmentData['response'];
$display_questions = $assessmentData['questions'];
$qustion_types = $assessmentData['qustion_types'];
foreach ($qustion_types as $qtype) {
    $questiontypes[$qtype['genc_detail_code']] =  $qtype['genc_detail_name'];
}

$assigntonames = json_decode($assessment_details['assigned_to_employees'],1);

$employee_idlist = urlencode($assigntonames);
$who_can_see_the_results = json_decode($assessment_details['who_can_see_the_results'],1);
?>
<script type="text/javascript">
    function moreemployees() {
        var employee_idlist = document.getElementById("employee_idlist").value;
        jQuery.ajax({
            url: "views/custom_view_employees_popup.php",
            data: 'empids='+employee_idlist,
            type: "GET",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal({ show: true});
            },
            error:function (){}
        });

    }
</script>
<input type="hidden" id="employee_idlist" value="<?php echo $employee_idlist;?>">
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <!--begin::Navbar-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-body pt-9 pb-0">
                <!--begin::Details-->
                <div class="d-flex flex-wrap flex-sm-nowrap mb-3">

                    <div class="flex-shrink-0 mr-7 mt-lg-0 mt-3  mr-10">
                        <div class="symbol symbol-danger symbol-70  symbollogo p-15 text-center">
                            <span class="font-size-h4 f-18"><?php echo initials($assessment_details['assessment_name']); ?></span>
                        </div>
                    </div>
                    <!--begin: Info-->
                    <div class="flex-grow-1">
                        <!--begin::Title-->
                        <div class="d-flex justify-content-between flex-wrap mt-1">
                            <div class="d-flex mr-3">
                                <a href="#" class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3"><?php echo $assessment_details['assessment_name'];?></a>
                                <a href="#">
                                    <i class="flaticon2-correct text-success font-size-h5 mr-2"></i>
                                </a>


                            </div>
                            <div class="my-lg-0 my-3">
                                <div class="d-flex align-items-center flex-lg-fill my-1">
                                    <span class="mr-4">
                                        <i class="flaticon-network icon-2x text-muted font-weight-bold" data-toggle="tooltip" title="<?php echo $tag1;?>"></i>
                                    </span>
                                    <div class="symbol-group symbol-hover"  >
                                        <?php $k=1;
                                        foreach ($assigntonames as $res) {
                                            if($k<6){
                                                ?>
                                                <div class="symbol symbol-35 symbol-circle" data-toggle="tooltip" title="<?php echo $employees[$res['display_name']]; ?>">
                                                    <span class="symbol-label font-weight-bold"><?php echo initials($employees[$res]); ?></span>
                                                </div>
                                                <?php

                                            } $k++;
} ?>
                                            <?php if(count((array)$resdata)>1){ ?>
                                                <div class="symbol symbol-35 symbol-circle symbol-light">
                                                    <button type="button" class="btn p-0 border-0 bg-transparent text-reset cursor-pointer" onclick="moreemployees()"><span class="symbol-label font-weight-bold">5+</span>
                                                    </button></div>
                                                <?php } ?>
                                            </div>

                                                                         <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>

                                        </div>
                                    </div>
                                </div>
                                <!--end::Title-->
                                <!--begin::Content-->
                                <div class="d-flex flex-wrap justify-content-between mt-1">
                                    <div class="d-flex flex-column flex-grow-1 pr-8  col-lg-8">
                                        <div class="d-flex flex-wrap mb-4">
                                            <span class="text-dark-50  font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 ">
                                                <?php echo $assessment_details['description']; ?> </span>
                                            </div>
                                            <div class="d-flex align-items-center mr-10">
                                                <div class="mr-5">
                                                    <div class="font-weight-bold mb-2 mr-5"><?php echo $tag1;?> </div>
                                                    <span class="form-label label label-lg label-primary">
                                                        <?php echo count((array)$assigntonames);?>
                                                    </span>
                                                </div>
                                                <div class="mr-5">
                                                    <div class="font-weight-bold mb-2 mr-5">Responded </div>
                                                    <span class="form-label label label-lg label-primary">
                                                        <?php echo $no_of_responds;?>
                                                    </span>
                                                </div>
                                                <div class="mr-5">
                                                    <div class="font-weight-bold mb-2 mr-5">Started Date</div>
                                                    <span class="form-label label label-lg label-primary">
                                                        <?php echo date('d M, Y',strtotime($assessment_details['assessment_start_date']));?>
                                                    </span>
                                                </div>
                                                <div class="mr-5">
                                                    <div class="font-weight-bold mb-2 mr-5">Expiry Date</div>
                                                    <span class="form-label label label-lg label-danger">
                                                        <?php echo date('d M, Y',strtotime($assessment_details['assessment_end_date']));?>
                                                    </span>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3 col-lg-4">

                                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                                <span class="fw-bold fs-6 text-gray-400">Completion Rate</span>
                                                <span class="fw-bolder fs-6"><?php echo $rate; ?>%</span>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="progress  progress-xs">
                                                    <div class="progress-bar progress-bar-pink " role="progressbar" style="width: <?php echo $rate; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Content-->
                                </div>
                                <!--end::Info-->
                            </div>
                            <!--end::Details-->
                            <!--begin::Navs-->
                            <div class="d-flex overflow-auto mt-2 h-55px">

                                <ul class="nav md-tabs">
                                    <!--begin::Nav item-->
                                    <li class="nav-item">
                                        <a class="nav-link text-active-primary me-6 <?php if($display_id== 2){ echo "active";
} ?>"  href="index.php?option=COM_VIEW_ASSESSMENT_SUMMARY&Rid=<?php echo $_GET['Rid']; ?>&CAT=<?php echo $_GET['CAT']; ?>&NI=<?php echo "2"; ?>">
                                            <?php echo $display_tag." Details"; ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary me-6 <?php if($display_id== 1){ echo "active";
} ?>"  href="index.php?option=COM_VIEW_ASSESSMENT_SUMMARY&Rid=<?php echo $_GET['Rid']; ?>&CAT=<?php echo $_GET['CAT']; ?>&NI=1">
                                                <?php echo $display_tag." Responses"; ?></a>
                                            </li>

                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary me-6 <?php if($display_id== 3){ echo "active";
} ?>"  href="index.php?option=COM_VIEW_ASSESSMENT_SUMMARY&Rid=<?php echo $_GET['Rid']; ?>&CAT=<?php echo $_GET['CAT']; ?>&NI=3">
                                                    <?php echo $display_tag." Report"; ?></a>
                                                </li>

                                            </ul>
                                        </div>
                                        <!--begin::Navs-->
                                    </div>
                                </div>
                                <!--end::Navbar-->


                                <div class="card mb-5 p-5 ">
                                    <?php
                                    if($display_id==1){
                                        include_once "views/custom_view_assessment_response_details.php";

                                    }elseif($display_id==2){
                                        include_once "views/custom_insert_assessment_setup.php";

                                    }
                                    elseif($display_id==3){
                                        include_once "views/custom_view_assessment_report.php";

                                    } ?>
                                </div>
                            </div>
                        </div>

                        <div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable"  >
                                <div class="modal-content"  id="popup2">

                                </div>
                            </div>
                        </div>
