<?php
$_SESSION['apply_js']['multiselest'] = 1;
   $category = array('1' =>'Survey', '2'=> 'Poll');
   if ($_GET['CAT']=="" && $_GET['option']="COM_INSERT_SURVEY_SETUP") {
       $_GET['CAT'] =1;
    }
 $display_tag = $category[$_GET['CAT']];
 $table_list = array(TBL_SURVEY_SETUP,TBL_SURVEY_SETUP_QUESTIONS,TBL_SURVEY_SETUP_QUESTION_OPTIONS);
 if(isset($_GET['Rid']) && $_GET['Rid']!="") {
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

$vdata1["where"] = array('is_active'=>'0');
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=job_titles&data=".$vdata;
$configesponse = callAPI($vurl);
$job_titles_list = json_decode($configesponse,1);

$vdata1["where"] = array('is_active'=>'0','genc_code'=>'SURVEY_QTYPE');
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=general_code_details&data=".$vdata;
$configesponse = callAPI($vurl);
$qustion_types = json_decode($configesponse,1);

foreach ($qustion_types as $key => $value) {
 $question_types[$value['genc_detail_code']]  = $value['genc_detail_code'];
 $question_title[$value['genc_detail_code']]  = $value['genc_detail_name'];

}
if($row_id!='') {
   $vdata1["where"] = array('is_active'=>'0','id'=>$row_id);
   $vdata = urlencode(json_encode($vdata1));
   $vurl = API."?mode=gettabledetailswithconditions&table=job_feedback_setup&data=".$vdata;
   $configesponse = callAPI($vurl);
   $survey_details = json_decode($configesponse,1);
   $survey_details = $survey_details['0'];

   $vdata1["where"] = array('is_active'=>'0','job_feedback_setup_id'=>$row_id);
   $vdata = urlencode(json_encode($vdata1));
   $vurl = API."?mode=gettabledetailswithconditions&table=job_feedback_question_options&data=".$vdata;
   $configesponse = callAPI($vurl);
   $survey_option_details = json_decode($configesponse,1);

   $vurl = API."?mode=gettabledetailswithconditions&table=job_feedback_questions&data=".$vdata;
   $configesponse = callAPI($vurl);
   $survey_qustion_details = json_decode($configesponse,1);

   foreach ($survey_option_details as $option_data) {
 $answer[$option_data['question_id']] =  json_decode($option_data['feedback_question_option'],1);
}
foreach ($survey_qustion_details as $qdata) {
    $starray[] =array("options_display_type"=>$qdata['options_display_type'],"survey_question"=>$qdata['feedback_question'],"required_response"=>$qdata['required_response'],"allow_multiple_selections"=>$qdata['allow_multiple_selections'],"survey_question_option"=>$answer[$qdata['id']],'question_id'=>$qdata['id']);
}

   foreach ($survey_qustion_details as $qkey => $qvalue) {
      $qid = $qvalue['id'];
      $questions_list = array('title'=>$question_title[$qvalue['options_display_type']],'type'=>$qvalue['options_display_type'],'question'=>$qvalue['feedback_question'],'require_response'=>$qvalue['require_response'],'allow_multiple_selections'=>$qvalue['allow_multiple_selections'],'option_data'=>$answer[$qid],'rid'=>$row_id,'question_id'=>$qid);
      $questiondetails[$qid] = json_encode($questions_list,1);
   }
   $_SESSION['questions_list'] = $questiondetails;
}


if(isset($_POST['create'])){
   $survey_date= $_POST['survey_date'];
   $survey_start_date = date('Y-m-d',strtotime((substr($survey_date, 0, 10))));
   $survey_start_time = date('H:i:s',strtotime(substr($survey_date, 11, 8)));
   $survey_end_date = date('Y-m-d',strtotime((substr($survey_date, 22, 10))));
   $survey_end_time = date('H:i:s',strtotime(substr($survey_date, 33, 8)));

   $survy_setup_array = array('name'=>$_POST['survey_name'],'description'=>$_POST['description'],'job_title_id'=>$_POST['job_title_id']);

   $survy_setup_array['created_date'] = nowDate();
   $survy_setup_array['created_by'] = $_SESSION['userid'];
   $survy_setup_array['is_active'] = "0";


   $survy_setup_array['company_id'] = $_SESSION['company_id'];
   foreach ($_POST['value_id'] as $vkey => $i) {
      $survey_question='survey_question'.$i;
      $options_display_type='options_display_type'.$i;
      $question_id='question_id'.$i;
      $allow_multiple_selections='allow_multiple_selections'.$i;
      $require_response='require_response'.$i;
      $survey_question_option='survey_question_option'.$i;
      $required_ratting = 'required_ratting'.$i;
      $_POST[$allow_multiple_selections] = $_POST[$allow_multiple_selections]== 'on' ? '0' : '1';
      $_POST[$require_response] = $_POST[$require_response]== 'on' ? '0' : '1';
      $qustion[]=array('rid'=>$row_id,'question_id'=>$_POST[$question_id],'feedback_question'=>$_POST[$survey_question],'options_display_type'=>$_POST[$options_display_type],'required_response'=>$_POST[$required_ratting],'allow_multiple_selections'=>$_POST[$allow_multiple_selections],'feedback_question_option'=>json_encode($_POST[$survey_question_option],1));
   }
   $data['main'] = $survy_setup_array;
   $data['qustion'] = $qustion;
   if($_GET['Rid']==""){
     $url = API."?mode=createjobfeedback&table_setup=job_feedback_setup&table_questions=job_feedback_questions&table_options=job_feedback_question_options&method=post";
  }else{
     $url = API."?mode=createjobfeedback&table_setup=job_feedback_setup&table_questions=job_feedback_questions&table_options=job_feedback_question_options&method=post&rowid=".$row_id;
  }
  $retunn_survey_id = file_post_contents($url,$data,'insert');
   $return_response=json_decode($retunn_survey_id,1);
}

?>
<script src="assets/js/pages/myscrpit/bootstrap-daterangepicker.js"></script>
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
     alert(optionlistid)
     $("#options"+qid).append('<div id='+"'"+optionlistid+"'"+'> <div class="form-group row" id="'+"'"+op+"'"+'"><div class="col-lg-1"></div><div class="col-lg-8"><div class="input-group input-group-button "> <input aria-label="Option" type="text" name="survey_question_option'+qid+'[]"  placeholder="Option" class="form-control text-right width1"> <button class="btn btn-danger" type="button" onclick="delnewoptionrows('+"'"+op+"'"+','+"'"+qid+"'"+')"><i class="ti-trash"></i></button> </div></div></div>');
     document.getElementById('option_row').value = op ;
  }

  function delnewoptionrows(rowid,qid) {

   var optionlistid = "optionlist"+qid+rowid;
   $("#"+optionlistid).remove();
}
function dynamiceditpopup(type,title){
 jQuery.ajax({
  url: "views/custom_insert_feedback_add_question.php",
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


function getnamelist(emplist){
   console.log(emplist);
   document.getElementById('filterby').value = emplist;
   $('#kt_modal_dynamic_popup').modal('hide');
}

</script>

<div class="card card-custom card-sticky " id="kt_page_sticky_card">
    <div class="alert alert-warning alert-dismissible">
      <strong><?php echo "This function is used for maintaining <strong>Display Attributes</strong>. * represents mandatory fields."; ?></strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>

 <div class="card-header">
    <h5>Job Candidate Feed Back</h5>

    <div class="card-header-right">
             <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
    </div>
</div>

<div class="card card-custom j-content">
  <div class="card-body">
   <form method="post" action="">
      <div class="row">

         <div class="col-lg-6 px-4">
            <input type="hidden" id="filterby" name="filterby">
            <input type="hidden" name="survey_assigned_to" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>">
            <div class="card card-custom gutter-b">
               <div class="card-body">
                  <div class="form-group">
                     <label for="example-date-input-from" class="form-label font-weight-bolder">Job Titles</label>
                     <select aria-label="Job Title" class="form-control form-select js-example-basic-single" id="kt_select2_48" name="job_title_id">
                        <option> Select any one</option>
                        <?php foreach ($job_titles_list as $value) { ?>
                           <option value="<?php echo $value['id'];?>" <?php if($value['id'] == $survey_details['job_title_id']){ ?>selected <?php
} ?> ><?php echo $value['job_title']; ?></option>
                        <?php } ?>
                     </select>
                  </div>

                  <div class="">
                     <span class="font-weight-bolder form-label">Name <span class="text-danger">*</span></span>
                     <input aria-label="Survey Name" type="text" name="survey_name" value="<?php echo $survey_details['name'];?>" class="form-control" placeholder="Enter Title">

                  </div>
                  <div class="col-form-label">
                     <div class="mt-3">
                        <span class="font-weight-bolder form-label">Description <span class="text-danger">*</span></span>
                        <textarea aria-label="Enter Title" class="form-control" name="description" placeholder="Enter Title"> <?php echo $survey_details['description'];?> </textarea>
                     </div>
                  </div>
                  <div class="card-footer d-flex row">
                     <div class="col-lg-3">
                        <button type="submit" name="create" class="btn btn-primary font-weight-bold float-left"><?php echo $_GET['Rid']!='' ? 'Update' : 'Save'; ?></button>
                     </div>

                     <div class="col-lg-8 text-right">

                        <div class="btn-group">
                           <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Add Questions</button>
                           <div class="dropdown-menu">
                              <?php foreach ($qustion_types as $key => $value) { ?>
                              <button type="button" onclick="dynamiceditpopup('<?php echo $value['genc_detail_code']; ?>','<?php echo $value['genc_detail_name']; ?>')" class="dropdown-item">  <?php echo $value['genc_detail_name']; ?></button>
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
      <div class="col-lg-6 p-10" id="survey_questions">

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
            $display_question = '';
            $display_question .= ' <div class="mt-5" id="'.$question_id.'"> <div class="col-lg-12 table-bordered">
            <input type="hidden" name="totalquestion" value="'.$totalquestion.'">
            <input type="hidden" name="question_id'.$qkey.'" value="'.$question_id.'">
            <input type="hidden" name="rid" value="'.$rid.'">
            <input type="hidden" name="value_id[]" value="'.$question_id.'">
            <input type="hidden" name="options_display_type'.$qkey.'" value="'.$value['type'].'">

            <div class="modal-header" >
            <h5 class="modal-title" id="exampleModalLabel">'.$title.'</h5>
             <button type="button" class="close text-white border-0 bg-transparent p-0" data-dismiss="modal" aria-label="Close" onclick="deletequestion('."'".$question_id."'".')"><i class="ti-trash"></i></button>

            </div>
            <div class="card-body mt-3">
            <div class="form-group text-left">
            <span>Question Text: <span class="text-danger"></span></span>
            <textarea aria-label="Survey Question" id="survey_question'.$qkey.'" name="survey_question'.$qkey.'" class="form-control" placeholder=""> '.$value['question'].' </textarea>
            </div>
            <div class="form-group text-left">
            <div class="checkbox-inline">';
            if($value['type']==1 || $value['type']==3)   {
               $display_question .='
               <label class="checkbox checkbox-primary">
               <input aria-label="Allow Multiple Selections" type="checkbox" name="allow_multiple_selections'.$qkey.'" id="allow_multiple_selections'.$qkey.'" '.$allow_multiple_selections_status.'/>
               <span></span>
               Allow multiple selections
               </label>';
            }
            else{
               $display_question .='<input type="hidden" name="allow_multiple_selections'.$qkey.'" id="allow_multiple_selections_2'.$qkey.'" value="1"/>';
            }
            $display_question .='
            <label class="checkbox checkbox-primary">
            <input aria-label="Require Response" type="checkbox" name="require_response'.$qkey.'" id="require_response'.$qkey.'" '.$require_response_status.'>
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
        <input aria-label="Option" type="text" class="form-control display-inline width1" name="survey_question_option'.$qkey.'[]"  placeholder="Option" value="'.$opvalue.'">
        <span class="input-group-text addon-style">
            <span class=""><button type="button" class="border-0 bg-transparent p-0 text-white" onclick="delnewoptionrows('."'".$opkey."'".','."'".$qkey."'".')"><i class="ti-trash close"></i></button> </span>
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
               <span>Scale from 1 to: <span class="text-danger">*</span></span>
               <input aria-label="Survey Question Option" type="number" name="survey_question_option'.$qkey.'[]"  class="form-control" placeholder="'.$options[0].'" value="'.$options[0].'" >
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


<div id="kt_modal_KTDatatable_setup" class="modal fade" role="dialog" aria-hidden="true">
   <div class="modal-dialog  modal-lg" >
      <div class="modal-content j-pro"  id="popup2">
      </div>
   </div>
</div>
 <div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content"  id="popup1">

        </div>
    </div>
</div>
