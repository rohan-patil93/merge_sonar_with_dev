<?php
$category = array('1' =>'Survey', '2'=> 'Poll');
if($_GET['CAT']==""){
    $_GET['CAT']=1;
}
$display_tag = $category[$_GET['CAT']];
$table_list = array(TBL_SURVEY_SETUP,TBL_SURVEY_SETUP_QUESTIONS,TBL_SURVEY_SETUP_QUESTION_OPTIONS);
echo $vurl = API."?mode=getsurveysummary&company_id=".$company_id."&is_active=0&category=".$_GET['CAT'];
$configesponse = callAPI($vurl);
$surveyData = json_decode($configesponse,1);
$survey_details = $surveyData['summary'];
$survey_data = $surveyData['survey_details'];
$survey_qtypes = implode('|',(array)$surveyData['survey_qtypes']);
$survey_qtype_names = implode('|',array_keys((array)$surveyData['survey_qtypes']));

?>
<link rel="stylesheet" href="assets/css/custom_list_survey_setup.css">
<script src="assets/js/custom_list_survey_setup.js"></script>
<div class="card">
    <div class="alert alert-warning alert-dismissible">
        <strong><?php echo "This function is used for maintaining <strong>Display Attributes</strong>. * represents mandatory fields."; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <div class="card-header table-card-header">
        <h5><?php echo $display_tag; ?> Overview</h5>
        <div class="card-header-right table-card-header ">
            <?php if($_GET['CAT'] == '2'){ ?>
                <a href="index.php?option=COM_INSERT_SURVEY_SETUP&CAT=2"  class=" btn btn-success  ">
                    <i class="icofont icofont-plus icon-white "></i>
                Create Poll </a>
            <?php }else{ ?>
                <a href="index.php?option=COM_INSERT_SURVEY_SETUP"  class=" btn btn-success  ">
                    <i class="icofont icofont-plus icon-white "></i>
                Create Survay </a>
            <?php } ?>
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
                        <div class="row">
                            <?php foreach($survey_details as $survey) { ?>
                                <button type="button" class="btn circle-circle-xl border-<?php echo $survey['bgcolor']; ?> ml-2 "><div class="text-center font-size-h3"><?php echo $survey['data']; ?></div> <div class="text-center font-size-h6"><?php echo $survey['name'];?></div></button>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between flex-grow-1">
                                <div class="mr-2">
                                    <h6><?php echo $display_tag; ?> Completion Rate</h6>
                                </div>
                            </div>
                            <div class="pt-8">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="text-muted font-weight-bold mr-2">Average</div>
                                    <div class="text-muted font-weight-bold"><?php echo $surveyData['rate']; ?></div>
                                </div>
                                <div class="progress bg-light-warning progress-xs">
                                    <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
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
                        <th scope="col">Action </th>
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
                            <td class="text-right" style="width: 14%;">
                                <a target="_blank" href="index.php?option=COM_INSERT_SURVEY_RESPONSE&Rid=<?php echo $survey_id;?>&CAT=<?php echo $_GET['CAT'];?>"><button type="button" class="btn btn-primary">Reponse</button></a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="form-group col-lg-12 mt-2" >
                <input type="hidden" id="survey_qtypes" value="<?php echo $survey_qtypes; ?>">
                <input type="hidden" id="survey_qtype_names" value="<?php echo $survey_qtype_names; ?>">

            </div>
        </div>
</div>

