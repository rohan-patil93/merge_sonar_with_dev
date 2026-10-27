   <?php
//id:3 => YVdRNk13PT0=

$category = array('1' =>'Survey', '2'=> 'Poll');

       $_GET['CAT'] =1;
$display_tag = "Exit interview";
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
$vurl = API."?mode=getsurveydetails&company_id=".$company_id."&emp_id=".$emp_id."&is_active=0&survey_id=".$row_id;
$configesponse = callAPI($vurl);
$employeeDetails = json_decode($configesponse,1);
$employee_list = $employeeDetails['employeedetails'];
$qustion_types = $employeeDetails['qustion_types'];
$survey_details = $employeeDetails['survey_details'];
$survey_qustion_details = $employeeDetails['survey_qustion_details'];
$survey_option_details = $employeeDetails['survey_option_details'];
$publish_status = $survey_details['publish_status'];
$filtertbl_rowid = $survey_details['survey_assigned_to'];
$allocated_time =  $survey_details['allocated_time'];

foreach ($survey_option_details as $option_data){
 $answer[$option_data['question_id']] =  json_decode($option_data['survey_question_option'],1);
}
foreach ($survey_qustion_details as $qdata) {
 $starray[] =array("options_display_type"=>$qdata['options_display_type'],"survey_question"=>$qdata['survey_question'],"require_response"=>$qdata['require_response'],"allow_multiple_selections"=>$qdata['allow_multiple_selections'],"survey_question_option"=>$answer[$qdata['id']],'question_id'=>$qdata['id']);
}

$who_can_see_id = json_decode($survey_details['who_can_see_the_results'],1);

foreach ($qustion_types as $key => $value) {
 $question_types[$value['genc_detail_code']]  = $value['genc_detail_code'];
 $question_title[$value['genc_detail_code']]  = $value['genc_detail_name'];

}
if($row_id!='') {
   foreach ($survey_qustion_details as $qkey => $qvalue) {
      $qid = $qvalue['id'];
      $questions_list = array('title'=>$question_title[$qvalue['options_display_type']],'type'=>$qvalue['options_display_type'],'question'=>$qvalue['survey_question'],'require_response'=>$qvalue['require_response'],'allow_multiple_selections'=>$qvalue['allow_multiple_selections'],'option_data'=>$answer[$qid],'rid'=>$row_id,'question_id'=>$qid);
      $questiondetails[$qid] = json_encode($questions_list,1);
   }
   $_SESSION['questions_list'] = $questiondetails;
}


if(isset($_POST['create'])){
   $survey_date= $_POST['survey_date'];

    $survey_start_date = date('Y-m-d',strtotime($_POST['survey_start_date']));
   $survey_start_time = "12:00:00";
   $survey_end_date = date('Y-m-d',strtotime($_POST['survey_end_date']));
   $survey_end_time = "12:00:00";


   $survy_setup_array = array('survey_name'=>$_POST['survey_name'],'survey_type'=>$_POST['survey_type'],'description'=>$_POST['description'],'survey_start_date'=> $survey_start_date,'survey_start_time'=>$survey_start_time,'survey_end_date'=>$survey_end_date,'survey_end_time'=>$survey_end_time,'survey_initiator'=>$_POST['survey_initiator'],'allocated_time'=>'0','survey_assigned_to'=>$_POST['survey_assigned_to'],'who_can_see_the_results'=>json_encode($_POST['who_can_see_the_results'],1));

   $survy_setup_array['created_date'] = nowDate();
   $survy_setup_array['created_by'] = $_SESSION['userid'];
   $survy_setup_array['is_active'] = "0";
   $survy_setup_array['publish_status'] = "1";
   $survy_setup_array['category'] = $_GET['CAT'];


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
      $qustion[]=array('rid'=>$_POST['rid'],'question_id'=>$_POST[$question_id],'survey_question'=>$_POST[$survey_question],'options_display_type'=>$_POST[$options_display_type],'require_response'=>$_POST[$require_response],'allow_multiple_selections'=>$_POST[$allow_multiple_selections],'survey_question_option'=>json_encode($_POST[$survey_question_option],1));

   }
   $data['main'] = $survy_setup_array;
   $data['qustion'] = $qustion;

   if($_GET['Rid']==""){
     $url = API."?mode=createsurveys&table_setup=".$table_list[0]."&table_questions=".$table_list[1]."&table_options=".$table_list[2]."&method=post";
  }
  else{
     $url = API."?mode=createsurveys&table_setup=".$table_list[0]."&table_questions=".$table_list[1]."&table_options=".$table_list[2]."&method=post&rowid=".$row_id;
  }

   $retunn_survey_id = file_post_contents($url,$data,'insert');
   $return_response=json_decode($retunn_survey_id,1);
   header("location:index.php?option=COM_LIST_SURVEY_SETUP&CAT=".$_GET['CAT']);


}

?>
<script src="assets/js/pages/myscrpit/bootstrap-daterangepicker.js"></script>
<script type="text/javascript">

   function deletequestion(qid){
      alert(qid);
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
     $("#options"+qid).append('<div id='+"'"+optionlistid+"'"+'> <div class="form-group row" id="'+"'"+op+"'"+'"><div class="col-lg-1"></div><div class="col-lg-8"><div class="input-group input-icon-right"> <input aria-label="Option" type="text" class="form-control display-inline"  name="survey_question_option'+qid+'[]" placeholder="Option" tabindex="0" onkeypress="if(event.key===\'Enter\'||event.key===\' \')this.click()"> <div class="input-group-append"><span class="input-group-text bg-primary"><span><button type="button" class="btn p-0 border-0 bg-transparent flaticon-delete text-white" onclick="delnewoptionrows('+"'"+op+"'"+','+"'"+qid+"'"+')" tabindex="0" onkeypress="if(event.key===\'Enter\'||event.key===\' \')this.click()"></button></span></span></div></div></div></div></div>');
     document.getElementById('option_row').value = op ;
  }

  function delnewoptionrows(rowid,qid) {
   var optionlistid = "optionlist"+qid+rowid;
   $("#"+optionlistid).remove();
}
function dynamiceditpopup(type,title){
 jQuery.ajax({
  url: "views/custom_insert_survey_addquestions.php",
  data:'type='+type+"&question_title="+title,
  type: "GET",
  success:function(data){
   $('#popup2').html(data);
   $("#kt_modal_KTDatatable_setup").modal("show");
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
        console.log(data);
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
<div class="flex-column-fluid">
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
            <div class="card-header-right table-card-header ">

                  <button type="button" class="btn waves-effect waves-light btn-inverse btn-outline-inverse"><i class="icofont icofont-reply"></i>Back</button>
            </div>
       </div>
<div class="card card-custom">
      <div class="card-body">
<form method="post" class="j-pro" action="">
   <div class="row">

      <div class="col-lg-6 px-4">
        <input type="hidden" id="filterby" name="filterby">
        <input type="hidden" name="survey_assigned_to" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>">
        <input type="hidden" name="survey_type" value="<?php echo EXITE_INTERVIEW_TYPE; ?>"  />
        <input type="hidden" name="survey_name" value="Exit Interview" class="form-control" placeholder="Enter Title">
            <?php
            $dateformat = strtotime("+12 Years",time());
            $startdate = date('Y-m-d');
            $enddate =  date('Y-m-d',strtotime('last day of this month', $dateformat));
            ?>
        <input type='hidden' class="form-control "  name="survey_start_date"  value="<?php echo $startdate; ?>" />
        <input type='hidden' class="form-control"  name="survey_end_date" value="<?php echo $enddate; ?>" />

    <div class="card card-custom gutter-b">
            <div class="card-body">
               <div class="form-group">
                     <div class="form-group mt-3">
                        <span class="font-weight-bolder"><?php echo $display_tag; ?> Description <span class="text-danger">*</span></span>
                        <textarea aria-label="Enter Title" class="form-control" name="description" placeholder="Enter Title"> <?php echo $survey_details['description'];?> </textarea>

                     </div>
                     <div class="form-group">
                        <label for="example-date-input-from" class="col-form-label font-weight-bolder"><?php echo $display_tag; ?> Initiator</label>
                        <select aria-label="Survey Initiator" class="form-select js-example-basic-single" id="kt_select2_48" name="survey_initiator">
                           <option> Select any one</option>
                           <?php $survey_init =  $survey_details['survey_initiator']!="" ? $survey_details['survey_initiator'] :$_SESSION['emp_id'];
                           foreach ($employee_list as $value) { ?>
                              <option value="<?php echo $value['id'];?>" <?php if($value['id'] == $survey_init){ ?>selected <?php
} ?> ><?php echo $value['display_name']; ?></option>
                           <?php } ?>
                        </select>
                     </div>
                     <div class="form-group">
                        <span class="font-weight-bolder">Asign to <span class="text-danger">*</span></span>
                        <div id="tsk_fortable"></div>
                        <div class="col-xl-12">
                           <i class="flaticon2-group text-success"></i>
                           <button type="button" onclick="showpopup();">Edit </button>
                        </div>
                     </div>
                     <div class="form-group">
                        <label for="example-date-input-to" class="col-form-label font-weight-bolder">Who can see the results</label>
                        <select aria-label="Who Can See The Results[]" class="form-select js-example-basic-multiple" id="kt_select2_43" name="who_can_see_the_results[]" multiple="multiple">
                           <?php foreach ($employee_list as $value) { $key=$value['id']; ?>
                           <option <?php if(in_array($key,(array)$who_can_see_id)){ ?> selected <?php
} ?> value="<?php echo $key;?>"><?php echo $value['display_name']; ?></option>
                        <?php } ?>
                     </select>

                  </div>
               </div>
               <div class="card-footer d-flex row">

                  <div class="col-lg-3">
                     <button type="submit" name="create" class="btn btn-primary font-weight-bold float-left"><?php echo $_GET['Rid']!='' ? 'Update' : 'Save'; ?></button>
                  </div>

                  <div class="col-lg-6 text-right">

                     <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Add Questions</button>
                        <div class="dropdown-menu">
                            <?php  foreach ($qustion_types as $key => $value) { ?>
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
          foreach ($exitinterview_question_details as $value) {
            $options = explode(',', $value['options']);
            $allow_multiple_selections_status = $value['allow_multiple_selections']==1 ? 'checked' : '';
            $require_response_status = $value['require_response']==1 ? 'checked' : '';
            $question_id = $value['question_id'];
            $qkey = $question_id;
            $title = $value['title'];
            $rid = $value['rid'];
            $display_question = '';
            $display_question .= ' <div class="mt-5" id="'.$qkey.'"> <div class="col-lg-12 table-bordered">
            <input type="hidden" name="question_id'.$qkey.'" value="'.$question_id.'">
            <input type="hidden" name="rid" value="'.$rid.'">
            <input type="hidden" name="value_id[]" value="'.$question_id.'">
            <input type="hidden" name="options_display_type'.$qkey.'" value="'.$value['type'].'">

            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">'.$title.'</h5>
            <button type="button" class="btn p-0 border-0 bg-transparent fa fa-trash close text-white" data-dismiss="modal" aria-label="Close" onclick="deletequestion('."'".$question_id."'".')" tabindex="0" onkeypress="if(event.key===\'Enter\'||event.key===\' \')this.click()"></button>
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
            <span class=""><button type="button" class="btn p-0 border-0 bg-transparent fa fa-trash close text-white" onclick="delnewoptionrows('."'".$opkey."'".','."'".$qkey."'".')" tabindex="0" onkeypress="if(event.key===\'Enter\'||event.key===\' \')this.click()"></button> </span>
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
               <input aria-label="Survey Question Option" type="number" name="survey_question_option'.$qkey.'[]"  class="form-control" placeholder="'.$options[0].'" value"'.$options[0].'" >
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
