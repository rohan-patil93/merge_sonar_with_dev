<?php
if($_GET['Rid']!=""){
  $rid =  encodeId($_GET['Rid']);
  $rid = urldecode($rid);
  $ex_rid = explode(':', $rid);
  $survey_id = $ex_rid['1'];
}
$category = array('1' =>'Survey', '2'=> 'Poll' );
$table_list = array(TBL_SURVEY_SETUP,TBL_SURVEY_SETUP_QUESTIONS,TBL_SURVEY_SETUP_QUESTION_OPTIONS);
$company_id = $_SESSION['company_id'];
$emp_id = $_SESSION['emp_id'];

$vurl = API."?mode=getsharedsurveydetails&company_id=".$company_id."&emp_id=".$emp_id."&is_active=0"."&survey_id=".$survey_id;
$configesponse = callAPI($vurl);
$surveyData = json_decode($configesponse,1);
$survey_details = $surveyData['survey_details'];
//print_r($survey_details);
$survey_response_details = $surveyData['survey_reponse_details'];
$survey_start_date = $survey_details['survey_start_date'];
$survey_end_date = $survey_details['survey_end_date'];
$survey_start_time = $survey_details['survey_start_time'];
$survey_end_time = $survey_details['survey_end_time'];
$allocated_time =  $survey_details['allocated_time'] == '0'? ST_SURVEY_DURATION_IN_MINUTES : $survey_details['allocated_time'];
$allocated_time = $allocated_time * 60;
$display_questions = $surveyData['questions'];
$display_tag = $category[$survey_details['category']];
$markedanswer = $surveyData['resultdata'];

//$duration = timediff($survey_start_date,$survey_end_date,$survey_start_time,$survey_end_time);
//print_r($markedanswer); exit;
$qustion_types = $surveyData['qustion_types'];
$total_q = count((array)$display_questions);
$_GET['Qid']==''? ($questionid=1) : ($questionid=$_GET['Qid']);
//$answer = json_decode($_SESSION['survey'][$emp_id][$survey_id]['answered'][$_GET['Qid']]['answer'],1);

foreach ($_SESSION['survey'][$emp_id][$survey_id]['answered'] as $keyans=>$valueans) {
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
  // print_r($_POST); exit;
  $partial_complete = 1;
  $answered = 0;
  if (count((array)$_POST['value_id']) > 0) {
    foreach ($_POST['value_id'] as $q_no) {
      $ans_data = $_POST['response_option'.$q_no];
      if (count((array)$ans_data) > 0 && trim($ans_data[0]) != '') {
        $answered++;
      }
    }
  }
  $unanswer = (int)$_POST['total_q'] - $answered;
  if ($unanswer > 0) {
    $partial_complete = 0;
  }
  
  $diff = (strtotime(date('H:i:s'))-strtotime($_SESSION['response_start_time'])) > 0 ? (strtotime(date('H:i:s'))-strtotime($_SESSION['response_start_time']) ) : 3600;
  
  $duration_taken = round(abs($diff)/3600,2);

  $commondata = array('duration_taken'=>$duration_taken,'partial_complete'=>$partial_complete,'category'=>$_POST['category'],'employee_id'=>$_POST['emp_id'],'survey_id'=>$_POST['survey_id'],'response_start_date'=>date('Y-m-d'),'response_start_time'=>$_SESSION['response_start_time'],'response_end_date'=>date('Y-m-d'),'response_end_time'=>date('H:i:s'),'created_date'=>date('Y-m-d'),'created_by'=>$_SESSION['user_id'],'is_active'=>'0','company_id'=>$_SESSION['company_id']);
  // print_r($commondata); exit;
  if($_POST['submit']=='Submit '.$display_tag) {
    $commondata['current_status'] = ST_COMPLETE; 
  }else{
    $commondata['current_status'] = ST_INPROGRESS;
   }
  $data['survey_response'] = $commondata;

  unset($commondata['duration_taken']);
  unset($commondata['partial_complete']);
  unset($commondata['category']);

  foreach($_POST['value_id'] as $q_no) {
   $ans_data= $_POST['response_option'.$q_no];
   $commondata['question_id']=$_POST['questionid'.$q_no];
   $commondata['response_option']=json_encode($ans_data,1);
   $data['survey_response_details'][] = $commondata;
 }
 $url = API."?mode=savesurveyreponses&table=survey_responses&method=post";
 $retunn_inserted_qid = file_post_contents($url,$data,'insert'); 
  //  echo "<pre>"; print_r($data); //exit;
  header("location:index.php");exit;
  // if($_SESSION['working_role_short_code'] == "SURVEY_ADMIN"){
  //   header("location:index.php?option=COM_SURVEY_ADMIN_DASHBOARD&CAT=".$_GET['CAT']);
  // }else{
  //   header("location:index.php?option=COM_EMPLOYEE_SURVEY_ADMIN_DASHBOARD&CAT=".$_GET['CAT']);
  // }
}
?>
<style type="text/css">
  .q-n ul li{
    display:inline-block;
  }
  .active {
    background-color: #0BB783; color: #ffffff;
  }
</style>
<script type="text/javascript">


  var anscount = 0;
  var myVar =0;
  function startTimer(duration, display) {
    duration = duration * 60;
    document.getElementById("save").style.display = "block";
    document.getElementById("start").style.display = "none";
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
        //document.getElementById('summary'+currentqno).style.backgroundColor = '#0BB783' ;
        //document.getElementById('summarytext'+currentqno).style.Color = '#000000' ;
		  //$('#summarytext'+currentqno).css("color","#000000");
      $('#summarytext'+currentqno).addClass("active");
        checkedval.push(inputs[i].value);
        
       // break;
     }
   } 
   if(checkedval.length>0){
     obj[currentqno] = checkedval;
   }else{
     document.getElementById('summary'+currentqno).style.backgroundColor = '#FFFFFF' ;
     //document.getElementById('summarytext'+currentqno).style.Color = '#000000' ;
	 $('#summarytext'+currentqno).css("color","#000000");
     delete obj[currentqno];
     console.log(obj);
   }

 }else{
  var textval= document.getElementById('response_option'+currentqno).value;
  if(textval.length>0){
    //document.getElementById('summary'+currentqno).style.backgroundColor = '#0BB783' ;
    //document.getElementById('summarytext'+currentqno).style.Color = '#000000' ;
	//$('#summarytext'+currentqno).css("color","#000000");
  $('#summarytext'+currentqno).addClass("active");
    checkedval.push(textval);

    obj[currentqno] = checkedval; 
  }else{
    delete obj[currentqno];

    document.getElementById('summary'+currentqno).style.backgroundColor = '#FFFFFF' ;
   // document.getElementById('summarytext'+currentqno).style.Color = '#000000';
$('#summarytext'+currentqno).css("color","#000000");	
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
  //document.getElementById('summarytext'+qno).style.Color = '#000000' ; 
  $('summarytext'+qno).css("color","#000000");
  delete obj[qno];
  document.getElementById('anscount').innerHTML = Object.keys(obj).length;
  document.getElementById('unanscount').innerHTML = totalquestions - Object.keys(obj).length;
}
function submitform(){
 console.log("form submission after");
 //document.forms["myform"].submit();
 //document.getElementById("surveyform").submit();
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
  //submitform();
 // document.getElementById("surveyform").submit();
  //document.getElementById("surveyform").submit.click();
});

}


</script>



<div class="d-flex flex-column-fluid">
  <div class="container-fluid">
      <div class="card card-custom card-sticky" id="kt_page_sticky_card">
		
		<div class="card-header mb-3">
						<h5> <?php echo $survey_details['survey_name']; ?></h5>
						<div class="card-header-right">
							
					  <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
								<i class="icofont icofont-reply"></i>
								Back
							</a>
						</div>
					</div>
    <form method="post"  class="p-4" id="myform" name="myform" action="">
      <div class="row">
        
       <!--  <div class="col-md-6">
          <div class="countdown float-right">
            <span class="fas fa-stopwatch"><b>Timing Remaining </b></span> 
            <div id="duration"> </div>
            <div id="timerdisplay" class="font-weight-bold font-size-h3 text-danger"> Not Yet Start</div>
          </div>   
        </div> -->
      </div>
      <div class="row q-sec">  
        <div class="col-md-5">
          <div class="q-img">
            <img src="assets/media/books/img-72.jpg" class="w-100">
          </div>    
        </div>  
        <input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id'];?>">
        <input type="hidden" id="category" name="category" value="<?php echo $survey_details['category'];?>">
        <input type="hidden" id="allocatedtime" value="<?php echo ST_SURVEY_DURATION_IN_MINUTES;?>">
        <input type="hidden" id="total_q" value="<?php echo $total_q;?>" name="total_q" >
        <input type="hidden" id="survey_id" value="<?php echo $survey_id;?>" name="survey_id">
        <input type="hidden" id="answered" name="answered">
        <?php 
        $value_id = 0;

        foreach ($display_questions as $questionid=>$val) { 
          $value_id++;
          $selectedoption = json_decode($markedanswer[$display_questions[$questionid]['queid']],1);
          $addcss = "";
          $dispnext = "";
          if($questionid!=1) {  $addcss = "style='display:none'"; } 
          if($questionid==1) {  $dispnext = "style='display:none'"; } 

          ?>
          <input type="hidden" name="value_id[]" value="<?php echo $questionid;?>">

          <div class="col-md-5" id="disdiv<?php echo $questionid;?>" <?php echo $addcss;?>>
            <div class="q-a">
              <input type="hidden" name="questionid<?php echo $questionid;?>" value="<?php echo $display_questions[$questionid]['queid'];?>">

              <span><?php echo $question_type = $questiontypes[$display_questions[$questionid]['options_display_type']]; ?></span>
              <h4><?php echo $display_questions[$questionid]['survey_question']; ?> <?php if($display_questions[$questionid]['require_response']=="0") echo "*"; ?></h4>
              <?php 
              if($display_questions[$questionid]['options_display_type']==1 || $display_questions[$questionid]['options_display_type']==3 ){ 
                $optionlist= json_decode($display_questions[$questionid]['survey_question_option'],1);
                foreach ($optionlist as $option) {
                  if($display_questions[$questionid]['allow_multiple_selections']==1) {
                    $option_cat = "radio";
                  }else if ($display_questions[$questionid]['allow_multiple_selections']==0) {
                    $option_cat = "checkbox";
                  }
                  ?>
                  <b> <div class="<?php echo $option_cat;?>-group required">
                    <input type="<?php echo $option_cat;?>" class="response_option<?php echo $questionid;?>" value="<?php echo $option;?>" name="response_option<?php echo $questionid?>[]"   <?php if($display_questions[$questionid]['require_response']=="0") echo "required"; ?> <?php echo in_array($option,(array)$selectedoption )==1 ?  'Checked': '';?> >
                    <label for="q1"><?php echo $option; ?> </label> </div>
                  </b>
                <?php }
              }else if($display_questions[$questionid]['options_display_type']==2){ 
                $selectedoption =json_decode($markedanswer[$display_questions[$questionid]['queid']],1)[0];
                ?>

                <label class="mt-10">Scale from 1 to: <?php echo $option;?></label>
                <input type="number" <?php if($display_questions[$questionid]['require_response']=="0") echo "required"; ?> id="response_option<?php echo $questionid;?>" name="response_option<?php echo $questionid?>[]" class="form-control col-md-5" value="<?php echo $markedanswer;?>" placeholder="<?php echo $selectedoption;?>"> 
                <?php             
              }
              else if($display_questions[$questionid]['options_display_type']==4){
                $selectedoption =json_decode($markedanswer[$display_questions[$questionid]['queid']],1)[0];

                ?>
                <textarea id="response_option<?php echo $questionid;?>" name="response_option<?php echo $questionid?>[]"  <?php if($display_questions[$questionid]['require_response']=="0") echo "required"; ?> class="form-control mt-10 col-md-10" placeholder=""><?php echo $selectedoption;?></textarea> 
                <?php             
              }

              ?>
              <div class="card-footer mt-5">

                <?php if ($total_q!=$questionid){ ?> 
                  <a href="#" id="save" <?php echo $dispnext; ?> onclick="callnext('<?php echo $questionid+1;?>','<?php echo $questionid;?>','<?php echo $display_questions[$questionid]['options_display_type']; ?>')" class="btn btn-success font-weight-bold float-left">Next <i class="fas fa-chevron-right"> </i></a>
                  <?php if($questionid==1) { ?>
                    <a href="#" id="start" style="display: block"   onclick="callnext('<?php echo $questionid+1;?>','<?php echo $questionid;?>','<?php echo $display_questions[$questionid]['options_display_type']; ?>')" class="btn btn-success font-weight-bold float-left">Next <i class="fas fa-chevron-right"> </i></a>
                  <?php } ?>
                <?php } else {?>
                  <a href="#" id="previous" onclick="callnext('<?php echo $questionid-1;?>','<?php echo $questionid;?>','<?php echo $display_questions[$questionid]['options_display_type']; ?>')" class="btn btn-success font-weight-bold float-left"><i class="fas fa-chevron-left"> </i>Previous </a>
                <?php } ?>

                <!-- <a href="#" id="clear" onclick="callclear('<?php echo $questionid;?>','<?php echo $display_questions[$questionid]['options_display_type']; ?>')" class="btn btn-light-success font-weight-bold float-left">Clear Response</a> -->
                <?php if ($questionid!=1 && $total_q!=$questionid){ ?> 
                  <a href="#" id="previous" onclick="callnext('<?php echo $questionid-1;?>','<?php echo $questionid;?>','<?php echo $display_questions[$questionid]['options_display_type']; ?>')" class="btn btn-success font-weight-bold float-right"><i class="fas fa-chevron-left"> </i>Previous </a>
                <?php } ?>
                

                <?php if ($total_q==$questionid){ ?> 
                  <div id="expiry_submit" style="display:block">
                  <input class="btn btn-success font-weight-bold float-right" type="submit" name="submit" value="Submit <?php echo $display_tag;?>">
                  </div>
                <?php }else{  ?>
                <div id="expiry_submit" style="display:none">
                  <input class="btn btn-success font-weight-bold float-right"  type="submit" name="submit" value="Submit <?php echo $display_tag;?>">
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
                <li><a id="summary<?php echo $qidkey;?>" href="#" onclick="callnext('<?php echo $qidkey;?>','<?php echo $questionid;?>','<?php echo $display_questions[$questionid]['options_display_type']; ?>')" ><label class="form-label badge badge-inverse-primary" id="summarytext<?php echo $qidkey;?>"><?php echo $qidkey; ?></label></a></li>
              <?php  } ?>
            </ul>
            <div class="row">
              <ul> <li><a href="#"><h5> <span id="anscount">0 </span>  &nbsp;Answered </h5></a></li> </ul>
              
            </div>
            <div class="row">
              <ul> <li><a href="#"><h5> <span id="unanscount"><?php echo $total_q;?></span> &nbsp;Unanswered </h5></a></li> </ul>
              
            </div> 
            <div class="row mt-2">
              <input type="submit" name="submit" class="btn btn-success font-weight-bold " value="Save & Pause" /> 
            </div> 

          </div>    
        </div> 
      </div>   
    </form>
  </div></div></div>
