<?php
//id:3 => YVdRNk13PT0=
$_GET['CAT'] =1;
$category = array('1' =>'Assessment');
$display_tag = $category[$_GET['CAT']];
$table_list = array(TBL_ASSESSMENT_SETUP,TBL_ASSESSMENT_SETUP_QUESTIONS,TBL_ASSESSMENT_SETUP_QUESTION_OPTIONS);
if(isset($_GET['Rid']) && $_GET['Rid']!="") {
 $rids =  encodeId($_GET['Rid']);
 $rid = urldecode($rids);
 $cand = explode(':', $rid);
 $row_id = $cand['1'];
}else{
   $_SESSION['questions_list'] = array();
}
$typeofquestion= array('1'=>'MCQ','2'=>'MCA','3'=>'FT');

$table_name= $table_list[0];
$company_id = $_SESSION['company_id'];
$emp_id = $_SESSION['emp_id'];
$vurl = API."?mode=getassessmentdetails&company_id=".$company_id."&emp_id=".$emp_id."&is_active=0&assessment_id=".$row_id;
$configesponse = callAPI($vurl);
$employeeDetails = json_decode($configesponse,1);
$employee_list = $employeeDetails['employeedetails'];
$qustion_types = $employeeDetails['qustion_types'];
$assessment_details = $employeeDetails['assessment_details'];
$assessment_qustion_details = $employeeDetails['assessment_qustion_details'];
$assessment_option_details = $employeeDetails['assessment_option_details'];

$publish_status = $assessment_details['publish_status'];
$filtertbl_rowid = $assessment_details['assessment_assigned_to'];
$allocated_time =  $assessment_details['allocated_time'];

foreach ($assessment_option_details as $option_data) {
 $answer[$option_data['question_id']] =  json_decode($option_data['assessment_question_option'],1);
 $marks[$option_data['question_id']] =  $option_data['assessment_marks'];
 $correctoption[$option_data['question_id']] =  json_decode($option_data['assessment_correct_option'],1);
}



$who_can_see_id = json_decode($assessment_details['who_can_see_the_results'],1);
$assessment_date = $assessment_details['assessment_start_date']." ".$assessment_details['assessment_start_time']." / ".$assessment_details['assessment_end_date']." ".$assessment_details['assessment_end_time'];
$assessment_types = $employeeDetails['assessment_types'];
//print_r($answer); exit;

foreach ($qustion_types as $key => $value) {
 $question_types[$value['genc_detail_code']]  = $value['genc_detail_code'];
 $question_title[$value['genc_detail_code']]  = $value['genc_detail_name'];

}
if($row_id!='') {
//print_r($assessment_qustion_details);
   foreach ($assessment_qustion_details as $qkey => $qvalue) {
      $qid = $qvalue['id']; 
      //print_r($answer[$qid]);
      $checkedstatus = array();
      foreach($answer[$qid] as $anslist){
         $checkedstatus[] = in_array($anslist,(array)$correctoption[$qid]) ? "checked" : "";
      }
      // print_r($checkedstatus);

      $questions_list = array('title'=>$question_title[$qvalue['options_display_type']],'type'=>$qvalue['options_display_type'],'question'=>$qvalue['assessment_question'],'marks'=>$marks[$qid],'checkedstatus'=>$checkedstatus,'allow_multiple_selections'=>$qvalue['allow_multiple_selections'],'option_data'=>$answer[$qid],'rid'=>$row_id,'question_id'=>$qid);
      $questiondetails[$qid] = json_encode($questions_list,1);

   }
   $_SESSION['questions_list'] = $questiondetails;
}

//echo "<pre>"; print_r($_SESSION['questions_list']); //exit;

if(isset($_POST['create'])){
 /*  $assessment_date= $_POST['assessment_date'];
   $assessment_start_date = fromDate(substr($assessment_date, 0, 10));
   $assessment_start_time = substr($assessment_date, 11, 8);
   $assessment_end_date = fromDate(substr($assessment_date, 22, 10));
   $assessment_end_time = substr($assessment_date, 33, 8);
*/
	$assessment_start_date = date('Y-m-d',strtotime($_POST['assessment_start_date']));
   $assessment_start_time = "12:00:00";
   $assessment_end_date = date('Y-m-d',strtotime($_POST['assessment_end_date']));
   $assessment_end_time = "12:00:00";

   $survy_setup_array = array('assessment_name'=>$_POST['assessment_name'],'assessment_type'=>$_POST['assessment_type'],'description'=>$_POST['description'],'assessment_start_date'=> $assessment_start_date,'assessment_start_time'=>$assessment_start_time,'assessment_end_date'=>$assessment_end_date,'assessment_end_time'=>$assessment_end_time,'assessment_initiator'=>$_POST['assessment_initiator'],'allocated_time'=>$_POST['allocated_time'],'assessment_assigned_to'=>$_POST['assessment_assigned_to'],'who_can_see_the_results'=>json_encode($_POST['who_can_see_the_results'],1));

   $survy_setup_array['created_date'] = nowDate();
   $survy_setup_array['created_by'] = $_SESSION['userid'];
   $survy_setup_array['is_active'] = "0";
   $survy_setup_array['publish_status'] = "";
   $survy_setup_array['category'] = $_GET['CAT'];


   $survy_setup_array['company_id'] = $_SESSION['company_id'];
  // echo '<pre>'; print_r($_POST); exit;
   foreach ($_POST['value_id'] as $vkey => $i) {
      $assessment_question='assessment_question'.$i;
      $options_display_type='options_display_type'.$i;
      $question_id='question_id'.$i;
      $allow_multiple_selections='allow_multiple_selections'.$i;
      $assessment_question_option='assessment_question_option'.$i;
      $assessment_marks='marks'.$i;
      $assessment_correct_option='assessment_correct_option'.$i;
      $oplist = array_keys(($_POST[$assessment_correct_option]));
      $assessment_answer_option = array();
      foreach($oplist as $ops){
           $assessment_answer_option[] = $_POST[$assessment_question_option][$ops];
      }
     // print_r($assessment_answer_option);
     // $oplist = $_POST[$assessment_question_option][];
      //$_POST[$assessment_correct_option] = $_POST[$assessment_correct_option]== 'on' ? '0' : '1';


     // print_r($_POST[$assessment_correct_option]); //exit;
      $_POST[$allow_multiple_selections] = $_POST[$allow_multiple_selections]== 'on' ? '0' : '1';
      $qustion[]=array('rid'=>$_POST['rid'],'question_id'=>$_POST[$question_id],'assessment_question'=>$_POST[$assessment_question],'options_display_type'=>$_POST[$options_display_type],'allow_multiple_selections'=>$_POST[$allow_multiple_selections],'assessment_question_option'=>json_encode($_POST[$assessment_question_option],1),'assessment_correct_option'=>json_encode($assessment_answer_option,1),'assessment_marks'=>$_POST[$assessment_marks]);

   }
   $data['main'] = $survy_setup_array;
   $data['qustion'] = $qustion;

 //echo '<pre>';  print_r($data); exit;
   if($_GET['Rid']==""){
     $url = API."?mode=createassessments&table_setup=".$table_list[0]."&table_questions=".$table_list[1]."&table_options=".$table_list[2]."&method=post";
  }
  else{
     $url = API."?mode=createassessments&table_setup=".$table_list[0]."&table_questions=".$table_list[1]."&table_options=".$table_list[2]."&method=post&rowid=".$row_id;
  }
  //echo "<pre>".$row_id;
  //print_r($data); exit;
  $retunn_assessment_id = file_post_contents($url,$data,'insert');
  $return_response=json_decode($retunn_assessment_id,1);
 // exit;
  header("location:index.php?option=COM_LIST_ASSESSMENT_SETUP&CAT=".$_GET['CAT']); 
 // header("location:index.php?option=".$_GET['option']."&CAT=".$_GET['CAT']."&Rid=".$return_response);  
}

?>
<script src="assets/js/pages/myscrpit/bootstrap-daterangepicker.js"></script> 
<script type="text/javascript">

   function deletequestion(qid){
     // alert(qid);
      $.ajax({
         url: "ajax.php",
         data:'page=removeqidfromsession&qid='+qid,
         type: "POST",
         success:function(){
            $('#'+qid).remove();
         },
         error:function (){}
      });

   }

   function addnewoptionrows(qid,type) {
      var op = document.getElementById('option_row').value;
      var op = parseInt(op)+1;
      var optionlistid = "optionlist"+qid+op;

      if(type=='MCQ'){
         var appenddiv = '<label class="radio radio-primary  mt-4""><input type="radio" name="assessment_correct_option'+qid+'['+op+']" id="singlechoice'+qid+op+'"><span></span></label>';
      }else{
         var appenddiv = '<label class="checkbox checkbox-primary  mt-4""><input type="checkbox"  name="assessment_correct_option'+qid+'['+op+']" id="multiplechoice'+qid+op+'"><span></span></label>';
      }

      $("#options"+qid).append('<div id='+"'"+optionlistid+"'"+'> <div class="col-lg-1 mt-4">'+appenddiv+'</div><div class="col-lg-8"><div class="input-group input-icon-right"><input type="text" class="form-control display-inline"  name="assessment_question_option'+qid+'[]" placeholder="Option"><div class="input-group-append"><span class="input-group-text bg-primary"><i class="flaticon-delete btn-primary" onclick="delnewoptionrows('+"'"+op+"'"+','+"'"+qid+"'"+')"></i></span></span></div></div></div></div>');

      document.getElementById('option_row').value = op ;

   }



   function delnewoptionrows(rowid,qid) {
      var optionlistid = "optionlist"+qid+rowid;
      $("#"+optionlistid).remove();
   }


   function dynamiceditpopup(type,title){
    jQuery.ajax({
     url: "views/custom_insert_assessment_addquestions.php",
     data:'type='+type+"&question_title="+title,
     type: "GET",
     success:function(data){
      $('#popup2').html(data);
      $('#kt_modal_KTDatatable_setup').modal("show");
   },
   error:function (){}
});
 }

 function statuspopup(status,id){
   //alert("data");
   var table_name = document.getElementById('table_name').value;
   jQuery.ajax({
      url: "views/custom_insert_status_popup.php",
      data:'table='+table_name+'&status='+status+'&rid='+id,
      type: "POST",
      success:function(data){
         $('#popup1').html(data);
         $('#kt_modal_KTDatatable_remote').modal("show");
      },
      error:function (){}
   });
}

function displaystausdrop(id,table,status_id){
   //alert("id");
   $.ajax({
      url: "ajax.php",
      data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
      type: "POST",
      success:function(data){
        console.log(data);
        $("#dropdownlist_"+id).html(data);
     },
     error:function (){}
  });
   
}

function showpopup(){
  var filter_id = document.getElementById('filtertbl_rowid').value;
  //console.log(filter);
  jQuery.ajax({
    url: "views/custom_filter_bulktransfer.php",
    data:'filter_id='+filter_id,
    type: "GET",
    success:function(data){
     $('#dynamic_popup1').html(data);
     $('#kt_modal_dynamic_popup').modal('show');
  },
  error:function (){}
});
}
function getnamelist(){

   var selectednames = document.getElementById('filter').value;
   
   $.ajax({
      url: "ajax.php",
      data:'page=selectednames&names='+selectednames,
      type: "POST",
      success: function(response) {
         var division = document.getElementById('tsk_fortable');

         console.log("response ="+response);
         division.innerHTML = response;
      }
   });
}

</script>
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
               <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                  <i class="ki ki-long-arrow-back icon-xs"></i>
                  Back
               </a>
            </div>
         
   </div>

   <div class="card card-custom">
      <div class="card-body">
         <form method="post" action="" class="j-pro">
            <div class="row">

               <div class="col-lg-6 px-4">
                  <input type="hidden" name="assessment_assigned_to" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>">
                  <div class="card card-custom gutter-b">

                     <div class="card-body">
                        <div class="form-group">
                           <label class="font-weight-bolder"><?php echo $display_tag; ?> Name <span class="text-danger">*</span></label>
                           <input type="text" name="assessment_name" value="<?php echo $assessment_details['assessment_name'];?>" class="form-control" placeholder="Enter Title">
                           <!-- <span class="form-text text-muted">We'll never share your email with anyone else.</span> -->
                        </div>
                        <div class="form-group">
                           <label class="font-weight-bolder"><?php echo $display_tag; ?> Type <span class="text-danger">*</span></label>
                           <div class="col-9 col-form-label">
                              <div class="radio-inline">
                                 <?php foreach ($assessment_types as $value) { ?>
                                    <label class="radio radio-primary">
                                       <input type="radio" name="assessment_type" value="<?php echo $value['genc_detail_code'];?>" <?php if($assessment_details['assessment_type'] == $value['genc_detail_code']) {?> checked="checked" <?php } ?>  />
                                       <span></span><?php echo $value['genc_detail_name']; ?></label>
                                    <?php } ?>
                                 </div>
                                 <!-- <span class="form-text text-muted">We'll never share your email with anyone else.</span> -->
                              </div>
                              <div class="form-group mt-3">
                                 <label class="font-weight-bolder"><?php echo $display_tag; ?> Description <span class="text-danger">*</span></label>
                                 <textarea class="form-control" name="description" placeholder="Enter Title"> <?php echo $assessment_details['description'];?> </textarea>
                                 <!-- <span class="form-text text-muted">We'll never share your email with anyone else.</span> -->
                              </div>
                              <div class="form-group mt-3">
                                 <label class="font-weight-bolder"><?php echo $display_tag; ?> Date Range <span class="text-danger">*</span></label>
                                 <div class='row' id='kt_daterangepicker_survey'>
								   <div class="col-lg-6">
										<input type='text' class="form-control datepicker"  name="assessment_start_date"  value="<?php echo $assessment_details['assessment_start_date']; ?>" />
									</div>
									<div class="col-lg-6">
										<input type='text' class="form-control datepicker"  name="assessment_end_date" value="<?php echo $assessment_details['assessment_end_date']; ?>" />
									</div>
								 <!--  <input type='date' class="form-control"  name="assessment_date" placeholder="Select date &amp; time range" value="<?php echo $assessment_date; ?> " /> -->
								   
								</div>
								<!-- <div class='input-group' id='kt_daterangepicker_assessment'>
                                    
                                     <div class="input-group-append">
                                       <span class="input-group-text">
                                          <i class="la la-calendar-check-o"></i>
                                       </span>
                                    </div> 
                                 </div>-->
                              </div>
                              <div class="form-group mt-3">
                                 <label class="font-weight-bolder"><?php echo $display_tag; ?> Duration In Minutes<span class="text-danger">*</span></label>
                                 <div class='input-group'>
                                    <input type='number' class="form-control"  name="allocated_time" placeholder="Total Minutes" value="<?php echo $allocated_time; ?>" />
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="example-date-input" class="col-form-label font-weight-bolder"><?php echo $display_tag; ?> Initiator</label>
                                 <select class="form-select js-example-basic-single" id="kt_select2_48" name="assessment_initiator">
                                    <option> Select any one</option>
                                    <?php $assessment_init =  $assessment_details['assessment_initiator']!="" ? $assessment_details['assessment_initiator'] :$_SESSION['emp_id'];
                                    foreach ($employee_list as $value) { ?>
                                       <option value="<?php echo $value['id'];?>" <?php if($value['id'] == $assessment_init){ ?>selected <?php } ?> ><?php echo $value['display_name']; ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                              <div class="form-group">
                                 <label class="font-weight-bolder">Asign to <span class="text-danger">*</span></label>
                                 <div id="tsk_fortable"></div>
                                 <div class="col-xl-12">
                                    <i class="flaticon2-group text-success"></i>
                                    <a href="#" onclick="showpopup();">Edit </a>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="example-date-input" class="col-form-label font-weight-bolder">Who can see the results</label>
                                 <select class="form-select js-example-basic-multiple" id="kt_select2_43" name="who_can_see_the_results[]" multiple="multiple">
                                    <?php foreach ($employee_list as $value) { $key=$value['id']; ?>
                                    <option <?php if(in_array($key,(array)$who_can_see_id)){ ?> selected <?php } ?> value="<?php echo $key;?>"><?php echo $value['display_name']; ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                        </div>
                        <div class="card-footer d-flex row">

                           <div class="col-lg-3">
                              <button type="submit" name="create" class="btn btn-primary font-weight-bold float-left"><?php echo $_GET['Rid']!='' ? 'Update' : 'Save'; ?></button>                       
                           </div>

                           <div class="col-lg-6 text-right">
                              <!-- Example single danger button-->
                              <div class="btn-group">
                                 <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Add Questions</button>
                                 <div class="dropdown-menu">
                                    <?php  foreach ($qustion_types as $key => $value) {
                              //$question_types[$value['genc_detail_code']]  = $value['genc_detail_code']; ?>
                              <a href="#" onclick="dynamiceditpopup('<?php echo $value['genc_detail_code']; ?>','<?php echo $value['genc_detail_name']; ?>')" class="dropdown-item">  <?php echo $value['genc_detail_name']; ?></a>
                           <?php } ?>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-lg-3"></div>
            <div class="col-lg-3">
            </div>
         </div>
      </div>
      <div class="col-lg-6 p-10" id="assessment_questions">
        <?php if($_GET['Rid']!='') { 
         $value_id = 0;
         foreach ($_SESSION['questions_list'] as $qkey => $qvalue) {
            $value = json_decode($qvalue,1);
            $options = $value['option_data'];
        $checkedstatus = $value['checkedstatus'];
            $marks = $value['marks'];           
            $title = $value['title'];
            $deleteqid = $value['uniq_qid'];
            $question_id = $value['question_id'];          
           $rid = $value['rid'];   
            $display_question .= ' <div class="mt-5" id="'.$qkey.'"> <div class="col-lg-12 table-bordered">
            <input type="hidden" name="question_id'.$value_id.'" value="'.$question_id.'">
            <input type="hidden" name="rid" value="'.$rid.'">
            <input type="hidden" name="value_id[]" value="'.$value_id.'">
            <input type="hidden" name="options_display_type'.$value_id.'" value="'.$value['type'].'">

            <div class="modal-header" >
            <h5 class="modal-title" id="exampleModalLabel">'.$title.'</h5>
            
			<i aria-hidden="true" class="fa fa-trash close text-white"  data-dismiss="modal" aria-label="Close" onclick="deletequestion('."'".$qkey."'".')"></i>
            
            </div>
            <div class="card-body mt-3">
            <div class="form-group text-left row">
            <div class="col-lg-8">
            <label>Question Text: <span class="text-danger"></span></label>
            <textarea id="assessment_question'.$value_id.'" name="assessment_question'.$value_id.'" class="form-control" placeholder=""> '.$value['question'].' </textarea>
            </div>';
            $display_question .= '<div class="col-lg-4">
            <label>Marks <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="markfield" name="marks'.$value_id.'" value="'.$marks.'"  >
            </div>'; 
            
            $display_question .= '</div>';
            if($value['type']== $typeofquestion['1'] || $value['type']== $typeofquestion['2']) { 
               $display_question .= '<div class="col-lg-12 mt-4 mb-4">  Mark the Correct Answer </div>';
               $display_question .= '<div id="options'.$value_id.'">';
               foreach ($options as $opkey => $opvalue) { 
                   $checkstatus = $checkedstatus[$opkey];
                  $display_question .= '<div id="optionlist'.$value_id.$opkey.'">
                  <div class="form-group row" id="'.$opkey.'">
                  <div class="col-lg-12  row ml-5"> <div class="">'; 
                  if($value['type']== $typeofquestion['1'] ) {
                     $display_question .= '<label class="radio radio-primary mt-4">
                     <input type="radio"  name="assessment_correct_option'.$value_id.'['.$opkey.']" id="singlechoice'.$value_id.$opkey.'" '.$checkstatus.'>"
                     <span></span>
                     </label>
                     <input type="hidden" name="allow_multiple_selections'.$value_id.'" id="allow_multiple_selections'.$value_id.'" value="0" />';
                  } else {
                     $display_question .= '<label class="checkbox checkbox-primary mt-4">
                    <input type="checkbox"  name="assessment_correct_option'.$value_id.'['.$opkey.']"  id="multiplechoice'.$value_id.$opkey.'" '.$checkstatus.'>
                     <span></span>
                     </label>
                     <input type="hidden" name="allow_multiple_selections'.$value_id.'" id="allow_multiple_selections'.$value_id.'" value="1" />';
                  } 
                  $copvalue = $checkstatus=='checked'? $opvalue : '';
                  $display_question .= '
                                  
                  </div><div class="col-lg-8"> 
				         <div class="input-group input-group-button"> 
        <input type="text" class="form-control display-inline width1" name="survey_question_option'.$value_id.'[]"  placeholder="Option" value="'.$opvalue.'">
        <span class="input-group-text addon-style">
            <label class=""><i class="fa fa-trash close text-white" onclick="delnewoptionrows('."'".$opkey."'".','."'".$value_id."'".')"></i> </label>
        </span>
    </div>
	 </div>';
                  $display_question .= '</div>';
                  
               }
               $display_question .= '</div>';
               $op = count((array)$options)+1;
               $display_question .= '<div class="form-group">
               <input type="hidden" value="'.$opkey.'" id="option_row"/>
               <button class="btn btn-success"  onclick="addnewoptionrows('."'".$value_id."'".",'".$value['type']."'".')" id="addNewRows" type="button">+ Add More Options</button>
               </div>';
            }  
            if($value['type']==$typeofquestion['3'])  {  
               $display_question .= '  <input type="hidden" name="assessment_question_option'.$value_id.'[]"  class="form-control" value=""> ';
            } 
            $display_question .= '</div> </div>  ';
            $value_id ++;

         }
         echo $display_question;    
      }
      ?>
</div>
</div>

</form>
<div id="kt_modal_KTDatatable_setup" class="modal fade" role="dialog" aria-hidden="true">
   <div class="modal-dialog  modal-lg" >
      <div class="modal-content"  id="popup2">
      </div>
   </div>
</div>
