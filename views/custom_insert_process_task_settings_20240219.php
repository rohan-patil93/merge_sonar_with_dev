

<?php
//print_r($_GET);
if(isset($_GET['popup'])){
  include('../includes/session.php');
  include('../dbconfig.php');
  include('../comm.php');
}
if(isset($_GET['Rid']) && $_GET['Rid']!="") {
  $rid =  encodeId($_GET['Rid']);
  $rid = urldecode($rid);
  $jrid = explode(":",$rid);
  $row_id = $jrid['1'];//base64_decode($_GET['Rid']); 
}
$company_id = $_SESSION['company_id'];
$url = API."?mode=gettasksdetails&company_id=".$company_id."&is_active=0";
               //  echo $url; 
   $response = callAPI($url);
   $tasks_details = json_decode($response,1);
   $period_list = $tasks_details['period_list'];
   $due_list = $tasks_details['due_list'];
   $buddy_list = $tasks_details['buddy_list'];
   $task_list = $tasks_details['task_list'];
   $emp_list = $tasks_details['emp_list'];
   $application_pages = $tasks_details['application_pages'];
  // print_r($application_pages);
   $buddy_keys = array_keys((array)$buddy_list['genc_detail_code']);
   $wherecon["where"] = array('is_active'=> '0','company_id'=>$company_id,'id'=>$row_id);
   $data_con = urlencode(json_encode($wherecon));
   $url = API."?mode=gettabledetailswithconditions&table=process_task_settings&data=".$data_con; 
   $response = callAPI($url);
   $display_data = json_decode($response,1);
   $id = $display_data[0]['id']; 

   $task_name = $display_data[0]['task_name']; 
   $assigned_to = $display_data[0]['assigned_to'];
   $main_data = $display_data[0];
  //  echo "<pre>"; print_r($main_data); exit;
   if($row_id!=""){   
    $where2con["where"] = array('is_active'=> '0','company_id'=>$company_id,'transaction_id'=>$row_id);
   $data2_con = urlencode(json_encode($where2con));
   $url2 = API."?mode=gettabledetailswithconditions&table=filter_tables&data=".$data2_con; 
   $response2 = callAPI($url2);
   $filte_data = json_decode($response2,1);
   $filter_id = $filte_data[0]['id']; 
 }

 $whereemal["where"] = array('is_active'=> '0','company_id'=>$company_id);
   $emailcon = urlencode(json_encode($whereemal));
   $emailurl = API."?mode=gettabledetailswithconditions&table=email_content&data=".$emailcon; 
   $emaildata = callAPI($emailurl);
   $emaildata = json_decode($emaildata,1);
   //$emaildata = $emaildata[0]; 

 
    //print_r($emaildata);
    if(!in_array($assigned_to,(array)$buddy_keys)) {
      $assigned_to = OPTION_OTHERS;
      $emp_id =$display_data[0]['assigned_to'];
    }
    $process_id = $display_data[0]['process_id']; 
    $no_action = $display_data[0]['no_action']; 
    $on_the_day = $display_data[0]['on_the_day']; 
    $action_no = $display_data[0]['action_no']; 
    $action_frequency = $display_data[0]['action_frequency']; 
    $before_or_after = $display_data[0]['before_or_after']; 
    $description = $display_data[0]['description']; 
    $files = $display_data[0]['files']; 
    $display_color = $display_data[0]['display_color']; 
    $display_background_color = $display_data[0]['display_background_color'];
    $remarks = $display_data[0]['remarks']; 
    $task_for = $display_data[0]['task_for'];  
    
/*
    if (isset($_POST['submit'])){
       $_POST['created_date'] = nowDate();
       $_POST['created_by'] = $_SESSION['userid'];
       $_POST['current_status'] = 0;
        if($_POST['due_date']=="no_action") {
          $_POST['no_action'] = '1';
        }else if($_POST['due_date']=="on_the_day") {
          $_POST['on_the_day'] = '1';
        }
        $task_list = $tasks_details['task_list'][$_POST['process_id']];
        $_POST['display_color'] = $task_list['display_color'];
        $_POST['display_background_color'] = $task_list['display_background_color'];
        $_POST['row_id']=$row_id;
        foreach ($_POST['checklist'] as  $key=>$value) {
         if($value!=""){
           $listdata[$key]['name'] = $value;
           $listdata[$key]['check_value'] = "";
         }
        }

        $_POST['check_list'] = json_encode($listdata,1);
	      $url = API."?mode=processtasksettings&method=post";
        $response = file_post_contents($url,$_POST,'insert');
        echo $response; //exit;
       // header("location:index.php?option=COM_LIST_PROCESS_TASK_SETTINGS");
    }

*/

    $pageurl = API."?mode=displayattributesview&fid=application_table_name&view=process_task_settings";
    $page_response = callAPI($pageurl);
    $list = json_decode($page_response,1);
    $content = $list['help_text'] != '' ? $list['help_text'] : "Help Text"; 
    $title = viewTitle($_GET['option']);
    $dataedu["where"] = array('is_active'=> '0','genc_code'=>'TASKPROCESSTYPE');
    $costdataedu = urlencode(json_encode($dataedu));
    $eduurl = API."?mode=gettabledetailswithconditions&table=general_code_details&data=".$costdataedu;
    $edutypedata = callAPI($eduurl);
    $processtypedata = json_decode($edutypedata,1);


    $wh5["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
    $wh5["field"] = "id,module_id,module_name";
    $data5 = urlencode(json_encode($wh5));
    $url5 = API."?mode=gettabledetailswithconditions&table=module_setup&data=".$data5; 
    $mopdule_setup = callAPI($url5);
    $mopdulesetup = json_decode($mopdule_setup,1);
?>
<script type="text/javascript">
  function showemplist(value) {
     if(value=='OB') {
      $("#emplistshow").show('slow');
      //  document.getElementById('emplistshow').style.display ='block';
     } else {
      $("#emplistshow").hide('slow');
      // document.getElementById('emplistshow').style.display ='none';
     }
  }
  function showemplist1(value) {
     if(value=='OB') {
      $("#emplistshowapprovaltype").show('slow');
       // document.getElementById('emplistshowapprovaltype').style.display ='block';
     } else {
      $("#emplistshowapprovaltype").hide('slow');
       //document.getElementById('emplistshowapprovaltype').style.display ='none';
     }
  }
  
 

   function showpopup(popup_function_name){

      if(popup_function_name=="newtask"){
         var filter_id = document.getElementById('filtertbl_rowid').value;
         var process_type = document.getElementById('process_type').value;
         
       jQuery.ajax({
            url: "views/custom_insert_filters.php",
            data:'filter_id='+filter_id+'&process_type='+process_type,
            type: "GET",
            success:function(data){
                $('#dynamic_popup_task_settings').html(data);
               // $('#kt_modal_dynamic_popup').modal("show");
                $("#kt_modal_dynamic_popup_task_setting").modal("show");
              //  $(".updaterolemodal").modal("show");
            },
            error:function (){}
        });
     }
     if(popup_function_name=="addfiles"){
       var files = document.getElementById('files').value;
       var sendfile = files;
       jQuery.ajax({
            url: "views/newtasks_addfiles.php",
            data:'filelist='+sendfile,
            type: "GET",
            success:function(data){
                $('#dynamic_popup_task_settings').html(data);
               // $('#kt_modal_dynamic_popup').modal({ show: true});
                 $("#kt_modal_dynamic_popup_task_setting").modal("show");
              
            },
            error:function (){}
        });
     }
  }

function getfile()
{

  var selectedfileslist = document.getElementById('files').value;
  console.log("************************"+selectedfileslist);

   $.ajax({
     url: "ajax.php",
       data:'page=selectedfiles&files='+selectedfileslist,
       type: "POST",
       success: function(response) {
         console.log("response ="+response);
          var division = document.getElementById('divshowfiles');
           division.innerHTML = response;
      }
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
function deleteRows(val){
   // alert(val);
    // $("#static_div_"+val).css("display", "none");
    // $("#static_delete_value_"+val).val('1');

    $("#static_div_"+val).remove();
   // $("#static_delete_value_"+val).val('1');
  }
function addstatickRows123(){
    var count = $(".sfitemRow").length;
    //alert('la-hand-o-right'+count);
    count++;
    var htmlRows = '';    
    htmlRows += '<div class="row align-items-center sfitemRow" id="static_div_'+count+'" style="margin-top: 10px;">';
    htmlRows += '<div class="col-lg-5">';
    htmlRows += '<input type="text" class="form-control"  id="static_value_'+count+'"  value=""  name="checklist[]">';
    htmlRows += '</div>';

    htmlRows +='<div class="col-lg-1 text-center"><input type="hidden" name="checkboxid[]" value="'+count+'"><div class="checkbox-fade fade-in-primary"> <label class="form-label"> <input type="checkbox" value="Y" name="attachment_requerd['+count+']" ><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span></label></div></div>';

    htmlRows +='<div class="col-lg-1 text-center"><div class="checkbox-fade fade-in-primary"> <label class="form-label"> <input type="checkbox" value="Y" name="mandatory_checklist['+count+']" ><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span></label></div></div>';

      htmlRows +='<div class="col-lg-3 text-center"> <div class="row"><div class="col-lg-11"><input type="file" name="dyanamic_file" class="form-control" ></div></div></div>';
   /* htmlRows += '<div class="col-lg-3">';
    htmlRows += '<input type="text"class="form-control"  id="static_lable_'+count+'"  value=""  name="type"> ';
    htmlRows += '</div>';
    */
    htmlRows += '<div class="col-lg-2">';               
    htmlRows += ' <input type="hidden" value="0" id="static_delete_value_'+count+'" ><button class="btn btn-warning" id="'+count+'" type="button" onclick="deleteRows(this.id)">Delete</button>';           
    htmlRows += '</div>';
    htmlRows += '</div>';
    $('#append_statick').append(htmlRows);
  }
</script>

<script>
  var pro = $("#process_type").val();
  processtylelabel(pro);
 function processtylelabel(value){
if(value == "O" || value == "I"){
  $(".labelprocess").text("Joining Date");
}else if(value == "E"){ $(".labelprocess").text("Leaving Date"); }else{ $(".labelprocess").text(""); }
}




function submittasksettings(){
   var formData = new FormData();
    formData.append("page", "insertprocesstasksettings"); 
    var dataArray = $("#kt_form_submits").serializeArray(),
    dataObj = {};
    $(dataArray).each(function(i, field){
      formData.append(field.name, field.value); 
    });
    $.each($("input[name=dyanamic_file]"), function(i, obj) {
        $.each(obj.files,function(j, file){
            formData.append('file'+i, file);
        })
    });
    jQuery.ajax({
        url: "ajax.php",
        data: formData,
        type: "POST",
        contentType: false,
        processData: false,
        success:function(response){
          console.log(response);
          <?php  if(isset($_GET['popup'])){ ?>
            $('#kt_modal_KTDatatable_remotem_task').modal("hide"); 
             $('#kt_modal_dynamic_popup').modal("hide");   
            custompopups('<?php echo $_GET['type']; ?>',<?php echo $_GET['workflowid']; ?>);
          <?php }else{ ?>
              location.reload();
              window.location = "index.php?option=COM_LIST_PROCESS_TASK_SETTINGS";

         <?php  } ?>

        }
    });

  // jQuery.ajax({
  //       url: "ajax.php",
  //       data:formdata+'&page=insertprocesstasksettings',
  //       type: "POST",
  //       success:function(data){
  //         //console.log(data);
  //        <?php   if(isset($_GET['popup'])){ ?>
  //         $('#kt_modal_KTDatatable_remotem_task').modal("hide"); 
  //          $('#kt_modal_dynamic_popup').modal("hide");   
  //         custompopups('<?php echo $_GET['type']; ?>',<?php echo $_GET['workflowid']; ?>);
  //       <?php }else{ ?>
  //           location.reload();
  //      <?php  } ?>
  //       },
  //       error:function (){}
  //     });
}

function pageChange(page){
  console.log(page);
  $("#page_url").val("index.php?OPTION="+page);
}
function checkPageInternalLink(){
	 if($("#internal_link").is(':checked')){
	 		console.log("ok");	
	 		$(".internallinkclass").show('slow');	
	 }else{
	 	console.log("no");
	 		$(".internallinkclass").hide();	
	 }
}


function checkPageRedirect(){
    if($("#link_page").is(':checked')){
      $(".hidenoturl").hide('slow');
      $(".hideurl").show('slow');
    }else{
      $(".hideurl").hide('slow');
      $(".hidenoturl").show('slow');
    }
}

</script>
<style>
  .radio-inline .form-label input[type="radio"]{
     display: none;
  }
</style>
<div class="">
  <div class="card" >
    <?php /** if($content !='' && $_POST['option']==''){ ?>
   <!--  <div class="alert alert-custom alert-white shadow-lg fade show mb-5" role="alert">
        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
        <div class="alert-text"></div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div> -->
     <div class="alert alert-warning alert-dismissible">
      <strong><?php echo $content; ?></strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
    <?php } **/ if($_GET['error'] !=''){ ?>
  <!--   <div class="alert alert-danger fade show" role="alert">
      <div class="alert-icon"><i class="flaticon-warning"></i></div>
      <div class="alert-text"> </div>
      <div class="alert-close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="la la-close"></i></span>
        </button>
      </div>
    </div> -->
     <div class="alert alert-danger alert-dismissible">
      <strong> <?php echo $_SESSION['error_message'][$_GET['error']]; ?> </strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
    <?php } ?>
      <div class="card card-custom <?php echo $card_sticky; ?> j-pro" id="kt_page_sticky_card ">
        <!-- <div class="card-header">
          <div class="card-title">
            <h3 class="card-label">&nbsp;<?php echo $title;  ?> </h3>
          </div>
          <div class="card-toolbar">
           
          </div>
        </div>
 -->
  <div class="card-header">
    <h5><?php echo $title;  ?></h5>
    <div class="card-header-right">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
        </div>
</div>




        <div class="card-body ">
          <div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="step-first" data-wizard-clickable="true">
            <div class="row ">
              <div class="col-xl-12">
                <form class="form" id="kt_form_submits" method="post" action="" enctype="multipart/form-data">

                   <input type="hidden" name="row_id"  value="<?php echo $row_id; ?>">
                   <input type="hidden" name="Rid_id"  value="<?php echo $row_id; ?>">
                  <input type="hidden" name="task_for" id="filter" value="<?php echo urlencode($task_for); ?>"><!-- 
                  <input type="hidden" name="emp_count" id="emp_count" value="<?php //echo urlencode($emp_count); ?>"> -->
                  <input type="hidden" name="files" id="files" value="<?php echo urlencode($main_data['files']); ?>">
                  <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filter_id; ?>">
                  <div class="p-4" data-wizard-type="step-content" data-wizard-state="current">
                  <!--   <h4 class="mb-10 font-weight-bold text-dark">Tasks</h4> -->
                    <div class="row">
                      <div class="col-lg-5 mb-1 ">
                        <label class="form-label">Task Name *</label>
                        <input type="text" class="form-control" name="task_name" value="<?php echo $task_name; ?>" />
                      </div>
                      <?php  if($row_id ==""){ ?>
                        <div class="form-group col-lg-4 w-full">
                          <label class="form-label">Module</label>
                          <select class="form-select js-example-basic-multiple" id="module_setup_id" name="module_setup_id[]" multiple>
                            <?php foreach($mopdulesetup as $modulesetup){ ?>
                              <option value="<?php echo $modulesetup['module_id']; ?>" <?php echo $jsondecode3['module_setup_id'] == $modulesetup['module_id']?"selected":""; ?>><?php echo $modulesetup['module_name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <?php }else{ ?>
                          <div class="form-group col-lg-4 w-full">
                          <label class="form-label">Module</label>
                          <select class="form-select js-example-basic-single" id="module_setup_id" name="module_setup_id">
                            <option value="">Select Any One</option>
                            <?php foreach($mopdulesetup as $modulesetup){ ?>
                              <option value="<?php echo $modulesetup['module_id']; ?>" <?php echo $jsondecode3['module_setup_id'] == $modulesetup['module_id']?"selected":""; ?>><?php echo $modulesetup['module_name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        
                        <?php } ?>  
                      <div class="col-lg-9 mt-2 mb-1">
                        <label class="form-label"> Assign To</label>
                       <div class="form-radio">
                          <?php
                          foreach ($buddy_list as $emptypes) {
                            ?>
                            <div class="radio radio-inline">
                              <label class="form-label">
                              <input type="radio" name="assigned_to" style="display: none;"  onclick="showemplist(this.value);" value="<?php echo $emptypes['genc_detail_code'];?>" <?php if($main_data['assigned_to'] == $emptypes['genc_detail_code']) {?> checked="checked" <?php } ?>  />
                              <i class="helper"></i>
                              <?php echo $emptypes['genc_detail_name'];?>
                            </label>
                          </div>
                          <?php } ?>
                        </div>
                      </div>
                      <?php if($main_data['assigned_to']!='OB'){ $csd = "style='display: none'"; }?>

                      <div <?php echo $csd; ?>  id="emplistshow" class="col-xl-4 w-full">
                        <label class="form-label">Employee</label>
                        <select name="other_id" class="form-control form-select js-example-basic-single" id="fdrf">
                          <option value="">Select Any One </option>
                          <?php foreach ($emp_list as $emp) { ?>
                            <option value="<?php echo $emp['id'];?>" <?php if($main_data['user_id'] == $emp['id']){ ?>selected <?php } ?> ><?php echo $emp['display_name'];?></option>
                          <?php } ?>
                        </select> 
                      </div>

                      <div class="col-lg-9 mt-2 mb-1">
                        <label class="form-label"> Approval Type</label>
                       <div class="form-radio">
                          <?php
                          foreach ($buddy_list as $emptypes) {
                            ?>
                            <div class="radio radio-inline">
                              <label class="form-label">
                              <input type="radio" name="approval_type" style="display: none;"  onclick="showemplist1(this.value);" value="<?php echo $emptypes['genc_detail_code'];?>" <?php if($main_data['approval_type'] == $emptypes['genc_detail_code']) {?> checked="checked" <?php } ?>  />
                              <i class="helper"></i>
                              <?php echo $emptypes['genc_detail_name'];?>
                            </label>
                          </div>
                          <?php } ?>
                        </div>
                      </div>
                      <?php if($main_data['approval_type']!='OB'){ $csd = "style='display: none'"; }?>

                      <div <?php echo $csd; ?>  id="emplistshowapprovaltype" class="col-xl-4 w-full mb-2">
                        <label class="form-label">Employee</label>
                        <select name="approval_other_id" class="form-control form-select js-example-basic-single">
                          <option value="">Select Any One </option>
                          <?php foreach ($emp_list as $emp) { ?>
                            <option value="<?php echo $emp['id'];?>" <?php if($main_data['approval_other_id'] == $emp['id']){ ?>selected <?php } ?> ><?php echo $emp['display_name'];?></option>
                          <?php } ?>
                        </select> 
                      </div>


                      <div class="col-lg-9">
                        <div class="row ">
                        <div class="col-lg-6">
                          <label class="form-label">Email Template</label>
                          <select name="email_content_id" class="form-select js-example-basic-single" id="email_template">
                            <option value="">Select Any One </option>
                            <?php foreach ($emaildata as $email) {  ?>
                              <option value="<?php echo $email['id'];?>" <?php if($main_data['email_content_id'] == $email['id']){ ?>selected <?php } ?> ><?php echo $email['template_name'];?></option>
                            <?php } ?>
                          </select>
                        </div> 
                        <?php if(isset($_GET['process_type'])){?>
                          <input type="hidden" name="process_type" id="process_type" value="<?php echo $_GET['process_type']; ?>">
                         <?php }else{ ?> 
                        <div class="col-lg-6">
                          <label class="form-label">Process Type</label>
                            <select name="process_type" class="form-control form-select js-example-basic-single" id="process_type" onchange="processtylelabel(this.value)">
                              <option value="">Select Any One </option>
                              <?php foreach ($processtypedata as $processtype) {  ?>
                                <option value="<?php echo $processtype['genc_detail_code'];?>" <?php if($main_data['process_type'] == $processtype['genc_detail_code']){ ?>selected <?php } ?> ><?php echo $processtype['genc_detail_name'];?></option>
                              <?php } ?>
                            </select>
                          </div> 
                      <?php } ?>
                      </div>
                    </div>
                      
                      <div class="col-lg-9 mt-3 mt-1">
                        <label>Due Date</label></br>
                        <div class="form-radio">
                      <div class="radio radio-inline">
                          <label class="form-label">
                            <input type="radio" name="due_date" <?php if($main_data['no_action'] =='1') {?> checked="checked" <?php } ?>  value="no_action"/>
                            <i class="helper"></i>None
                          </label>
                        </div>
                        <div class="radio radio-inline">
                            <label class="form-label">
                            <input type="radio" name="due_date" <?php if($main_data['on_the_day']=='1') {?> checked="checked" <?php } ?> value="on_the_day"/>
                           <i class="helper"></i>On Date
                          </label>
                      </div>

                      <div class="radio radio-inline">
                          <label class="form-label">
                            <input type="radio" name="due_date" <?php if($main_data['transaction_date']=='1') {?> checked="checked" <?php } ?> value="transaction_date"/>
                           <i class="helper"></i>Transaction date
                          </label>
                      </div>
                      <div class="radio radio-inline">
                          <label class="form-label">
                            <input type="radio" name="due_date" <?php if($main_data['approval_date']=='1') {?> checked="checked" <?php } ?> value="approval_date"/>
                           <i class="helper"></i>Approval Date
                          </label>
                      </div>

                    </div></br>
                     
                      <div class="mb-3 row">
                          <div class="col-lg-4 col-md-3 col-sm-3 col-12 m-b-10" style="display: inherit;">
                          <div class="form-radio">
                            <div class="radio radio-inline">
                               <label class="form-label">
                                <input type="radio" name="due_date" <?php if($main_data['no_action'] !='1' && $main_data['on_the_day'] !='1') {?> checked="checked" <?php } ?> value="frequency"/> <i class="helper"></i>
                              </label>

                            </div>
                          </div>
                         <input type="text" class="form-control mr-2" style=""  name="action_no" value="<?php echo $main_data['action_no'];?>" />
                     
                    </div>
    
                      <div class="col-lg-4 col-md-3 col-sm-3 col-12 m-b-10 w-full">
                         <select name="action_frequency"  style="width:150px;" class="form-control form-select js-example-basic-single">
                          <option value="">Select</option>
                          <?php foreach ($due_list as $due) {    ?>
                            <option value="<?php echo $due['genc_detail_code'];?>" <?php if($main_data['action_frequency'] == $due['genc_detail_code']){ ?>selected <?php } ?> ><?php echo $due['genc_detail_name'];?></option>
                          <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-3 col-sm-3 col-12 m-b-10 w-full">
                        <select name="before_or_after" style="width:150px;" class="form-control form-select js-example-basic-single">
                          <option value="">Select</option>
                          <?php foreach ($period_list as $due) {  ?>
                            <option value="<?php echo $due['genc_detail_code'];?>" <?php if($main_data['before_or_after'] == $due['genc_detail_code']){ ?>selected <?php } ?> ><?php echo $due['genc_detail_name'];?></option>
                          <?php } ?>
                        </select> 
                    </div>
              </div>
                    </div>
                  </div>
                <div class="col-lg-9">
                  <div class="row mt-2 mb-1">
                    <label class="form-label col-lg-3">Category</label>
                    <div class="col-lg-9">
                      <select name="process_id" class="form-control form-select js-example-basic-single">
                        <option value="">Select Any One </option>
                        <?php
                        foreach ($task_list as $task) {
                          ?>
                          <option value="<?php echo $task['id'];?>" <?php if($main_data['process_id'] == $task['id']){ ?>selected <?php } ?> ><?php echo $task['category_name'];?></option>
                        <?php } ?>
                      </select>
                    </div> 
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-9 mt-2 mb-1">
                    <label class="form-label">Description</label>
                    <textarea style="overflow:auto" class="form-control form-control-lg" name="description" > <?php echo $main_data['description']; ?> </textarea>
                  </div>
                </div>
                <?php //print_r($main_data); ?>

 <div class="col-lg-9  mt-2 mb-1">
                  <div class="row mb-2">
                    <div class="col-lg-12">
                      <label class="form-label d-block">Link Page (if User Wants To Open Any Custome page Or URL Select This Option)</label>
                      <input type="checkbox" class="js-switch" value="0" id="link_page" name="link_page" onchange="checkPageRedirect()" <?php echo $main_data['link_page'] =='0'?"checked":""; ?>>
                    </div>
                  </div>

                  <div class="row hideurl" <?php echo $main_data['link_page'] =='0'?"":"style='display:none'"; ?>>
                    <div class="col-lg-4">
                      <label class="form-label d-block">Internal Link</label>
                      <input type="checkbox" class="js-switch" value="0" id="internal_link" name="internal_link" onchange="checkPageInternalLink()" <?php echo $main_data['link_page'] =='0'?"checked":""; ?>>
                    </div>


                      <div class="col-lg-4 w-full internallinkclass">
                        <label class="form-label d-block">Internal Page</label>
                        <select class="form-control form-select js-example-basic-single" onchange="pageChange(this.value)">
                          <option value="">Select Any One </option>
                          <?php
                          foreach ($application_pages as $applicationpages) {
                            ?>
                            <option value="<?php echo $applicationpages['insert_page_name'];?>"  ><?php echo $applicationpages['page_title'] !=""?$applicationpages['page_title']:ucwords(str_replace("_"," ",($applicationpages['application_table_name'])));?></option>
                          <?php } ?>


                        </select>
                      </div>
                   <!--    <div class="col-lg-4 w-full internallinkclass">
                        <label class="form-label d-block">Internal Field</label>
                        <select class="form-control form-select js-example-basic-single" onchange="pageChange(this.value)">
                          <option value="">Select Any One </option>
                          <?php
                          foreach ($application_pages as $applicationpages) {
                            ?>
                            <option value="<?php echo $applicationpages['insert_page_name'];?>"  ><?php echo $applicationpages['page_title'] !=""?$applicationpages['page_title']:ucwords(str_replace("_"," ",($applicationpages['application_table_name'])));?></option>
                          <?php } ?>


                        </select>
                      </div> -->
                      <div class="col-lg-12 mt-2 mb-1">
                        <label class="form-label d-block">Page URL</label>
                        <textarea  class="form-control form-control-lg" id="page_url" name="page_url" > <?php echo $main_data['page_url']; ?> </textarea>
                      </div>
                  </div>
                </div>



                <div class="row hidenoturl" <?php echo $main_data['link_page'] !='0'?"":"style='display:none'"; ?>>
                  <div class="col-lg-6 mt-2">
                    <label class="form-label">Attach File(s)</label>
                    <a href="#" class="text-primary" onclick="showpopup('addfiles');">Add</a>
                    <div id="divshowfiles">
                      <?php $files_datas = json_decode($main_data['files'],1);
                            if(count((array)$files_datas)>0){ 
                              foreach($files_datas as $filess){?>
                            <div class='ml-10 f-14' ><?php echo $filess; ?></div>
                          <?php } } ?>
                    </div>
                </div>
              </div>
              <div class="row hidenoturl" <?php echo $main_data['link_page'] !='0'?"":"style='display:none'"; ?>>
                <div class="col-lg-6 mt-3">
                   <label class="form-label">This is required for...</label>
                </div>
                <div id="tsk_fortable"></div>
                  <div class="col-lg-12">
                      <i class="flaticon2-group text-success"></i>
                      <a href="#" class="text-primary" onclick="showpopup('newtask');">Edit </a>
                  </div>
                </div>
                <?php //print_r($main_data);?>
                <div class="col-lg-12 mt-3 hidenoturl" <?php echo $main_data['link_page'] !='0'?"":"style='display:none'"; ?>>
                  <div id="append_statick" class="">
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-lg-5">
                          <label class="form-label">Check List</label>
                        </div>
                        <div class="col-lg-1">
                          <label class="form-label text-center">Mandatory</label>
                        </div>
                        <div class="col-lg-1">
                          <label class="form-label text-center">Attachment</label>
                        </div>
                        <div class="col-lg-3">
                          <label class="form-label">Download Form</label>
                        </div>
                        <div class="col-lg-2">
                          <label>Action</label>
                        </div>
                    </div>
                    <?php $checklist_data = json_decode($main_data['check_list'],1);
                      
                      //if(count((array)$checklist_data)>0){ continue; }else{ $checklist_data = array('name'=>""); }
                      $m=1;
                      if(count((array)$checklist_data) == 0){ $checklist_data[] = array("name"=>""); }
                      foreach ($checklist_data as $checkvalue) {
                       //print_r($checkvalue);
                    ?>
                    <div class="row sfitemRow mt-1" id="static_div_<?php echo $m; ?>">
          <div class="col-lg-5">
              <input type="text" class="form-control"  id="static_value_<?php echo $m; ?>"  name="checklist[]" value="<?php echo $checkvalue['name']; ?>"> 
          </div>
          
          <div class="col-lg-1 text-center">
                <div class="checkbox-fade fade-in-primary">
                 <label class="form-label mt-2">
                  <input type="checkbox" value="Y" name="mandatory_checklist[<?php echo $m; ?>]" <?php echo $checkvalue['mandatory_checklist'] == "Y"?"checked":""; ?>>
                  <span class="cr">
                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                  </span>
                </label>
              </div>
          </div>
          <div class="col-lg-1 text-center">
            <input type="hidden" name="checkboxid[]" value="<?php echo $m; ?>">
                <div class="checkbox-fade fade-in-primary">
                 <label class="form-label mt-2">
                  <input type="checkbox" value="Y" name="attachment_requerd[<?php echo $m; ?>]" <?php echo $checkvalue['attachment_requerd'] == "Y"?"checked":""; ?>>
                  <span class="cr">
                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                  </span>
                </label>
              </div>
          </div>
          <div class="col-lg-3 text-center">
            <div class="row">
              <div class="col-lg-11">
                <input type="file" name="dyanamic_file" class="form-control" >
                <input type="hidden" name="hidden_file[]" value="<?php echo $checkvalue['downloadfile']; ?>">
              </div>
              <div class="col-lg-1">
                <?php if($checkvalue['downloadfile'] !=""){ ?>
               <a href="uploads/all/<?php echo $checkvalue['downloadfile']; ?>" download><i class="fa fa-download"></i></a>
              <?php } ?>
             </div>
           </div>
          </div>
          <div class="col-lg-2">
             <input type="hidden" value="0" id="static_delete_value_<?php echo $m; ?>" >
              <button class="btn btn-warning" id="<?php echo $m; ?>" type="button" onclick="deleteRows(this.id)">Delete</button>
          </div>
        </div>


                   <!--  <div class="row sfitemRow" id="static_div_<?php echo $m; ?>" style="margin-top: 10px;">
                      <div class="col-lg-9">
                        <input type="text" class="form-control"  id="static_value_<?php echo $m; ?>"  name="checklist[]" value="<?php echo $checkvalue['name']; ?>"> 
                      </div>
                      <div class="col-lg-3 m-t-10">
                        <input type="hidden" value="0" id="static_delete_value_<?php echo $m; ?>" >
                        <button class="btn btn-warning" id="<?php echo $m; ?>" type="button" onclick="deleteRows(this.id)">Delete</button>
                      </div>
                    </div> -->
                 
                <?php $m++; } ?>
                 </div>
                  <div class="" style="margin-top: 10px;">
                     <button class="btn btn-success waves-effect waves-light col-lg-3 fw-bold" id="addstatickRows" type="button" onclick="addstatickRows123()">+ Add Check List</button>                  </div>
                </div>
              </div>
           <!--    <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light text-uppercase  mt-15"/> submit</button> -->
            <div class="p-4">
              <button type="button" name="submit" onclick="submittasksettings()" class="btn btn-primary waves-effect waves-light text-uppercase  mt-15 ml-3"/> submit</button>
            </div>
              </div>
            </form>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</div>



<div id="kt_modal_dynamic_popup" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content j-wrapper"  id="dynamic_popup1">
            
        </div>
    </div>
</div>

<div id="kt_modal_dynamic_popup_task_setting" class="modal fade" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content"  id="dynamic_popup_task_settings">
            
        </div>
    </div>
</div>

<?php if($_GET['popup']==0){ ?>
<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>
<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
</script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
<?php } ?>

