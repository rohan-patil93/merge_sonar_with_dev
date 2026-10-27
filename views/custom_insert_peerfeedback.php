<?php
$url = API."?mode=getperformanceemployees&company_id=".$_SESSION['company_id']."&emp_id=".$emp_id;
$response = callAPI($url);
$employees_details = json_decode($response,1);
$appln_users = $employees_details['appln_users'];
$employees = $employees_details['employees'];
$reports_to = $_SESSION['employee']['reporting_manager_id'];
$userid = $_SESSION['userid'];
if($reports_to==""){
   $reports_to = $appln_users[$userid];
}
$review_manager = $employees[$reports_to]['reporting_manager_id'];
$typeofquestion= array('1'=>'MCQ','2'=>'FT');

$url = API."?mode=getpeerfeedbackquestionsandoptions&company_id=".$_SESSION['company_id']."&emp_id=".$emp_id."&assessment_id=".$assessment_id."&peer_feedback_empid=".$peer_feedback_empid;
$response = callAPI($url);
$peerfeedbacktable = json_decode($response,1);
$peer_questions = $peerfeedbacktable['peer_questions'];
$employee_responses = $peerfeedbacktable['peer_answers'];
$updaterow = $peerfeedbacktable['peer_row'];
$employee_submitted = count((array)$employee_responses) == 0 ? '0':'1';

if(isset($_POST['peersubmit'])){
   $questionidlist = $_POST['question_ids'];
   $row_id = $_POST['rowid'];
   foreach($questionidlist as $id){
      $response_option = $_POST['response_option'.$id];
      $option_type = $_POST['option_type'.$id];
      if($option_type=='MCQ'){
         $response_option = explode('||', $response_option);
            $ans = json_encode($response_option,1);
      }else{
         $ans = json_encode($response_option,1);
      }
      $update_answer[] = array($id=>$response_option);
   }
      $postdata['answers'] = json_encode($update_answer,1);
      $postdata['updated_date'] = date('Y-m-d');
      $postdata['updated_by'] = $_SESSION['userid'];
      $url = API."?mode=updaterowtabledata&rid=".$row_id."&table=".TBL_EMP_PERFORMANCE_PEER_FEEDBACK."&method=post";
      $response = file_post_contents($url, $postdata, 'update');
}
?>
<!-- custom html start here -->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
   <div class="card-body alert alert-white" >
      <div class="col-6">
                                    <div class="d-flex flex-column font-weight-bold">
                                       <a href="#" class="text-dark text-hover-primary mb-1 font-size-lg">Peers Assessment</a>
                                    </div>


                                 <?php  if($employee_submitted==1) {

                                  foreach($peer_questions as $emp_que) {

                                    ?>
                                    <div class="form-group mt-10 mb-2">
                                       <label class="col-12 font-weight-bold" for=""><?php echo $emp_que['assessment_question'];?></label>
                                       <?php if($emp_que['options_display_type']== $typeofquestion[1] ) {
                                          for($k=0;$k<$emp_que['number_of_options'];$k++){
                                             ?>
                                             <button type="button" class="btn ml-7 mt-3 <?php echo $employee_responses[$emp_que['id']]['selected_option_index']== $k ? "btn-success":"btn-outline-success"; ?>" data-toggle="tooltip" data-title="<?php echo json_decode($emp_que['question_option'],1)[$k]; ?>"><?php echo $k+1; ?></button>
                                          <?php } ?>
                                          <br>
                                          <span class="text-success ml-4"><?php echo $employee_responses[$emp_que['id']]['selected_option_value']; ?> </span>
                                       <?php } elseif($emp_que['options_display_type']== $typeofquestion[2] ) { ?>
                                        <span class="ml-4"><?php echo $employee_responses[$emp_que['id']]['selected_option_value']; ?></span>
                                     <?php } ?>
                                  </div>
                               <?php } ?>
                               <?php } else { ?>


                                 <form method="post" action="">
                                    <input type="hidden" name="reports_to" value="<?php echo $reports_to;?>">
                                    <input type="hidden" name="review_manager" value="<?php echo $review_manager;?>">
                                    <input aria-label="Rowid" type="text" name="rowid" value="<?php echo $updaterow;?>">
                                    <input type="hidden" name="assessment_id" value="<?php echo $selected_assessment_id;?>">
                                    <input type="hidden" name="response_row" value="<?php echo $employee_responses['response_row']; ?>">

                                    <?php
                                    $value_id = 0;

                                    foreach($peer_questions as $emp_que) {
                                       $value_id++;
                                       $optionlist = json_decode($emp_que['question_option'],1);
                                       ?>
                                       <div class="form-group mt-5">
                                          <label class="col-12 font-weight-bold" for=""><?php echo $emp_que['assessment_question'];?></label>

                                          <input type="hidden" name="question_ids[]" value="<?php echo $emp_que['id'];?>">
                                          <input type="hidden" name="option_type<?php echo $emp_que['id'];?>" value="<?php echo $emp_que['options_display_type']; ?>">

                                          <?php if($emp_que['options_display_type']== $typeofquestion[1] ) {
                                           $response_answered_option = json_decode($employee_responses[$emp_que['id']]['response_option'],1)[1];
                                           ?>
                                           <select aria-label="Select option" name="response_option<?php echo $emp_que['id'];?>" class="form-control" id="exampleSelect1">
                                              <?php foreach($optionlist as $opkey=>$opts) { $optiondata = $opkey."||".$opts; ?>
                                              <option value="<?php echo $optiondata; ?>" <?php if($opts==$response_answered_option){ echo "selected";
} ?> ><?php echo $opts;?></option>
                                           <?php } ?>
                                        </select>
                                     <?php } elseif($emp_que['options_display_type']== $typeofquestion[2] ) {
                                       $response_answered_option = json_decode($employee_responses[$emp_que['id']]['response_option'],1)[0];
                                       ?>
                                       <textarea aria-label="Text area" name="response_option<?php echo $emp_que['id'];?>" class="form-control col-md-12"> <?php echo $response_answered_option; ?></textarea>
                                    <?php } ?>
                                 </div>

                              <?php } ?>

                              <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="peersubmit">Submit</button>

                           </form>
                        <?php } ?>
   </div>
   </div>

</div>

<!-- custom html end here -->

