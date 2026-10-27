

<?php


$url = API."?mode=getcompliancedetails";
$response = callAPI($url);
$tasks_details = json_decode($response,1);
/*
echo "<pre>";
print_r($tasks_details);
echo "</pre>";
*/
	$tasks_details = json_decode($response,1);
	$period_list = $tasks_details['period_list'];
	$due_list = $tasks_details['due_list'];
	$buddy_list = $tasks_details['buddy_list'];
	$task_list = $tasks_details['category'];
	$emp_list = $tasks_details['employees'];
    $costcenter = $tasks_details['costcenter'];
	$departments = $tasks_details['departments'];
	$emaildata = $tasks_details['email_content'];
	$processtypedata = $tasks_details['task_process_type'];
	$module_setupres = $tasks_details['module_setup'];


   if($row_id!=""){   
    $where2con["where"] = array('is_active'=> '0','company_id'=>$company_id,'transaction_id'=>$row_id);
   $data2_con = urlencode(json_encode($where2con));
   $url2 = API."?mode=gettabledetailswithconditions&table=filter_tables&data=".$data2_con; 
   $response2 = callAPI($url2);
   $filte_data = json_decode($response2,1);
   $filter_id = $filte_data[0]['id']; 
 }

?>
<script type="text/javascript">
   function showemplist(value) {
   //alert(value);
   if(value=='OB') {
   document.getElementById('emplistshow').style.display ='block';
   } else {
   document.getElementById('emplistshow').style.display ='none';

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
    $("#static_div_"+val).css("display", "none");
    $("#static_delete_value_"+val).val('1');
  }
function addstatickRows123(){
    var count = $(".sfitemRow").length;
    //alert('la-hand-o-right'+count);
    count++;
    var htmlRows = '';    
    htmlRows += '<div class="row align-items-center sfitemRow" id="static_div_'+count+'" style="margin-top: 10px;">';
    htmlRows += '<div class="col-lg-9">';
    htmlRows += '<input type="text" class="form-control"  id="static_value_'+count+'"  value=""  name="checklist[]">';
    htmlRows += '</div>';
   /* htmlRows += '<div class="col-lg-3">';
    htmlRows += '<input type="text"class="form-control"  id="static_lable_'+count+'"  value=""  name="type"> ';
    htmlRows += '</div>';
    */
    htmlRows += '<div class="col-lg-3">';               
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
  var formdata = $("#kt_form_submits").serialize();
  jQuery.ajax({
        url: "ajax.php",
        data:formdata+'&page=insertprocesstasksettings',
        type: "POST",
        success:function(data){
			//console.log(data);
         <?php   if(isset($_GET['popup'])){ ?>
          $('#kt_modal_KTDatatable_remotem_task').modal("hide"); 
           $('#kt_modal_dynamic_popup').modal("hide");   
          custompopups('<?php echo $_GET['type']; ?>',<?php echo $_GET['workflowid']; ?>);
        <?php }else{ ?>
            location.reload();
       <?php  } ?>
        },
        error:function (){}
      });
}


function selectGroupModul(gmid){
	jQuery.ajax({
        url: "ajax.php",
        data:'page=getmodulesetupoption&gmid='+gmid,
        type: "POST",
        success:function(data){
			console.log(data);
         	$("#module_id").html(data);
        }
    });
}

</script>
<style>
  .radio-inline .form-label input[type="radio"]{
     display: none;
  }
</style>
<div class="d-flex flex-column-fluid j-wrapper">
  <div class="container-fluid " >
      <div class="card card-custom  j-pro" id="kt_page_sticky_card ">
  <div class="card-header mb-3">
    <h5><?php echo $title;  ?></h5>
    <div class="card-header-right">
    	 <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
				  </div>
</div>




        <div class="card-body p-0 ">
              <div class="col-xl-12 j-content">
                <form class="form" id="kt_form_submits" method="post" action="" enctype="multipart/form-data">
                   <input type="hidden" name="task_for" id="filter" value="<?php echo $Rid_id; ?>">
                  <input type="hidden" name="task_for" id="filter" value="<?php echo urlencode($task_for); ?>"><!-- 
                  <input type="hidden" name="emp_count" id="emp_count" value="<?php echo urlencode($emp_count); ?>"> -->
                  <input type="hidden" name="files" id="files" value="<?php echo urlencode($main_data['files']); ?>">
                  <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filter_id; ?>">
                  <div class="p-4" data-wizard-type="step-content" data-wizard-state="current">
                  <!--   <h4 class="mb-10 font-weight-bold text-dark">Tasks</h4> -->
                    <div class="row">
                      <div class="col-lg-6 mb-1 ">
                        <label class="form-label">Compliances Name *</label>
                        <input type="text" class="form-control" name="task_name" value="<?php echo $task_name; ?>" />
                      </div>
					  <div class="col-lg-3 mb-1 ">
                        <label class="form-label">Module Groupe</label>
						<select name="module_group_id" class="form-control form-select js-example-basic-single" onchange="selectGroupModul(this.value)">
                          <option value="">Select Any One </option>
                          <?php foreach($_SESSION['module_setup_groups'] as $module_setup_groups ) { ?>
                            <option value="<?php echo $module_setup_groups['id'];?>" <?php if($module_setup_groups['id'] == $display_data[0]['module_group_id']){ ?>selected <?php } ?> ><?php echo $module_setup_groups['name'];?></option>
                          <?php } ?>
                        </select>                      
					  </div>
					  <div class="col-lg-3 mb-1 ">
                        <label class="form-label">Module</label>
						<select name="module_id" class="form-control form-select js-example-basic-single" id="module_id">
                          <option value="">Select Any One </option>
                          <?php foreach ($module_setupres as $module_setu) { ?>
                            <option value="<?php echo $module_setu['id'];?>" <?php if($module_setu['id'] == $display_data[0]['module_id']){ ?>selected <?php } ?> ><?php echo $module_setu['module_name'];?></option>
                          <?php } ?>
                        </select> 
                      </div>
				</div>
			 <div class="row">
                  <div class="col-lg-6">
                    <label class="form-label col-lg-3">Category</label>
                    <div class="col-lg-9">
                      <select name="process_id" class="form-control form-select js-example-basic-single">
                        <option value="">Select Any One </option>
                        <?php
                        foreach ($task_list as $task){
                          ?>
                          <option value="<?php echo $task['id'];?>" <?php if($main_data['process_id'] == $task['id']){ ?>selected <?php } ?> ><?php echo $task['category_name'];?></option>
                        <?php } ?>
                      </select>
                    </div> 
                  </div>
				  <div class="col-lg-6">
                    <label class="form-label col-lg-3">Sub Category</label>
                    <div class="col-lg-9">
                      <select name="process_id" class="form-control form-select js-example-basic-single">
                        <option value="">Select Any One </option>
                        <?php
                        foreach ($task_list as $task){
                          ?>
                          <option value="<?php echo $task['id'];?>" <?php if($main_data['process_id'] == $task['id']){ ?>selected <?php } ?> ><?php echo $task['category_name'];?></option>
                        <?php } ?>
                      </select>
                    </div> 
                  </div>
                </div>
				<div class="row">
                  <div class="col-lg-6">
                    <label class="form-label">Description</label>
                    <textarea style="overflow:auto" class="form-control form-control-lg" name="description" > <?php echo $main_data['description']; ?> </textarea>
                  </div>
				<div class="col-lg-6">
					<label class="form-label">Email Template</label>
					<div class="">
					  <select name="email_content_id" class="form-select js-example-basic-single" id="email_template">
						<option value="">Select Any One </option>
						<?php foreach ($emaildata as $email) {  ?>
						  <option value="<?php echo $email['id'];?>" <?php if($main_data['email_content_id'] == $email['id']){ ?>selected <?php } ?> ><?php echo $email['email_header'];?></option>
						<?php } ?>
					  </select>
					</div> 
				  </div>
                </div>
				<div class="row">		  
			<div class="col-lg-8">
				<label class="f-w-700">Allocation Type</label>
				<div class="form-radio">
				    <div class="radio radio-inline">
				      <label class="form-label">
				        <input type="radio" id="allocation_type"  name="allocation_type" value="E" onclick="selectAllocationType()" style="display: none;" checked>
				        <i class="helper"></i>Employees</label>
				    </div>
				    <div class="radio radio-inline">
				      <label class="form-label">
				        <input type="radio" id="allocation_type" name="allocation_type"  value="CC" onclick="selectAllocationType()"  style="display: none;">
				        <i class="helper"></i>Cost Cneters
				      </label>
				    </div>
				    <div class="radio radio-inline">
				      <label class="form-label">
				        <input type="radio" id="allocation_type" name="allocation_type"  value="D" onclick="selectAllocationType()" style="display: none;">
				        <i class="helper"></i>Departments
				      </label>
				    </div>
					<div class="radio radio-inline">
				      <label class="form-label">
				        <input type="radio" id="allocation_type" name="allocation_type"  value="B" onclick="selectAllocationType()" style="display: none;">
				        <i class="helper"></i>buddy
				      </label>
				    </div>
					<div class="radio radio-inline" onclick="showpopup('newtask');">
				      <label class="form-label">
				        <input type="radio" id="allocation_type" name="allocation_type"  value="C" onclick="selectAllocationType()" style="display: none;">
				        <i class="helper"></i>Criteria
				      </label>
				    </div>
				</div>
			</div>
			<div  id="emplistshow" class="col-lg-4"> 
				<label class="form-label">Employee</label>
					<select name="other_id" class="form-control form-select js-example-basic-single " >
					  <option value="">Select Any One </option>
					  <?php foreach ($emp_list as $emp) { ?>
						<option value="<?php echo $emp['id'];?>" <?php if($main_data['user_id'] == $emp['id']){ ?>selected <?php } ?> ><?php echo $emp['display_name'];?></option>
					  <?php } ?>
					</select> 
			 </div>
			 <div class="form-group col-lg-4 select_caost_center" style="display:none">
				<label>Cost Center</label>
				<select class="form-select js-example-basic-single " name="coast_center_id">
						<option value="">Select Any One</option>
				<?php foreach ($costcenters as $costcenter){ ?>					
					<option  value="<?php echo $costcenter['id']; ?>" ><?php echo ucwords($costcenter['cost_center_name']); ?></option>
				<?php } ?>
				</select> 
			</div>
			<div class="form-group col-lg-4 select_dept " style="display:none">
				<label>Department</label>
				<select class="form-select js-example-basic-single " name="departent_id">
						<option value="">Select Any One</option>
				<?php foreach ($departments as $departments){ ?>					
					<option  value="<?php echo $departments['id']; ?>"><?php echo ucwords($departments['department_name']); ?></option>
				<?php } ?>
				</select> 
			</div>
			<div  id="buddyshow" class="col-lg-4" style="display:none"> 
				<label class="form-label">Buddy</label>
					<select name="other_id" class="form-control form-select js-example-basic-single">
					  <option value="">Select Any One </option>
					  <?php foreach ($buddy_list as $emptypes) { ?>
						<option value="<?php echo $emptypes['genc_detail_code'];?>" <?php if($emptypes['genc_detail_code'] == $emp['id']){ ?>selected <?php } ?> ><?php echo $emptypes['genc_detail_name'];;?></option>
					  <?php } ?>
					</select> 
			 </div>
			</div> 
			 
                      
                    
                      
                      <div class="col-lg-12 mt-6 mt-1">
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

                    </div>
                      <div class="mb-3 row">
                          <div class="col-lg-3 col-md-3 col-sm-3 col-12 m-b-10" style="display: inherit;">
                          <div class="form-radio">
                            <div class="radio radio-inline">
                               <label class="form-label">
                                <input type="radio" name="due_date" <?php if($main_data['no_action'] !='1' && $main_data['on_the_day'] !='1') {?> checked="checked" <?php } ?> value="frequency"/> <i class="helper"></i>
                              </label>

                            </div>
                          </div>
                         <input type="text" class="form-control mr-2" style=""  name="action_no" value="<?php echo $main_data['action_no'];?>" />
                    </div>
    
                      <div class="col-lg-3 col-md-3 col-sm-3 col-12 m-b-10">
                         <select name="action_frequency"  style="width:150px;" class="form-control form-select js-example-basic-single">
                          <option value="">Select</option>
                          <?php foreach ($due_list as $due) {    ?>
                            <option value="<?php echo $due['genc_detail_code'];?>" <?php if($main_data['action_frequency'] == $due['genc_detail_code']){ ?>selected <?php } ?> ><?php echo $due['genc_detail_name'];?></option>
                          <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-12 m-b-10">
                        <select name="before_or_after" style="width:150px;" class="form-control form-select js-example-basic-single">
                          <option value="">Select</option>
                          <?php foreach ($period_list as $due) {  ?>
                            <option value="<?php echo $due['genc_detail_code'];?>" <?php if($main_data['before_or_after'] == $due['genc_detail_code']){ ?>selected <?php } ?> ><?php echo $due['genc_detail_name'];?></option>
                          <?php } ?>
                        </select> 
                    </div>
              </div>
                    </div>
                  
              
              
                <div class="col-lg-12 mt-3">
                  <div id="append_statick" class="">
                    <div class="row" style="margin-top: 10px;">
                      <div class="col-lg-9">
                        <label class="form-label">Check List</label>
                      </div>
                      <div class="col-lg-3">
                        <label><b></b></label>
                      </div>
                    </div>
                    <?php $checklist_data = json_decode($main_data['check_list'],1);
                      
                      //if(count((array)$checklist_data)>0){ continue; }else{ $checklist_data = array('name'=>""); }
                      $m=1;
                      foreach ($checklist_data as $checkvalue) {
                       //print_r($checkvalue);
                    ?>
                    <div class="row sfitemRow" id="static_div_<?php echo $m; ?>" style="margin-top: 10px;">
                      <div class="col-lg-9">
                        <input type="text" class="form-control"  id="static_value_<?php echo $m; ?>"  name="checklist[]" value="<?php echo $checkvalue['name']; ?>"> 
                      </div>
                      <div class="col-lg-3 m-t-10">
                        <input type="hidden" value="0" id="static_delete_value_<?php echo $m; ?>" >
                        <button class="btn btn-warning" id="<?php echo $m; ?>" type="button" onclick="deleteRows(this.id)">Delete</button>
                      </div>
                    </div>
                 
                <?php $m++; } ?>
                 </div>
                  <div class="" style="margin-top: 10px;">
                     <button class="btn btn-success waves-effect waves-light col-lg-3 fw-bold" id="addstatickRows" type="button" onclick="addstatickRows123()">+ Add Check List</button>                  </div>
                </div>
              </div>
           <!--    <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light text-uppercase  mt-15"/> submit</button> -->
            <div class="p-4">
			    <input type="hidden" name="process_type" id="process_type" value="C"> <!--compliane type-->
                      
              <button type="button" name="submit" onclick="submittasksettings()" class="btn btn-primary waves-effect waves-light text-uppercase  mt-15 ml-3"/> submit</button>
            </div>
              </div>
            </form>
          </div>
        </div>
      </form>
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

