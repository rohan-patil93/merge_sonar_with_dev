<?php
$category = array('1' =>'Survey', '2'=> 'Poll' );
$display_tag = $category[$_GET['CAT']];
$table_list = array(TBL_SURVEY_SETUP,TBL_SURVEY_SETUP_QUESTIONS,TBL_SURVEY_SETUP_QUESTION_OPTIONS);
$vurl = API."?mode=getsurveysummary&company_id=".$company_id."&is_active=0&category=".$_GET['CAT'];
$configesponse = callAPI($vurl);
$surveyData = json_decode($configesponse,1);
$survey_details = $surveyData['summary'];
$survey_data = $surveyData['survey_details'];
$survey_qtypes = implode('|',(array)$surveyData['survey_qtypes']);
$survey_qtype_names = implode('|',array_keys((array)$surveyData['survey_qtypes']));
?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                    <div class="card card-custom " id="kt_page_sticky_card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">&nbsp;<?php echo $display_tag; ?> Overview  </h3>
                            </div>
                            <div class="card-toolbar">
                                <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                                    <i class="ki ki-long-arrow-back icon-xs"></i>
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
                                                <?php foreach($survey_details as $survey) { ?>
                                                    <button type="button" class="btn circle-circle-xl border-<?php echo $survey['bgcolor']; ?> ml-4 "><div class="text-center font-size-h2"><?php echo $survey['data']; ?></div> <div class="text-center font-size-h5"><?php echo $survey['name'];?></div></button>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="card-body d-flex flex-column">
                                                <div class="d-flex align-items-center justify-content-between flex-grow-1">
                                                    <div class="mr-2">
                                                        <h3 class="font-weight-bolder"><?php echo $display_tag; ?> Completion Rate</h3>
                                                    </div>
                                                </div>
                                                <div class="pt-8">
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <div class="text-muted font-weight-bold mr-2">Average</div>
                                                        <div class="text-muted font-weight-bold"><?php echo $surveyData['rate']; ?></div>
                                                    </div>
                                                    <div class="progress bg-light-warning progress-xs">
                                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-lg-12">
                                <div class="form-group col-lg-8" >
                                    <table class="table table-separate table-head-custom table-checkable bordered" id="kt_datatable1">

                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col" style="width: 20%;">Name </th>
                                                    <th scope="col">Start Date </th>
                                                    <th scope="col">End Date </th>
                                                    <th scope="col">Questions </th>
                                                    <th scope="col">Responses </th>
                                                    <th scope="col">Completion Rate </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $serialno = 0;
                                                foreach ($survey_data as $empkey => $empvalue) {
                                                    $survey_id = deocdeId("id:".$empvalue['survey_id']);

                                                    $serialno++;     ?>
                                                    <tr>
                                                        <td style="width: 3%;"> <?php echo $serialno; ?> </td>
                                                        <td style="width: 40%;"><a href="index.php?option=COM_VIEW_SURVEY_SUMMARY&Rid=<?php echo $survey_id;?>&CAT=<?php echo $_GET['CAT'];?>"> <?php echo $empvalue['survey_name']; ?> </a></td>
                                                        <td style="width: 15%;"> <?php echo  $empvalue['survey_start_date']; ?> </td>
                                                        <td style="width: 15%;"> <?php echo  $empvalue['survey_end_date']; ?> </td>
                                                        <td class="text-right" style="width: 14%;"> <?php echo $empvalue['totalquestion']; ?> </td>
                                                        <td class="text-right" style="width: 14%;"> <?php echo $empvalue['totalresponse']; ?> </td>
                                                        <td class="text-right" style="width: 14%;">
                                                            <button type="button" class="btn btn-<?php echo $empvalue['completionratecolor']; ?>"><?php echo $empvalue['completionrate']; ?></button></td>
                                                        </tr>
                                                    <?php } ?>

                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="form-group col-lg-4" >
                                            <input type="hidden" id="survey_qtypes" value="<?php echo $survey_qtypes; ?>">
                                            <input type="hidden" id="survey_qtype_names" value="<?php echo $survey_qtype_names; ?>">
                                            <div id="survey_overview_chart" class="d-flex justify-content-center"></div>
                                            <h3 class="font-weight-bolder text-center"> Question Types</h3>

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

