<?php
$category = array('1' =>'Survey', '2'=> 'Poll' );
      $_GET['CAT']=1;

$display_tag = "Exit interview";
$table_list = array(TBL_SURVEY_SETUP,TBL_SURVEY_SETUP_QUESTIONS,TBL_SURVEY_SETUP_QUESTION_OPTIONS);
$vurl = API."?mode=getsurveysummary&company_id=".$company_id."&is_active=0&category=".$_GET['CAT']."&survey_type=".EXITE_INTERVIEW_TYPE;
$configesponse = callAPI($vurl);
$surveyData = json_decode($configesponse,1);
$survey_details = $surveyData['summary'];
$survey_data = $surveyData['survey_details'];
$survey_qtypes = implode('|',(array)$surveyData['survey_qtypes']);
$survey_qtype_names = implode('|',(array)array_keys($surveyData['survey_qtypes']));
?>
<div class="d-flex flex-column-fluid">
   <div class="container-fluid">
        <div class="alert alert-warning alert-dismissible">
            <strong><?php echo "This function is used for maintaining <strong>".__('Display Attributes')."</strong>".__('. * represents mandatory fields.').""; ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
        </div>
        <div class="row">
      <div class="col-lg-12">
         <div class="card">
            <div class="card-header table-card-header">
               <h5><?php echo $display_tag; ?> <?= __('Overview'); ?> </h5>
            <div class="card-header-right table-card-header ">
                <a href="index.php?option=COM_INSERT_EXIT_INTERVIEWS"  class=" btn btn-success  ">
                        <i class="icofont icofont-plus icon-white "></i>
                        <?= __('Create Exit interview'); ?> </a>

                  <button type="button" class="btn waves-effect waves-light btn-inverse btn-outline-inverse"><i class="icofont icofont-reply"></i><?= __('Back'); ?></button>
            </div>
         </div>
                        <div class="card-body">
                            <div class="">
                                    <table class="table table-separate table-head-custom table-checkable bordered" id="kt_datatable1">

                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col" style="width: 20%;"><?= __('Name'); ?> </th>
                                                    <th scope="col"><?= __('Start Date'); ?> </th>
                                                    <th scope="col"><?= __('End Date'); ?> </th>
                                                    <th scope="col"><?= __('Questions'); ?> </th>
                                                    <th scope="col"><?= __('Responses'); ?> </th>
                                                    <th scope="col"><?= __('Completion Rate'); ?> </th>
                                                    <th scope="col"><?= __('Action'); ?></th>
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
                                                        <td style="width: 40%;"><?php echo $empvalue['survey_name']; ?></td>
                                                        <td style="width: 15%;"> <?php echo  $empvalue['survey_start_date']; ?> </td>
                                                        <td style="width: 15%;"> <?php echo  $empvalue['survey_end_date']; ?> </td>
                                                        <td class="text-right" style="width: 14%;"> <?php echo $empvalue['totalquestion']; ?> </td>
                                                        <td class="text-right" style="width: 14%;"> <?php echo $empvalue['totalresponse']; ?> </td>
                                                        <td class="text-right" style="width: 14%;">
                                                            <button type="button" class="btn btn-<?php echo $empvalue['completionratecolor']; ?>"><?php echo $empvalue['completionrate']; ?></button></td>
                                                        <td style="width: 14%;">
                                                            <a target="_blank" href="index.php?option=COM_VIEW_EXIT_INTERVIEW_SUMMARY&Rid=<?php echo $survey_id;?>" ><i class="fa fa-edit"></i></a></td>
                                                        </tr>


                                                    <?php } ?>

                                                </tbody>
                                            </table>
                                        </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

