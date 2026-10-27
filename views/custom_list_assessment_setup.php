<?php
$category = array('1' =>'Assessment');
if ($_GET['CAT']=="" && $_GET['option']="COM_LIST_ASSESSMENT_SETUP") {
      $_GET['CAT']=1;
   }

$display_tag = $category[$_GET['CAT']];
$table_list = array(TBL_ASSESSMENT_SETUP,TBL_ASSESSMENT_SETUP_QUESTIONS,TBL_ASSESSMENT_SETUP_QUESTION_OPTIONS);
$vurl = API."?mode=getassessmentsummary&company_id=".$company_id."&is_active=0&category=".$_GET['CAT'];
$configesponse = callAPI($vurl);
$assessmentData = json_decode($configesponse,1);
$assessment_details = $assessmentData['summary'];
$assessment_data = $assessmentData['assessment_details'];
$assessment_qtypes = implode('|',(array)$assessmentData['assessment_qtypes']);
$assessment_qtype_names = implode('|',array_keys((array)$assessmentData['assessment_qtypes']));
?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="alert alert-warning alert-dismissible">
            <strong><?php echo "This function is used for maintaining <strong>Display Attributes</strong>. * represents mandatory fields."; ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>
         <div class="row">
      <div class="col-lg-12">
         <div class="card">
            <div class="card-header table-card-header">
               <h5><?php echo $display_tag; ?> Setup</h5>
         <div class="card-header-right table-card-header">
            <a href="index.php?option=COM_INSERT_ASSESSMENT_SETUP&CAT=<?php echo $_GET['CAT'];?>" class="btn btn-primary btn-shadow-hover font-weight-bold m-r-2"><i class="icofont icofont-plus icon-white "></i><?php echo $display_tag; ?></a>

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
                                            <div class="row">
                                                <?php foreach($assessment_details as $assessment) { ?>
                                                    <button type="button" class="btn circle-circle-xl border-<?php echo $assessment['bgcolor']; ?> ml-2 "><div class="text-center font-size-h3"><?php echo $assessment['data']; ?></div> <div class="text-center font-size-h6"><?php echo $assessment['name'];?></div></button>
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
                                                        <div class="text-muted font-weight-bold"><?php echo $assessmentData['rate']; ?></div>
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
                            <div class="row">
                                <div class="form-group col-lg-8 mt-2">

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
                                                foreach ($assessment_data as $empkey => $empvalue) {
                                                    $assessment_id = deocdeId("id:".$empvalue['assessment_id']);

                                                    $serialno++;     ?>
                                                    <tr>
                                                        <td style="width: 3%;"> <?php echo $serialno; ?> </td>
                                                        <td style="width: 40%;"><a href="index.php?option=COM_VIEW_ASSESSMENT_SUMMARY&Rid=<?php echo $assessment_id;?>&CAT=<?php echo $_GET['CAT'];?>"> <?php echo $empvalue['assessment_name']; ?> </a></td>
                                                        <td style="width: 15%;"> <?php echo  $empvalue['assessment_start_date']; ?> </td>
                                                        <td style="width: 15%;"> <?php echo  $empvalue['assessment_end_date']; ?> </td>
                                                        <td class="text-right" style="width: 14%;"> <?php echo $empvalue['totalquestion']; ?> </td>
                                                        <td class="text-right" style="width: 14%;"> <?php echo $empvalue['totalresponse']; ?> </td>
                                                        <td class="text-right" style="width: 14%;">
                                                            <button type="button" class="btn btn-<?php echo $empvalue['completionratecolor']; ?>"><?php echo $empvalue['completionrate']; ?></button></td>
                                                        </tr>
                                                    <?php } ?>

                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="form-group col-lg-4 mt-2" >
                                            <input type="hidden" id="assessment_qtypes" value="<?php echo $assessment_qtypes; ?>">
                                            <input type="hidden" id="assessment_qtype_names" value="<?php echo $assessment_qtype_names; ?>">
                                            <div id="assessment_overview_chart" class="d-flex justify-content-center"></div>
                                            <h3 class="font-weight-bolder text-center"> <?php echo $display_tag; ?> Types</h3>

                                        </div>
                                        </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

