<?php
$_SESSION['apply_js']['multiselest'] = '1';
$category = array('1' =>'Survey', '2'=> 'Poll');
//if($_GET['CAT']=="" AND $_GET['option']="COM_INSERT_SURVEY_SETUP"){
if($_GET['CAT']==""){
  $_GET['CAT'] = 1;
}
$display_tag = "Feedback";
$table_list = array(TBL_SURVEY_SETUP,TBL_SURVEY_SETUP_QUESTIONS,TBL_SURVEY_SETUP_QUESTION_OPTIONS);
if(isset($_GET['Rid']) && $_GET['Rid']!=""){
 $rids =  encodeId($_GET['Rid']);
 $rid = urldecode($rids);
 $cand = explode(':', $rid);
 $row_id = $cand['1'];
}else{
 $_SESSION['questions_list'] = array();
}
$table_name= $table_list[0];
$company_id = $_SESSION['company_id'];
$emp_id = $_SESSION['emp_id'];
$vurl = API."?mode=getvendorfeedbackeditdetails&company_id=".$company_id."&emp_id=".$emp_id."&is_active=0&survey_id=".$row_id;
$configesponse = callAPI($vurl);
$employeeDetails = json_decode($configesponse,1);
//echo "<pre>";print_r($employeeDetails);echo "<pre>";
$employee_list = $employeeDetails['employeedetails'];
$qustion_types = $employeeDetails['qustion_types'];
$survey_details = $employeeDetails['survey_details'];
$survey_qustion_details = $employeeDetails['survey_qustion_details'];
$survey_option_details = $employeeDetails['survey_option_details'];
$publish_status = $survey_details['publish_status'];
$filtertbl_rowid = $survey_details['survey_assigned_to'];
$allocated_time =  $survey_details['allocated_time'];

foreach ($survey_option_details as $option_data){
 $answer[$option_data['question_id']] =  json_decode($option_data['question_option'],1);
}
foreach ($survey_qustion_details as $qdata) {
 $starray[] =array("options_display_type"=>$qdata['options_display_type'],"question"=>$qdata['survey_question'],"require_response"=>$qdata['require_response'],"allow_multiple_selections"=>$qdata['allow_multiple_selections'],"question_option"=>$answer[$qdata['id']],'question_id'=>$qdata['id']);
}
$who_can_see_id = json_decode($survey_details['who_can_see_the_results'],1);
//$survey_date = $survey_details['survey_start_date']." ".$survey_details['survey_start_time']." / ".$survey_details['survey_end_date']." ".$survey_details['survey_end_time'];
$survey_types = $employeeDetails['survey_types'][$_GET['CAT']];
foreach ($qustion_types as $key => $value) {
 $question_types[$value['genc_detail_code']]  = $value['genc_detail_code'];
 $question_title[$value['genc_detail_code']]  = $value['genc_detail_name'];
}
if($row_id!='') {
 foreach ($survey_qustion_details as $qkey => $qvalue) {
  $qid = $qvalue['id'];
  $questions_list = array('title'=>$question_title[$qvalue['options_display_type']],'type'=>$qvalue['options_display_type'],'question'=>$qvalue['question'],'require_response'=>$qvalue['require_response'],'allow_multiple_selections'=>$qvalue['allow_multiple_selections'],'option_data'=>$answer[$qid],'rid'=>$row_id,'question_id'=>$qid);
  $questiondetails[$qid] = json_encode($questions_list,1);
}
//echo "<pre>"; print_r($answer); print_r($questiondetails); echo "</pre>";
$_SESSION['questions_list'] = $questiondetails;
}
if(isset($_POST['create'])){
 $survey_date= $_POST['survey_date'];
  /* $survey_start_date = date('Y-m-d',strtotime((substr($survey_date, 0, 10))));
   $survey_start_time = date('H:i:s',strtotime(substr($survey_date, 11, 8)));
   $survey_end_date = date('Y-m-d',strtotime((substr($survey_date, 22, 10))));
   $survey_end_time = date('H:i:s',strtotime(substr($survey_date, 33, 8)));
*/
   $survey_start_date = date('Y-m-d',strtotime($_POST['survey_start_date']));
   $survey_start_time = "12:00:00";
   $survey_end_date = date('Y-m-d',strtotime($_POST['survey_end_date']));
   $survey_end_time = "12:00:00";
   $survy_setup_array = array('name'=>$_POST['survey_name'],'type'=>$_POST['survey_type'],'description'=>$_POST['description'],'start_date'=> $survey_start_date,'start_time'=>$survey_start_time,'end_date'=>$survey_end_date,'end_time'=>$survey_end_time,'allocated_time'=>'0');
   $survy_setup_array['created_date'] = nowDate();
   $survy_setup_array['created_by'] = $_SESSION['userid'];
   $survy_setup_array['is_active'] = "0";
   $survy_setup_array['publish_status'] = "1";
   $survy_setup_array['company_id'] = $_SESSION['company_id'];
   foreach ($_POST['value_id'] as $vkey => $i) {
    $survey_question='survey_question'.$i;
    $options_display_type='options_display_type'.$i;
    $question_id='question_id'.$i;
    $allow_multiple_selections='allow_multiple_selections'.$i;
    $require_response='require_response'.$i;
    $survey_question_option='survey_question_option'.$i;
    $_POST[$allow_multiple_selections] = $_POST[$allow_multiple_selections]== 'on' ? '0' : '1';
    $_POST[$require_response] = $_POST[$require_response]== 'on' ? '0' : '1';
    $qustion[]=array('rid'=>$_POST['rid'],'question_id'=>$_POST[$question_id],'question'=>$_POST[$survey_question],'options_display_type'=>$_POST[$options_display_type],'require_response'=>$_POST[$require_response],'allow_multiple_selections'=>$_POST[$allow_multiple_selections],'survey_question_option'=>json_encode($_POST[$survey_question_option],1));

  } 
  $data['main'] = $survy_setup_array;
  $data['qustion'] = $qustion;
  if($_GET['Rid']==""){
   $url = API."?mode=createvendorfeedback&table_setup=".$table_list[0]."&table_questions=".$table_list[1]."&table_options=".$table_list[2]."&method=post";
 }else{
     $url = API."?mode=createvendorfeedback&table_setup=".$table_list[0]."&table_questions=".$table_list[1]."&table_options=".$table_list[2]."&method=post&rowid=".$row_id;
 } 
 $retunn_survey_id = file_post_contents($url,$data,'insert');
 //$return_response=json_decode($retunn_survey_id,1);
 //echo "<pre>"; print_r($data); echo "</pre>";
 header("location:index.php?option=COM_LIST_VENDOR_FEEDBACK_SETUP"); //exit;
 // header("location:index.php?option=".$_GET['option']."&CAT=".$_GET['CAT']."&Rid=".$return_response);  
}
?>
<!-- <script src="assets/js/pages/myscrpit/bootstrap-daterangepicker.js"></script>  -->
<script type="text/javascript">
 function deletequestion(qid){
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
function addnewoptionrows(qid) {
  var op = document.getElementById('option_row').value;
  var op = parseInt(op)+1;
     var optionlistid = "optionlist"+qid+op;
     $("#options"+qid).append('<div id='+"'"+optionlistid+"'"+'> <div class="form-group row" id="'+"'"+op+"'"+'"><div class="col-lg-1"></div><div class="col-lg-8"><div class="input-group input-icon-right"> <input type="text" class="form-control display-inline"  name="survey_question_option'+qid+'[]" placeholder="Option"> <div class="input-group-append"><span class="input-group-text bg-primary"><span><i class="fa fa-trash close text-white" onclick="delnewoptionrows('+"'"+op+"'"+','+"'"+qid+"'"+')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove Option"></i></span></span></div></div></div></div></div>');
     document.getElementById('option_row').value = op ;
   }


   function delnewoptionrows(rowid,qid) {
   //alert(qid);

   //alert(rowid);
   var optionlistid = "optionlist"+qid+rowid;
   $("#"+optionlistid).remove();
 }
 function dynamiceditpopup(type,title){
   jQuery.ajax({
    url: "views/custom_insert_vendor_feedback_addquestions.php",
    data:'type='+type+"&question_title="+title,
    type: "GET",
    success:function(data){
     $('#popup2').html(data);
   //$('#kt_modal_KTDatatable_setup').modal({ show: true});
   $("#kt_modal_KTDatatable_setup").modal("show");
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
     $('#kt_modal_KTDatatable_remote').modal('show');
   },
   error:function (){}
 });
 }

 function displaystausdrop(id,table,status_id){
   $.ajax({
    url: "ajax.php",
    data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
    type: "POST",
    success:function(data){
      $("#dropdownlist_"+id).html(data);
    },
    error:function (){}
  });
   
 }
 function showpopup(){
  var filter_id = document.getElementById('filtertbl_rowid').value;
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
 /*  function getnamelist(emplist){
      console.log(emplist);
      document.getElementById('filterby').value = emplist;
      $('#kt_modal_dynamic_popup').modal('hide');
   }
   */ 
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
 <div class="card">          
  <div class="card-header table-card-header">
   <h5>Vendor Feedback Setup</h5>
   <div class="card-header-right table-card-header ">
        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i> Back</a>
    </div>
</div>
<div class="card card-custom">
  <div class="card-body">
    <form method="post" class="" action="">
     <div class="row">
      <div class="col-lg-6 px-4">
       <input type="hidden" id="filterby" name="filterby"> 
       <input type="hidden" name="survey_assigned_to" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>">
       <div class="card card-custom gutter-b">
             <div class="card-body">
               <div class="form-group">
                <label class="form-label"> Name <span class="text-danger">*</span></label>
                <input type="text" name="survey_name" value="<?php echo $survey_details['name'];?>" class="form-control" placeholder="Enter Title">
              </div>
             
             
                
              
              <div class="form-group mt-3">
                <label class="form-label"><?php echo $display_tag; ?> Description <span class="text-danger">*</span></label>
                <textarea class="form-control" name="description" placeholder="Enter Title"><?php echo $survey_details['description'];?></textarea>
              </div>
              <div class=" form-group">
                <label class="form-label"><?php echo $display_tag; ?> Date Range <span class="text-danger">*</span></label>
                <div class='row' id='kt_daterangepicker_survey'>
                 <div class="col-lg-6">
                  <input type='text' class="form-control datepicker"  name="survey_start_date"  value="<?php echo fromDate($survey_details['start_date']); ?>" />
                </div>
                <div class="col-lg-6">
                  <input type='text' class="form-control datepicker"  name="survey_end_date" value="<?php echo fromDate($survey_details['end_date']); ?>" />
                </div>
              </div>
            
              <div class="form-group mt-3">
                  <?php //print_r($who_can_see_id); ?>
                  <label for="example-date-input" class="form-label">Who can see the results</label> 
                  <select class="form-select js-example-basic-multiple" id="kt_select2_43" name="who_can_see_the_results[]" multiple="multiple">
                   <?php foreach ($employee_list as $value) { 
                    $key=$value['id']; ?>
                    <option <?php if(count((array)$who_can_see_id) != 0 AND in_array($key,$who_can_see_id)){ ?> selected <?php } ?> value="<?php echo $key;?>"><?php echo $value['display_name']; ?></option>
                  <?php } ?>
                  </select>

              </div> 
              </div>
              <div class="col-lg-12 row">

                <div class="col-lg-3">
                 <button type="submit" name="create" class="btn btn-primary font-weight-bold float-left"><?php echo $_GET['Rid']!='' ? 'Update' : 'Save'; ?></button>                       
               </div>

               <div class="col-lg-9 text-right">
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
              <div class="col-lg-6 p-10 card card-custom " id="survey_questions">

                <?php if($_GET['Rid']!='') { 
                 $value_id = 0;
                 $totalquestion = count((array)$_SESSION['questions_list']);
                 foreach ($_SESSION['questions_list'] as $qkey => $qvalue) {
                  $value_id ++;
                  $value = json_decode($qvalue,1);
                  $allow_multiple_selections_status = $value['allow_multiple_selections']=='0'?'checked' :'';
                  $require_response_status = $value['require_response']=='0'?'checked' :'';
                  $options = $value['option_data'];
                  $question_id = $value['question_id'];
                  $rid = $value['rid'];
                  $totaloption = count((array)$options);
                  $title = $value['title'];
                  $display_question .= ' <div class="mt-5" id="'.$question_id.'"> <div class="col-lg-12 table-bordered">
                  <input type="hidden" name="totalquestion" value="'.$totalquestion.'">
                  <input type="hidden" name="question_id'.$qkey.'" value="'.$question_id.'">
                  <input type="hidden" name="rid" value="'.$rid.'">
                  <input type="hidden" name="value_id[]" value="'.$question_id.'">
                  <input type="hidden" name="options_display_type'.$qkey.'" value="'.$value['type'].'">

                  <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">'.$title.'</h5>
                  <i aria-hidden="true" class="fa fa-trash close text-white"  data-dismiss="modal" aria-label="Close" onclick="deletequestion('."'".$question_id."'".')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove Question"></i>
                  </div>
                  <div class="card-body mt-3">
                  <div class="form-group text-left">
                  <label>Question Text: <span class="text-danger"></span></label>
                  <textarea id="survey_question'.$qkey.'" name="survey_question'.$qkey.'" class="form-control" placeholder=""> '.$value['question'].' </textarea>
                  </div>
                  <div class="form-group text-left">
                  <div class="checkbox-inline">';
                  if($value['type']==1 || $value['type']==3)   { 
                   $display_question .='
                   <label class="checkbox checkbox-primary">
                   <input type="checkbox" name="allow_multiple_selections'.$qkey.'" id="allow_multiple_selections'.$qkey.'" '.$allow_multiple_selections_status.'/>
                   <span></span>
                   Allow multiple selections
                   </label>';
                 }
                 else{
                   $display_question .='<input type="hidden" name="allow_multiple_selections'.$qkey.'" id="allow_multiple_selections'.$qkey.'" value="1"/>';
                 }
                 $display_question .='
                 <label class="checkbox checkbox-primary">
                 <input type="checkbox" name="require_response'.$qkey.'" id="require_response'.$qkey.'" '.$require_response_status.'>
                 <span></span>
                 Require a Response
                 </label>
                 </div>
                 </div>' ;
                 if($value['type']==1 || $value['type']==3)   { 
                   $display_question .= '<div id="options'.$qkey.'">';
                   foreach ($options as $opkey => $opvalue) { 
                    $display_question .= ' <div id="optionlist'.$qkey.$opkey.'">
                    <div class="form-group row" id="'.$opkey.'">
                    <div class="col-lg-1"></div>
                    <div class="col-lg-8">
                    <div class="input-group input-group-button"> 
                    <input type="text" class="form-control display-inline width1" name="survey_question_option'.$qkey.'[]"  placeholder="Option" value="'.$opvalue.'">
                    <span class="input-group-text addon-style">
                    <label class="" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove Option"><i class="fa fa-trash close text-white" onclick="delnewoptionrows('."'".$opkey."'".','."'".$qkey."'".')"></i> </label>
                    </span>
                    </div>
                    </div>
                    </div>
                    </div>
                    ';

                  }
                  $display_question .= '</div>';
                  $display_question .= '<div class="form-group">
                  <input type="hidden" value="'.$opkey.'" id="option_row"/>
                  <button class="btn btn-success"  onclick="addnewoptionrows('."'".$qkey."'".')" id="addNewRows" type="button">+ Add More Options</button>
                  </div>';
                }  
                if($value['type']==2)   { 
                 $display_question .= '  <div class="form-group text-left mt-10">
                 <label>Scale from 1 to: <span class="text-danger">*</span></label>
                 <input type="number" name="survey_question_option'.$qkey.'[]"  class="form-control" placeholder="'.$options[0].'" value"'.$options[0].'" > 
                 </div>';
               }  
               if($value['type']==4)   { 
                 $display_question .= '  <input type="hidden" name="survey_question_option'.$qkey.'[]"  class="form-control" value=""> ';
               } 

               $display_question .= '</div> </div>  </div>';

             }
             echo $display_question;
           } ?> 
         </div>
       </div>
     </form>
   </div>
 </div>
 <div id="kt_modal_KTDatatable_setup" class="modal fade" role="dialog" aria-hidden="true">
   <div class="modal-dialog  modal-lg" >
    <div class="modal-content"  id="popup2">
    </div>
  </div>
</div>
