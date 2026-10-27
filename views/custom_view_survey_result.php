<?php

$category = array('1' =>'Survey','2'=> 'Poll' );
$table_list = array(TBL_SURVEY_SETUP,TBL_SURVEY_SETUP_QUESTIONS,TBL_SURVEY_SETUP_QUESTION_OPTIONS);
$company_id = $_SESSION['company_id'];

if($_GET['Pid']!=''){
 $eid =  encodeId($_GET['Pid']);
 $eid = urldecode($eid);
 $ex_eid = explode(':', $eid);
 $survey_id = $ex_eid['1'];
 $eid =  encodeId($_GET['Eid']);
 $eid = urldecode($eid);
 $ex_eid = explode(':', $eid);
 $employeeid = $ex_eid['1'];
}
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

<script type="text/javascript">

  var anscount = 0;
  var myVar =0;
  function startTimer(duration, display) {
    duration = duration * 60;
    document.getElementById("save").style.display = "block";
    document.getElementById("start").style.display = "none";
    var start = Date.now(),
    diff,
    minutes,
    seconds;
    function timer() {
// get the number of seconds that have elapsed since
diff = duration - (((Date.now() - start) / 1000) | 0);
// does the same job as parseInt truncates the float
minutes = (diff / 60) | 0;
seconds = (diff % 60) | 0;
minutes = minutes < 10 ? "0" + minutes : minutes;
seconds = seconds < 10 ? "0" + seconds : seconds;
if(minutes==0 && seconds==0 ){
  clearInterval(myVar);
  document.getElementById(display).innerHTML = "EXPIRED";
 submitsurvey();
}else {
  document.getElementById(display).innerHTML = minutes + ":" + seconds;
}
if (diff <= 0) {
// add one second so that the count down starts at the full duration
// example 05:00 not 04:59
start = Date.now() + 1000;
}
};
// we don't want to wait a full second before the timer starts
timer();
myVar = setInterval(timer, 1000);
}

var obj = {};
function callnext(qno,currentqno,qtype) {
  var x;
  var totalquestions= document.getElementById('total_q').value;
  for(x=1;x<=totalquestions;x++) {
    if(x==qno){
      document.getElementById("disdiv"+x).style.display = "block";
      document.getElementById("start").style.display = "none";
      document.getElementById("save").style.display = "block";
    }else{
      document.getElementById("disdiv"+x).style.display = "none";
    }
  }
  var checkedval=[];
  if(qtype==1 || qtype==3){
    var inputs = document.querySelectorAll('.response_option'+currentqno);
    for (var i = 0; i < inputs.length; i++) {
      if(inputs[i].checked){
        document.getElementById('summary'+currentqno).style.backgroundColor = '#0BB783' ;
        document.getElementById('summary'+currentqno).style.Color = '#000000' ;
        checkedval.push(inputs[i].value);

     }
   }
   if(checkedval.length>0){
     obj[currentqno] = checkedval;
   }else{
     document.getElementById('summary'+currentqno).style.backgroundColor = '#FFFFFF' ;
     document.getElementById('summary'+currentqno).style.Color = '#000000' ;
     delete obj[currentqno];
     console.log(obj);
   }

 }else{
  var textval= document.getElementById('response_option'+currentqno).value;
  if(textval.length>0){
    document.getElementById('summary'+currentqno).style.backgroundColor = '#0BB783' ;
    document.getElementById('summary'+currentqno).style.Color = '#000000' ;
    checkedval.push(textval);

    obj[currentqno] = checkedval;
  }else{
    delete obj[currentqno];

    document.getElementById('summary'+currentqno).style.backgroundColor = '#FFFFFF' ;
    document.getElementById('summary'+currentqno).style.Color = '#000000' ;
  }
}
console.log(obj);

document.getElementById('anscount').innerHTML = Object.keys(obj).length;
document.getElementById('unanscount').innerHTML = totalquestions - Object.keys(obj).length;
document.getElementById('answered').value = Object.keys(obj).length;
}

function callclear(qno,qtype){
  if(qtype==1 || qtype==3){
    var inputs = document.querySelectorAll('.response_option'+qno);
    for (var i = 0; i < inputs.length; i++) {
      inputs[i].checked = false;
    }
  }else{
    document.getElementById('response_option'+qno).value = "";
  }
  document.getElementById('summary'+qno).style.backgroundColor = '#FFFFFF' ;
  document.getElementById('summary'+qno).style.Color = '#000000' ;
  delete obj[qno];
  document.getElementById('anscount').innerHTML = Object.keys(obj).length;
  document.getElementById('unanscount').innerHTML = totalquestions - Object.keys(obj).length;
}
function submitform(){
 console.log("form submission after");
 $("#myForm").submit();
}

function submitsurvey(){

  Swal.fire({
    title: "",
    text: "Time is Expired And Please Submit",
    buttonsStyling: false,
    confirmButtonText: "<i class='la la-thumbs-up'></i>OK",
    showCancelButton: false,
    cancelButtonText: "<i class='la la-thumbs-down'></i> No I will do it later",
    customClass: {
     confirmButton: "btn btn-success",
     cancelButton: "btn btn-default"
   }
 }).then(function(result) {

   document.getElementById('expiry_submit').style.display ='block';
   document.getElementById('save').style.display ='none';
   document.getElementById('previous').style.display ='none';

   console.log("form submission before");
});
}
</script>
<div class="d-flex flex-column-fluid">
  <div class="container-fluid">
    <div class="row">
      <?php if($_GET['Pid']!='') { ?>
        <div class="col-lg-12">
          <div class="card card-custom " id="kt_page_sticky_card">

            <div class="card-header mb-3">
                        <h5>Responses</h5>
                        <div class="card-header-right">
                            <?php if(isset($_GET['Rid'])){ ?>
                                <a href="index.php?option=COM_VIEW_STOCKTAKES&Rid=<?php echo $_GET['Rid']; ?>" class="btn btn-success"> <i class="fa fa-eye"></i> Priview</a>
                            <?php } ?>
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                    </div>

          <?php } ?>

          <form method="post" id="myform" name="myform" action="">

            <div class="row q-sec m-10">

              <input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id'];?>">
              <input type="hidden" name="category" value="<?php echo $display_tag;?>">
              <input type="hidden" id="allocatedtime" value="<?php echo ST_SURVEY_DURATION_IN_MINUTES;?>">
              <input type="hidden" id="total_q" value="<?php echo $total_q;?>" name="total_q" >
              <input type="hidden" id="survey_id" value="<?php echo $_GET['SID'];?>" name="survey_id">
              <input type="hidden" id="answered" name="answered">

              <?php
              $value_id = 0;

              foreach ($display_questions as $questionid=>$val) {
                $value_id++;
                $selectedoption = json_decode($answer[$questionid],1);
                ?>

                <input type="hidden" name="value_id[]" value="<?php echo $questionid;?>">

                <div class="col-lg-12" id="disdiv<?php echo $questionid;?>" <?php echo $addcss;?>>
                  <div >
                    <input type="hidden" name="questionid<?php echo $questionid;?>" value="<?php echo $questionid;?>">


                    <h4 class="f-14"><?php echo $value_id.". ".$display_questions[$questionid]['survey_question']; ?> <?php if($display_questions[$questionid]['require_response']=="1") { echo "*";
} ?></h4>
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
                        <b> <div class="<?php echo $option_cat;?>-group required">
                          <input aria-label="Input field" type="<?php echo $option_cat;?>" value="<?php echo $option;?>" name="response_option<?php echo $questionid?>[]" readonly <?php echo in_array($option,(array)$selectedoption )==1 ?  'Checked': '';?> disabled>
                          <label for="q1"><?php echo $option; ?> </label> </div>
                        </b>
                      <?php }
                    }elseif($display_questions[$questionid]['options_display_type']==2){
                      $selectedoption =json_decode($answer[$questionid],1)[0];
                      ?>

                      <span class="mt-10">Scale from 1 to: <?php echo $option;?></span>
                      <input aria-label="Enter number" type="number" readonly class="form-control col-md-5" value=<?php echo $selectedoption;?> >
                      <?php
                    }

                    elseif($display_questions[$questionid]['options_display_type']==4){
                     $selectedoption =json_decode($answer[$questionid],1)[0];

                     ?>
                     <textarea aria-label="Text area" id="response_option<?php echo $questionid;?>" readonly  class="form-control mt-10 col-md-10" placeholder=""><?php echo $selectedoption;?></textarea>
                     <?php
                   }

                   ?>

                 </div>
               </div>
             <?php } ?>

           </div>
         </div>

       </form>
     </div></div>
     <?php if($_GET['Pid']!='') { ?>
     </div></div>
   <?php } ?>
