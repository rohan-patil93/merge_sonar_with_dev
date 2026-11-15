<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$category = array('1' =>'Survey','2'=> 'Poll' );
$table_list = array(TBL_SURVEY_SETUP,TBL_SURVEY_SETUP_QUESTIONS,TBL_SURVEY_SETUP_QUESTION_OPTIONS);
$company_id = $_SESSION['company_id'];
$survey_id =EXITE_INTERVIEW_SURVEY_ID;
$employeeid = $_POST['emp_id'];
$vurl = API."?mode=getsurveyresult&company_id=".$company_id."&survey_emp_id=".$employeeid."&is_active=0"."&survey_id=".$survey_id;
$configesponse = callAPI($vurl);
$surveyData = json_decode($configesponse,1);
$survey_details = $surveyData['survey_details'];
$survey_response_details = $surveyData['survey_reponse_details'];
$survey_start_date = $survey_details['survey_start_date'];
$survey_end_date = $survey_details['survey_end_date'];
$survey_start_time = $survey_details['survey_start_time'];
$survey_end_time = $survey_details['survey_end_time'];
$allocated_time =  $survey_details['allocated_time'] == '0'? ST_SURVEY_DURATION_IN_MINUTES : $survey_details['allocated_time'];
$allocated_time = $allocated_time * 60;
$display_questions = $surveyData['questions'];
$answer = $surveyData['resultdata'];
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
    console.log("Ravi:"+str);
    jQuery.ajax({
      url: "ajax.php",
      data:'page=emp_exit_survey&'+str,
      type: "POST",
      success:function(data){
        console.log(data);
		$('#kt_modal_KTDatatable_remote1').modal("hide");
      },
      error:function (){}
  });
}
</script>
<div class="modal-header">
  <h5 class="modal-title">
    <?= __('Exit Interview') ?>
  </h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= __('Close') ?>"></button>
</div>
<div class="modal-body1" >
  <form method="post" id="myform" name="myform" action="">
    <div class="row q-sec m-10">
      <input type="hidden" name="emp_id" value="<?php echo $_POST['emp_id'];?>">
      <input type="hidden" name="category" value="<?php echo '1';?>">
      <input type="hidden" id="allocatedtime" value="<?php echo ST_SURVEY_DURATION_IN_MINUTES;?>">
      <input type="hidden" id="total_q" value="<?php echo $total_q;?>" name="total_q" >
      <input type="hidden" id="survey_id" value="<?php echo EXITE_INTERVIEW_SURVEY_ID;?>" name="survey_id">
      <input type="hidden" id="answered" name="answered">
      <input type="hidden" id="response_start_time" name="response_start_time" value="<?php echo date('H:i:s'); ?>">
      <?php
      $value_id = 0;
	if(count((array)$display_questions)>0){
      foreach ($display_questions as $questionid=>$val) {
        $value_id++;
        $selectedoption = json_decode($answer[$questionid],1);
        ?>
        <input type="hidden" name="value_id[]" value="<?php echo $questionid;?>">
        <div class="col-lg-12 mb-3" id="disdiv<?php echo $questionid;?>" <?php echo $addcss;?>>
          <div >
            <input type="hidden" name="questionid<?php echo $questionid;?>" value="<?php echo $questionid;?>">
            <!-- <span class="mt-5"><?php echo $question_type = $questiontypes[$display_questions[$questionid]['options_display_type']]; ?></span> -->
            <h6><?php echo $value_id.". ".__($display_questions[$questionid]['survey_question']); ?> <?php if($display_questions[$questionid]['require_response']=="1") echo "*"; ?></h6>
            <?php
            if($display_questions[$questionid]['options_display_type']==1 || $display_questions[$questionid]['options_display_type']==3 ){
              $optionlist= json_decode($display_questions[$questionid]['survey_question_option'],1);
              foreach ($optionlist as $option) {
                if($display_questions[$questionid]['allow_multiple_selections']==1) {
                  $option_cat = "radio";
                }elseif ($display_questions[$questionid]['allow_multiple_selections']==0) {
                  $option_cat = "checkbox";
                }
                ?>
                <b> <div class="<?php echo $option_cat;?>-group required float-left ml-2">
                  <input aria-label="<?= htmlspecialchars(__('Response option'), ENT_QUOTES, 'UTF-8'); ?>" type="<?php echo $option_cat;?>" value="<?php echo $option;?>" name="response_option<?php echo $questionid?>[]"  <?php echo in_array($option,(array)$selectedoption )==1 ?  'Checked': '';?> >
                  <label for="q1"><?php echo __($option); ?> </label> </div>
                </b>
              <?php }
            }elseif($display_questions[$questionid]['options_display_type']==2){
              $selectedoption =$answer[$questionid];
              ?>

              <span class=" d-inline-block fw-bold" style="margin-bottom:5px"><?= __('Scale from 1 to:') ?> <?php echo $option;?></span>
              <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="number"  class="form-control col-md-5" value=<?php echo $selectedoption;?> >
              <?php
            }elseif($display_questions[$questionid]['options_display_type']==4){

             ?>
             <textarea id="response_option<?php echo $questionid;?>"  name="response_option<?php echo $questionid;?>" class="form-control mb-2" placeholder=""><?php echo $selectedoption;?></textarea>
             <?php
           }

           ?>

         </div>
       </div>
     <?php }
     }else{ ?>
		<div class="f-24 text-danger text-center"><?php echo $_SESSION['error_message']['8009']; ?> </div>
	<?php } ?>
   </div>
  </form>
</div>
<div class="modal-footer">

  <button type="button" name="submit" onclick="formsubmit()" class="btn btn-primary btn-bold btn-upper btn-font-sm" data-dismiss="modal"><?= __('Submit') ?></button>
  <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-bs-dismiss="modal"><?= __('Close') ?></button>
</div>