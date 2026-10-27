<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');

//print_r($_POST);
$table_list = array("job_feedback_setup","job_feedback_questions","job_feedback_question_options");
$company_id = $_SESSION['company_id'];
$vurl = API."?mode=getcandidatefeedbackresult&job_candidate_id=".$_POST['job_candidate_id']."&job_selection_process_step_id=".$_POST['job_selection_process_step_id'];
//echo $vurl;
$configesponse = callAPI($vurl);
$surveyData = json_decode($configesponse,1);
//echo "<pre>"; print_r($surveyData);
$survey_details = $surveyData['survey_details'];
//print_r($survey_details);
//$survey_response_details = $surveyData['survey_reponse_details'];

$answer_data = $surveyData['response'];
foreach($answer_data as $answer_da){
  $answer[$answer_da['question_id']] = $answer_da['response_option'];
}
$display_tag = $category[$survey_details['category']];

$qustion_types = $surveyData['qustion_types'];
$total_q = count((array)$display_questions);
foreach ($qustion_types as $qtype) {
  $questiontypes[$qtype['genc_detail_code']] =  $qtype['genc_detail_name'];
}
?>


<script>
  function formsubmit(){
        //var formValues= $(this).serialize();
        var str = $( "form" ).serialize();
        jQuery.ajax({
          url: "ajax.php",
          data:'page=job_posting_candidate_response&'+str,
          type: "POST",
          success:function(data){
            $('#kt_modal_KTDatatable_remotem').modal('hide');
            swal("<?php echo ucwords($_SESSION['config']['name']); ?>", '<?php echo $_SESSION['error_message']['3001']; ?>', "success");
          },
          error:function (){}
        });
      }
    </script>
    <div class="modal-header">
      <h5 class="modal-title">
        <?php echo "Candidate Feed Back"; ?> 
      </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body1" > 
      <form method="post" id="myform" name="myform" action="">
        <div class="row q-sec m-10 m-l-50 m-r-50">  
           <input type="hidden" name="job_candidate_interview_schedule_id" value="<?php echo $_POST['job_candidate_interview_schedule_id'];?>">
          <input type="hidden" name="job_posing_candidate_id" value="<?php echo $_POST['job_candidate_id'];?>">
          <input type="hidden" name="job_selection_process_step_id" value="<?php echo $_POST['job_selection_process_step_id'];?>">
          <?php 
          foreach($survey_details as $datail){
          $value_id = 0; $display_questions = $surveyData['question'][$datail['id']];
          //print_r($display_questions);
          ?>
          <div class="row col-lg-12 font-weight-bolder h6 mb-2"> 
            <?php echo $datail['name']; ?>              
            </div>
          <?php foreach ($display_questions as $questionid=>$val) { 
            $value_id++;
            $selectedoption = $answer[$questionid];

            ?>
            <input type="hidden" name="value_id[]" value="<?php echo $questionid;?>">
            <div class="col-lg-12 mb-3" id="disdiv<?php echo $questionid;?>" <?php echo $addcss;?>>
              <div >
                <input type="hidden" name="questionid<?php echo $questionid;?>" value="<?php echo $questionid;?>">
                <input type="hidden" name="job_feedback_setup_id<?php echo $questionid;?>" value="<?php echo $display_questions[$questionid]['job_feedback_setup_id'];?>">
                <!-- <span class="mt-5"><?php echo $question_type = $questiontypes[$display_questions[$questionid]['options_display_type']]; ?></span> -->
                <h6><?php echo $value_id.". ".$display_questions[$questionid]['survey_question']; ?> <?php if($display_questions[$questionid]['require_response']=="1") echo "*"; ?></h6>
                <?php 
                $optionlist= json_decode($display_questions[$questionid]['survey_question_option'],1);
                $option=$optionlist['0'];
                if($display_questions[$questionid]['options_display_type']==1 || $display_questions[$questionid]['options_display_type']==3 ){ 

                  foreach ($optionlist as $option) {
                    if($display_questions[$questionid]['allow_multiple_selections']==1) {
                      $option_cat = "radio";
                    }else if ($display_questions[$questionid]['allow_multiple_selections']==0) {
                      $option_cat = "checkbox";
                    }
                    ?>
                    <b> <div class="<?php echo $option_cat;?>-group required float-left ml-2">
                      <input type="<?php echo $option_cat;?>" value="<?php echo $option;?>" name="response_option<?php echo $questionid?>[]"  <?php echo ($option==$selectedoption )==1 ?  'Checked': '';?> >
                      <label for="q1"><?php echo $option; ?> </label> </div>
                    </b>
                  <?php }
                }else if($display_questions[$questionid]['options_display_type']==2){ 
                  
                  ?>
                        
                 <label class="">Scale from 1 to: <?php echo $option;?></label>
                  <input type="number"  class="form-control col-md-5" name="response_option<?php echo $questionid;?>"   id="response_option<?php echo $questionid;?>" value=<?php echo $selectedoption;?> > 
                  <?php             
                }else if($display_questions[$questionid]['options_display_type']==4){
                 
                 ?>
                 <textarea id="response_option<?php echo $questionid;?>"  name="response_option<?php echo $questionid;?>" class="form-control mb-2 col-lg-8" placeholder=""><?php echo $selectedoption;?></textarea>
                  
               <?php }  ?>
               <div class="col-lg-12 row">
                <!-- <textarea id="comments<?php echo $questionid;?>"  name="comments<?php echo $questionid;?>" class="form-control mb-2 col-lg-8" placeholder="Comments"><?php echo $selectedoption;?></textarea> -->
                <!-- <input type="text" class="form-control col-lg-3 ml-2" name="" placeholder="Ratting"> -->

              </div>
             </div>  
           </div>
         <?php } } ?>
       </div>     
     </form>
   </div>
   <div class="modal-footer">
    <button type="button" name="submit" onclick="formsubmit()" class="btn btn-primary btn-bold btn-upper btn-font-sm" data-dismiss="modal">Submit</button>
   <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
  </div>