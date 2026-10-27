<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$table_list = array('vendor_feedback_setup','vendor_feedback_setup_questions','vendor_feedback_setup_question_options');
$company_id = $_SESSION['company_id'];
$vendor_id = $_POST['vendor_id'];
$vurl = API."?mode=getvendorfeedbackdata&vendor_id=".$vendor_id."&setup_id=".$_POST['setup_id'];
$configesponse = callAPI($vurl);
$data = json_decode($configesponse,1);
$feedback_setup_details = $data['feedback_setup_details'];
$feedback_details = $data['feedback_details'];
$questions = $data['questions'];
?>
<style>
    /* Star Rating Styles */
    .rating {
        display: inline-block;
    }
    .rating input {
        display: none;
    }
    .rating label {
        cursor: pointer;
        width: 25px;
        height: 25px;
        margin: 0;
        padding: 0;
        font-size: 25px;
        color: #ddd;
        float: right;
    }
    .rating label:before {
        content: '\2605';
    }
    .rating input:checked ~ label {
        color: #ffca08;
    }
    .rating label:hover,
    .rating label:hover ~ label {
        color: #ffca08;
    }
</style>
<script type="text/javascript">
  function formsubmit(val){
    var str = $( "form" ).serialize();
    console.log("Ravi:"+str);
    jQuery.ajax({
      url: "ajax.php",
      data:'page=addvendorfeedbackresponse&'+str+'&save_type='+val,
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
    <?php echo "Vendor Feedback - ".$feedback_setup_details['feedback_name']; ?>
  </h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body1 col-lg-9 mx-auto mt-1 mb-5">
  <form method="post" id="myform" name="myform" action="">
    <div class="row q-sec m-10">
      <input type="hidden" name="vendor_id" value="<?php echo $_POST['vendor_id'];?>">
      <input type="hidden" name="setup_id" value="<?php echo $_POST['setup_id'];?>">
      <?php

        if(count((array)$questions)>0){
          foreach($feedback_details as $cat=>$detail){ ?>
              <div class="f-18 f-w-600 p-2 text-primary bg-gray mt-4" style="clear: both;">
                <?php echo $detail['name']; ?>
              </div>
        <?php $value_id = 0; foreach ($questions[$detail['id']] as $questionid=>$val) {
            $value_id++;
            $selectedoption = json_decode($val['question_option'],1);
      ?>
        <input type="hidden" name="value_id[]" value="<?php echo $questionid;?>">
        <div class="col-lg-12 mt-4 p-l-3" id="disdiv<?php echo $questionid;?>" <?php echo $addcss;?>>
          <div>
            <input type="hidden" name="questionid<?php echo $questionid;?>" value="<?php echo $questionid;?>">
            <input type="hidden" value="<?php echo $detail['id'];?>" name="response_cat[<?php echo $questionid; ?>]"   >

            <h6 class="f-w-600"><?php echo $value_id.". ".$val['question']; ?> <?php if($display_questions[$questionid]['require_response']=="1"){ echo "*";
} ?></h6>
            <?php
            if($val['options_display_type']==1 || $val['options_display_type']==3 ){
              $optionlist= json_decode($val['question_option'],1);
              foreach ($optionlist as $option) {
                if($val['allow_multiple_selections']==1) {
                  $option_cat = "radio";
                }elseif ($display_questions[$questionid]['allow_multiple_selections']==0) {
                  $option_cat = "checkbox";
                }
                ?>
                <b> <div class="<?php echo $option_cat;?>-group required float-left ml-2 deep">
                  <input aria-label="Input field" type="<?php echo $option_cat;?>" value="<?php echo $option;?>" name="response_option[<?php echo $questionid; ?>]"  <?php echo in_array($option,(array)$selectedoption )==1 ?  'Checked': '';?> >
                  <label for="q1"><?php echo $option; ?> </label> </div>
                </b>

              <?php }
            }elseif($val['options_display_type']==2){
              $optionlist= json_decode($val['question_option'],1);
              ?>

              <div class="rating">
                <?php for($i=$optionlist['0'];$i>=1; $i--){ ?>
                    <input type="radio" id="star<?php echo $questionid.'_val_'.$i; ?>" name="response_option[<?php echo $questionid; ?>]" value="<?php echo $i; ?>">
                    <label for="star<?php echo $questionid.'_val_'.$i; ?>" aria-label="Star <?php echo $i; ?>"></label>
                <?php } ?>
                </div>
              <?php
            }elseif($val['options_display_type']==4){

             ?>
             <textarea aria-label="Text area" id="response_option<?php echo $questionid;?>"  name="response_option[<?php echo $questionid; ?>]" class="form-control mb-2" placeholder=""></textarea>
             <?php
           }

           ?>

         </div>
       </div>
     <?php } ?>
     <?php } ?>
      <div class="col-lg-12 mt-4" >
          <div >

            <h6 class="f-w-600"><?php $value_id++; echo $value_id; ?> Vendor over all ratting  *</h6>

              <div class="rating">
                <?php for($i=10;$i>=1; $i--){ ?>
                    <input type="radio" id="star<?php echo 'overall_'.$i; ?>" name="overall_rating" value="<?php echo $i; ?>">
                    <label for="star<?php echo 'overall_'.$i; ?>" aria-label="Star <?php echo $i; ?>"></label>
                <?php } ?>
                </div>
          </div>
        </div>
    <?php }else{ ?>
    <div class="f-24 text-danger text-center"><?php echo $_SESSION['error_message']['8009']; ?> </div>
  <?php } ?>
   </div>
  </form>
</div>
<div class="modal-footer modal-fixed">
  <button type="button" name="submit" onclick="formsubmit('199')" class="btn btn-primary btn-bold btn-upper btn-font-sm" data-dismiss="modal">Save & Submit</button>
  <button type="button" name="submit" onclick="formsubmit('2')" class="btn btn-primary btn-bold btn-upper btn-font-sm" data-dismiss="modal">Save</button>
  <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-bs-dismiss="modal">Close</button>
</div>
