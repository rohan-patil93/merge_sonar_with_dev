<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$company_id = $_SESSION['company_id'];
$vendor_id = $_POST['vendor_id'];
$vurl = API."?mode=getvendorfeedbackresponsedata&vendor_id=".$vendor_id;
$configesponse = callAPI($vurl);
$data = json_decode($configesponse,1);
$survey_details = $data['feedback_details'];
$questions = $data['questions'];
$responses = $data['responses'];
$response_details = $data['response_details'];
$feedback_setup_details = $data['feedback_setup_details'];
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
  function formsubmit(){
    var str = $( "form" ).serialize();
    console.log("Ravi:"+str);
    jQuery.ajax({
      url: "ajax.php",
      data:'page=addvendorfeedbackresponse&'+str,
      type: "POST",
      success:function(data){
        console.log(data);
        $('#kt_modal_KTDatatable_remote1').modal("hide");
      },
      error:function (){}
    });
  }
  function showbaseuserdet(val){
    var nowid = $("#showidval").val();
    console.log(val+","+nowid);
    $("#bttn"+val).addClass("btn-primary");
    $("#bttn"+nowid).removeClass("btn-primary");
    $("#feedbackid_"+nowid).hide();
    $("#feedbackid_"+val).show();

    $("#showidval").val(val);
  }
</script>
<div class="modal-header">
  <h5 class="modal-title">
    <?php echo "Vendor Feedback"; ?>
  </h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body mb-3" >
  <div class="row col-lg-12">
    <div class="col-lg-3">
      <?php $k=1; foreach($responses as $resp){ ?>
        <div class="p-l-30 p-t-15">
          <div class="btn  <?php if($k==1){ echo 'btn-primary';
} ?> waves-effect" style="width:210px; text-align: left;" id="bttn<?php echo $resp['id']; ?>" onclick="showbaseuserdet(<?php echo $resp['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
            <?php echo $feedback_setup_details[$resp['vendor_feedback_setup_id']]['feedback_name']." - ".fromDate($resp['response_start_date']); ?>
          </div>
        </div>
      <?php $k++;
} ?>
       <input type="hidden" name="activeone"  id="showidval" value="<?php echo $responses['0']['id']; ?>">
    </div>
    <div class="col-lg-9">
      <?php if(count((array)$responses)>0){
        $mn=1;
        foreach($responses as $response){ ?>
          <div id="feedbackid_<?php echo $response['id']; ?>" <?php if($mn!=1){?> style="display: none;" <?php
} ?> >
            <div class="row col-lg-12">
              <div class="col-lg-3">
                <span class="f-16">Vendor Review Name:</span>
              </div>
              <div class="col-lg-9">
                <span class="text-muted f-14"><?php echo $feedback_setup_details[$response['vendor_feedback_setup_id']]['feedback_name']; ?></span>
              </div>
              <div class="col-lg-3">
                <span class="f-16">Feedback By:</span>
              </div>
              <div class="col-lg-3">
                <span class="text-muted f-14"><?php  echo $data['user']['display_name']; ?></span>
              </div>
              <div class="col-lg-3">
                <span class="f-16">Date:</span>
              </div>
              <div class="col-lg-3">
                <span class="text-muted f-14"><?php echo fromDate($response['response_start_date']); ?></span>
              </div>
               <div class="col-lg-3">
                <span class="f-16">Vendor Name:</span>
              </div>
              <div class="col-lg-3">
                <span class="text-muted f-14"><?php echo $data['vendor']['display_name']; ?></span>
              </div>
              <div class="col-lg-3">
                <span class="f-16">Ratting:</span>
              </div>
              <div class="col-lg-3">
                <div class="rating f-12 text-warning mt-2 d-inline-block">
                <?php for($n=1;$n<11;$n++){ ?>
                    <i class="fa fa-star f-12" <?php if($n>$response['over_all_ratting']){ ?> style= "color: #b7aeae"; <?php
} ?>></i>
                <?php } ?>
                </div>
              </div>
            </div>

          <?php $setup_id = $response['vendor_feedback_setup_id'];
          $response_id = $response['id'];
          foreach($response_details[$response_id] as $cat=>$detail){ ?>
              <div class="f-18 f-w-600 p-2 text-primary bg-gray mt-4" style="clear: both;">
                <?php echo $data['feedback_details'][$setup_id][$cat]['category_name'];
                ?>
              </div>
              <?php $value_id =1; foreach($detail as $quid=>$ques){
                    $val = $questions[$cat][$quid];
                    $res = $ques['response_option'];
               ?>
                  <div class="f-14 p-2 mt-4" style="clear: both;">
                      <h6 class="f-w-600">
                        <?php echo $value_id.". ".$val['question']; ?>
                      </h6>
                      <?php  $optionlist= json_decode($val['question_option'],1);
                        if($val['options_display_type']==1 || $val['options_display_type']==3 ){ ?>
                      <div class="mb-4">
                      <?php   if(!is_array($res)){ $res = array($res);
}
                       foreach ($optionlist as $option) {
                            if($val['allow_multiple_selections']==1) {
                              $option_cat = "radio";
                            }elseif ($display_questions[$questionid]['allow_multiple_selections']==0) {
                              $option_cat = "checkbox";
                            }
                      ?>
                          <b>
                            <div class="<?php echo $option_cat;?>-group required float-left ml-2">
                            <input aria-label="Input field" type="<?php echo $option_cat;?>" value="<?php echo $option;?>" name="response_option[<?php echo $response['fd_id']."_".$quid; ?>]"  <?php  if (in_array($option,(array)$res)){ echo 'Checked';
} ?> >
                           <label class="f-w-500" for="q1"><?php echo $option; ?> </label>
                            </div>
                          </b>

                      <?php } ?>
                    </div>
                    <?php }elseif($val['options_display_type']==2){  ?>
                      <div class="rating f-12 text-warning mt-2 d-inline-block">
                      <?php for($n=1;$n<=$optionlist['0'];$n++){ ?>
                          <i class="fa fa-star f-16" <?php if($res<$n){ ?> style= "color: #b7aeae"; <?php
} ?>></i>
                      <?php } ?>
                      </div>

                    <?php }elseif($val['options_display_type']==4){ ?>
                      <textarea aria-label="Text area" id="response_option<?php echo $questionid;?>" class="form-control mb-2" readonly><?php echo $res; ?></textarea>
                    <?php } ?>
                  </div>
              <?php $value_id++;
} ?>
          <?php  } ?>
          </div>
      <?php  $mn++;
}
        } ?>

    </div>
  </div>
</div>
