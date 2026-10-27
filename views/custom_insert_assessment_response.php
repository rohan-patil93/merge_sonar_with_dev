<?php
if($_GET['Rid']!=""){
  $rid =  encodeId($_GET['Rid']);
  $rid = urldecode($rid);
  $ex_rid = explode(':', $rid);
  $assessment_id = $ex_rid['1']; 
}
$category = array('1' =>'Assessment' );
$table_list = array(TBL_ASSESSMENT_SETUP,TBL_ASSESSMENT_SETUP_QUESTIONS,TBL_ASSESSMENT_SETUP_QUESTION_OPTIONS);
$company_id = $_SESSION['company_id'];
$emp_id = $_SESSION['emp_id'];

$vurl = API."?mode=getsharedassessmentdetails&company_id=".$company_id."&emp_id=".$emp_id."&is_active=0"."&assessment_id=".$assessment_id;
$configesponse = callAPI($vurl);
$assessmentData = json_decode($configesponse,1);
$assessment_details = $assessmentData['assessment_details'];
//print_r($assessment_details); exit;
$assessment_response_details = $assessmentData['assessment_reponse_details'];
$assessment_start_date = $assessment_details['assessment_start_date'];
$assessment_end_date = $assessment_details['assessment_end_date'];
$assessment_start_time = $assessment_details['assessment_start_time'];
$assessment_end_time = $assessment_details['assessment_end_time'];
$allocated_time =  $assessment_details['allocated_time'] == '0'? ST_SURVEY_DURATION_IN_MINUTES : $assessment_details['allocated_time'];
$allocated_time = $allocated_time * 60;
$display_questions = $assessmentData['questions'];
$display_tag = $category[$assessment_details['category']];

//$duration = timediff($assessment_start_date,$assessment_end_date,$assessment_start_time,$assessment_end_time);
//print_r($display_questions); exit;
$qustion_types = $assessmentData['qustion_types'];
$total_q = count((array)$display_questions);
$_GET['Qid']==''? ($questionid=1) : ($questionid=$_GET['Qid']);
$answer = json_decode($_SESSION['assessment'][$emp_id][$assessment_id]['answered'][$_GET['Qid']]['answer'],1);

foreach ($_SESSION['assessment'][$emp_id][$assessment_id]['answered'] as $keyans=>$valueans) {
  $answers_no[]=$keyans;
}
foreach ($qustion_types as $qtype) {
  $questiontypes[$qtype['genc_detail_code']] =  $qtype['genc_detail_name'];
}

if(isset($_POST['clear'])) {
  $data['Qid'] = $_GET['Qid']; 
  unset($_SESSION['answered_question'][$questionid]);
  header("location:index.php?option=".$_GET['option']."&Qid=".$questionid);   
}
if(isset($_POST['submit'])){

  $unanswer = $_POST['total_q'] - $_POST['answered'];
  if($unanswer>0){
    $partial_complete = 0;
  }

  $diff = (strtotime(date('H:i:s'))-strtotime($_SESSION['assesment_response_start_time'])) > 0 ? (strtotime(date('H:i:s'))-strtotime($_SESSION['assesment_response_start_time']) ) : 3600;

  $duration_taken = round($diff/3600,2);

  $commondata = array('duration_taken'=>$duration_taken,'partial_complete'=>$partial_complete,'category'=>$_POST['category'],'employee_id'=>$_POST['emp_id'],'assessment_id'=>$_POST['assessment_id'],'response_start_date'=>date('Y-m-d'),'response_start_time'=>$_SESSION['assesment_response_start_time'],'response_end_date'=>date('Y-m-d'),'response_end_time'=>date('H:i:s'),'created_date'=>date('Y-m-d'),'created_by'=>$_SESSION['user_id'],'is_active'=>'0','company_id'=>$_SESSION['company_id']);
  $commondata['current_status'] = ST_COMPLETE; 
  $data['assessment_response'] = $commondata;
  //print_r($commondata); exit;
  unset($commondata['duration_taken']);
  unset($commondata['partial_complete']);
  unset($commondata['category']);
$total_score = 0;
  foreach($_POST['value_id'] as $q_no) {
   $ans_data= $_POST['response_option'.$q_no];
   $commondata['question_id'] = $_POST['queid'.$q_no];
   $commondata['response_option']=json_encode($ans_data,1);
   $display_questions['response_option']=json_encode($ans_data,1);
  // echo $_POST['queid'.$q_no];
   //print_r($display_questions[$q_no]['assessment_correct_option']);
    $correctoptionvalue= $display_questions[$q_no]['assessment_correct_option'];
    $correctoptionmarks= $display_questions[$q_no]['assessment_marks'];

   if($correctoptionvalue==$display_questions['response_option']){
     $total_score += $correctoptionmarks;
   }
 $data['assessment_response']['total_score'] = $total_score;
  $commondata['current_status'] = ST_COMPLETE; 
   $data['assessment_response_details'][] = $commondata;
 }
 $url = API."?mode=saveassessmentreponses&table=assessment_responses&method=post";
 //echo "<pre>"; print_r($data['assessment_response']); exit;
 $retunn_inserted_qid = file_post_contents($url,$data,'insert'); 
 header("location:index.php?option=COM_LIST_EMP_ASSESSMENT&CAT=".$_GET['CAT']);
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
// startTimer() was called
diff = duration - (((Date.now() - start) / 1000) | 0);
// does the same job as parseInt truncates the float
minutes = (diff / 60) | 0;
seconds = (diff % 60) | 0;
minutes = minutes < 10 ? "0" + minutes : minutes;
seconds = seconds < 10 ? "0" + seconds : seconds;
if(minutes==0 && seconds==0 ){
  clearInterval(myVar);
  document.getElementById(display).innerHTML = "EXPIRED";
 // alert("Time Expired");
 submitassessment();
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
  if(qtype=='MCA' || qtype=='MCQ'){
    var inputs = document.querySelectorAll('.response_option'+currentqno); 
    for (var i = 0; i < inputs.length; i++) { 
      if(inputs[i].checked){
        document.getElementById('summary'+currentqno).style.backgroundColor = '#0BB783' ;
        document.getElementById('summary'+currentqno).style.Color = '#000000' ;
        checkedval.push(inputs[i].value);
        
       // break;
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
 //document.forms["myform"].submit();
 //document.getElementById("assessmentform").submit();
 $("#myForm").submit();
}

function submitassessment(){


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
  //submitform();
 // document.getElementById("assessmentform").submit();
  //document.getElementById("assessmentform").submit.click();
});



}


</script>
<div class="d-flex flex-column-fluid">
  <div class="container-fluid">
      <div class="card card-custom card-sticky" id="kt_page_sticky_card">
		
	  
	 <div class="card-header mb-3">
						<h5> <?php echo $assessment_details['assessment_name']; ?></h5>
						<div class="card-header-right">
							<div class="countdown float-right">
            <span class="fas fa-stopwatch"><b>Timing Remaining </b></span> 
            <div id="duration"> </div>
            <div id="timerdisplay" class="font-weight-bold font-size-h3 text-danger"> Not Yet Start</div>
          </div> 
			</div>
		</div>
<form method="post" id="myform" name="myform" action="">
<div class="row">
        
      </div>
      <div class="row ">  
        <div class="col-md-4" STYLE="OVERFLOW: HIDDEN;">
            <img src="assets/media/books/img-72.jpg" class="">
        </div>  
        <input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id'];?>">
        <input type="hidden" id="category" name="category" value="<?php echo $assessment_details['category'];?>">
        <input type="hidden" id="allocatedtime" value="<?php echo ST_SURVEY_DURATION_IN_MINUTES;?>">
        <input type="hidden" id="total_q" value="<?php echo $total_q;?>" name="total_q" >
        <input type="hidden" id="assessment_id" value="<?php echo $assessment_id;?>" name="assessment_id">
        <input type="hidden" id="answered" name="answered">

        <?php 
        $value_id = 0;

        foreach ($display_questions as $questionid=>$val) { 
          $value_id++;
			$addcss = "";
          $dispnext = "";
          if($questionid!=1) {  $addcss = "style='display:none'"; } 
          if($questionid==1) {  $dispnext = "style='display:none'"; } 

          ?>
          <input type="hidden" name="value_id[]" value="<?php echo $questionid;?>">
          <input type="hidden" name="queid<?php echo $questionid?>" value="<?php echo $display_questions[$questionid]['queid'];?>">
			<div class="col-md-5" id="disdiv<?php echo $questionid;?>" <?php echo $addcss;?>>
            <div class="q-a">
              <input type="hidden" name="questionid<?php echo $questionid;?>" value="<?php echo $questionid;?>">

              <span><?php echo $question_type = $questiontypes[$display_questions[$questionid]['options_display_type']]; ?></span>
              <h4><?php echo $display_questions[$questionid]['assessment_question']; ?> <?php if($display_questions[$questionid]['require_response']=="1") echo "*"; ?></h4>
              <?php 
              if($display_questions[$questionid]['options_display_type']=='MCQ' || $display_questions[$questionid]['options_display_type']=='MCA' ){ 
                $optionlist= json_decode($display_questions[$questionid]['assessment_question_option'],1);

                foreach ($optionlist as $option) {
                  if($display_questions[$questionid]['options_display_type']=='MCQ') {
                    $option_cat = "radio";
                  }else if ($display_questions[$questionid]['options_display_type']=='MCA') {
                    $option_cat = "checkbox";
                  }
                  ?>
                  <b> <div class="<?php echo $option_cat;?>-group required">
                    <input type="<?php echo $option_cat;?>" class="response_option<?php echo $questionid;?>" value="<?php echo $option;?>" name="response_option<?php echo $questionid?>[]"   <?php if($display_questions[$questionid]['require_response']=="1") echo "required"; ?> <?php echo in_array($option, (array)$answer)==1 ?  'Checked': '';?> >
                    <label for="q1"><?php echo $option; ?> </label> </div>
                  </b>
                <?php }
              }else if($display_questions[$questionid]['options_display_type']== 'FT'){
                $answer =$answer[0];
                ?>
                <textarea id="response_option<?php echo $questionid;?>" name="response_option<?php echo $questionid?>[]"  <?php if($display_questions[$questionid]['require_response']=="1") echo "required"; ?> class="form-control mt-10 col-md-10" placeholder=""><?php echo $answer;?></textarea> 
                <?php             
              }
			?>
              <div class="card-footer mt-5">
                <?php if ($total_q!=$questionid){ ?> 
                  <a href="#" id="save" <?php echo $dispnext; ?> onclick="callnext('<?php echo $questionid+1;?>','<?php echo $questionid;?>','<?php echo $display_questions[$questionid]['options_display_type']; ?>')" class="btn btn-success font-weight-bold float-left">Next <i class="fas fa-chevron-right"> </i></a>
                  <?php if($questionid==1) { ?>
                    <a href="#" id="start" style="display: block"  onclick="startTimer('<?php echo $allocated_time; ?>','timerdisplay')" class="btn btn-success font-weight-bold float-left">Start Now <i class="fas fa-chevron-right"> </i></a>
                  <?php } ?>
                <?php } else {?>
                  <a href="#" id="previous" onclick="callnext('<?php echo $questionid-1;?>','<?php echo $questionid;?>','<?php echo $display_questions[$questionid]['options_display_type']; ?>')" class="btn btn-success font-weight-bold float-left"><i class="fas fa-chevron-left"> </i>Previous </a>
                <?php } ?>

                <!-- <a href="#" id="clear" onclick="callclear('<?php echo $questionid;?>','<?php echo $display_questions[$questionid]['options_display_type']; ?>')" class="btn btn-light-success font-weight-bold float-left">Clear Response</a> -->
                <?php if ($questionid!=1 && $total_q!=$questionid){ ?> 
                  <a href="#" id="previous" onclick="callnext('<?php echo $questionid-1;?>','<?php echo $questionid;?>','<?php echo $display_questions[$questionid]['options_display_type']; ?>')" class="btn btn-success font-weight-bold float-right"><i class="fas fa-chevron-left"> </i>Previous </a>
                <?php } 

                if ($total_q==$questionid){ ?> 
                  <div id="expiry_submit" style="display:block">
                  <input class="float-right btn btn-success font-weight-bold" type="submit" name="submit" value="Submit <?php echo $display_tag;?>">
                  </div>
                <?php }else{  ?>
                <div id="expiry_submit" style="display:none">
                  <input class="float-right btn btn-success font-weight-bold"  type="submit" name="submit" value="Submit <?php echo $display_tag;?>">
                </div>
              <?php } ?>
              </div>
            </div>  
          </div>
        <?php } ?>
        <div class="col-md-2">
          <div class="q-n">
            <ul>
              <?php foreach ($display_questions as $qidkey=>$qidval) { ?>
                <li><a id="summary<?php echo $qidkey;?>" href="#" onclick="callnext('<?php echo $qidkey;?>','<?php echo $questionid;?>','<?php echo $display_questions[$questionid]['options_display_type']; ?>')" ><?php echo $qidkey; ?></a></li>
              <?php  } ?>
            </ul>
            <div class="row">
              <ul> <li><a href="#"><div id="anscount">0</div></a></li> </ul>
              <h5> &nbsp;Answered </h5>
            </div>
            <div class="row">
              <ul> <li><a href="#"><div id="unanscount"><?php echo $total_q;?></div></a></li> </ul>
              <h5> &nbsp;Unanswered </h5>
            </div>   
            
          </div>    
        </div> 
      </div>   
    </form>
  </div></div></div>
