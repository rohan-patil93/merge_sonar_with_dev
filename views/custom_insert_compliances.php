

<?php
$application_table = "process_task_settings";
if(isset($_GET['TBL']) && $_GET['TBL']!=""){
    $application_table = "compliances";
}

if(isset($_POST['submit'])){
    $_POST['company_id']=$_SESSION['company_id'];
    $_POST['user_id']=$_SESSION['userid'];
    $_POST['escalation'] = ($_POST['escalation']=="Y")?"Y":"N";
    $_POST['review'] = ($_POST['review']=="Y")?"Y":"N";
    $_POST['critical_compliance'] = ($_POST['critical_compliance']=="Y")?"Y":"N";

 foreach ($_POST['checklist'] as  $key=>$value) {
    if($value!=""){
       $listdata[$key]['name'] = $value;
       $listdata[$key]['check_value'] = "";
     }
    }
    $_POST['check_list'] = json_encode($listdata,1);
    $url = API."?mode=insertcompliance&method=post";
    $response = file_post_contents($url,$_POST,'insert');
    echo $response;
}

if(isset($_GET['Rid']) && $_GET['Rid']!="") {
  $rid =  encodeId($_GET['Rid']);
  $rid = urldecode($rid);
  $jrid = explode(":",$rid);
  $row_id = $jrid['1'];
}

$url = API."?mode=getcompliancedetails&rid=".$row_id."&application_table=".$application_table;
$response = callAPI($url);
$tasks_details = json_decode($response,1);
    $tasks_details = json_decode($response,1);
    $due_list = $tasks_details['due_list'];
    $buddy_list = $tasks_details['buddy_list'];
    $task_list = $tasks_details['category'];
    $sub_category = $tasks_details['sub_category'];
    $emp_list = $tasks_details['employees'];
    $costcenter = $tasks_details['costcenter'];
    $departments = $tasks_details['departments'];
    $emaildata = $tasks_details['email_content'];
    $processtypedata = $tasks_details['task_process_type'];
    $module_setupres = $tasks_details['module_setup'];
    $user_list = $tasks_details['application_users'];
$act_categories = $tasks_details['act_categories'];
$sub_act_categories = $tasks_details['sub_act_category'];
$compliancedetail = $tasks_details['process_task_settings'];

   $filte_data = $tasks_details['filter_data'];
   $filter_id = $filte_data['id'];

$montharray =$months = array('1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July ','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December');
$weekdays = ['Sun'=>'Sunday','Mon'=>'Monday','Tue'=>'Tuesday','Wed'=>'Wednesday','Thu'=>'Thursday','Fri'=>'Friday','Sat'=>'Saturday'];

?>
<script type="text/javascript">
   function showemplist(value) {
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
            var application_table = document.getElementById('application_table').value;
            jQuery.ajax({
                url: "views/custom_insert_filters.php",
                data:'filter_id='+filter_id+'&process_type='+process_type+"&tablename="+application_table,
                type: "GET",
                success:function(data){
                    $('#dynamic_popup_task_settings').html(data);
                    $("#kt_modal_dynamic_popup_task_setting").modal("show");
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
    $("#static_div_"+val).css("display", "none");
    $("#static_delete_value_"+val).val('1');
  }
function addstatickRows123(){
    var count = $(".sfitemRow").length;
    count++;
    var htmlRows = '';
    htmlRows += '<div class="row align-items-center sfitemRow" id="static_div_'+count+'" style="margin-top: 10px;">';
    htmlRows += '<div class="col-lg-9">';
    htmlRows += '<input aria-label="Checklist[]" type="text" class="form-control"  id="static_value_'+count+'"  value=""  name="checklist[]">';
    htmlRows += '</div>';

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

function scehduleaction(){
    var selectscehduleaction = $("#selectscehduleaction").val();
    $("#monthlyschedule").hide();
    $("#weeklyschedule").hide();
    $("#yearlyschedule").hide();
    if(selectscehduleaction == "MN"){
        $("#monthlyschedule").show('slow');
    }else if(selectscehduleaction == "WK"){
        $("#weeklyschedule").show('slow');
    }else if(selectscehduleaction == "YR"){
        $("#yearlyschedule").show('slow');
    }
}

function selectCategroy(){
    var cat_id = $("#select_category").val()
    jQuery.ajax({
        url: "ajax.php",
        data:'page=getsubcategroy&cat_id='+cat_id,
        type: "POST",
        success:function(data){
            $("#select_sub_category").html(data);
        }
    });

}
function selectAct(){
        var act_id = $("#select_act").val()
        jQuery.ajax({
        url: "ajax.php",
        data:'page=getsubacts&act_id='+act_id,
        type: "POST",
        success:function(data){
            $("#select_sub_act").html(data);
        }
    });
}

    function selectEscalatiOnCheck(){
        if($('#escalationcheck').is(':checked')){
            $('#escalationreviewshow').show();
        }else{
            $('#escalationreviewshow').hide();
        }
    }
function selectReviewOnCheck(){
        if($('#reviewoncheck').is(':checked')){
            $('#reviewshow').show();
        }else{
            $('#reviewshow').hide();
        }
    }

</script>
<style>
  .radio-inline .form-label input[type="radio"]{
     display: none;
  }
</style>
<div class="d-flex flex-column-fluid">
  <div class="container-fluid " >
      <div class="card card-custom  j-pro" id="kt_page_sticky_card ">
  <div class="card-header mb-3">
    <h5>Compliance</h5>
    <div class="card-header-right">
         <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
    </div>
</div>
<div class="card-body p-0 ">
    <form  method="post" action="" enctype="multipart/form-data">
            <div class="col-xl-12 ">
                   <input type="hidden" name="task_for" id="filter" value="<?php echo $Rid_id; ?>">
                  <input type="hidden" name="task_for" id="filter_2" value="<?php echo urlencode($task_for); ?>">
                  <input type="hidden" name="files" id="files" value="<?php echo urlencode($main_data['files']); ?>">
                  <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filter_id; ?>">
                  <input type="hidden" name="rid"  value="<?php echo $row_id ; ?>">
                  <input type="hidden" name="application_table" id="application_table"  value="<?php echo $application_table ; ?>">


                 <div class="p-4" data-wizard-type="step-content" data-wizard-state="current">

                    <div class="row">
                      <div class="col-lg-6 mb-1 ">
                        <span class="form-label">Compliances Name *</span>
                        <input aria-label="Task Name" type="text" class="form-control" name="task_name" value="<?php echo $compliancedetail['task_name']; ?>" />
                      </div>
                      <div class="col-lg-3 mb-1 ">
                        <span class="form-label">Module Groupe</span>
                        <select aria-label="Module Group" name="module_group_id" class="form-control form-select js-example-basic-single" onchange="selectGroupModul(this.value)">
                          <option value="">Select Any One </option>
                          <?php foreach($_SESSION['module_setup_groups'] as $module_setup_groups ) { ?>
                            <option value="<?php echo $module_setup_groups['id'];?>" <?php if($module_setup_groups['id'] == $compliancedetail['module_group_id']){ ?>selected <?php
} ?> ><?php echo $module_setup_groups['name'];?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="col-lg-3 mb-1 ">
                        <span class="form-label">Module</span>
                        <select aria-label="Module" name="module_id" class="form-control form-select js-example-basic-single" id="module_id">
                          <option value="">Select Any One </option>
                          <?php foreach ($module_setupres as $module_setu) { ?>
                            <option value="<?php echo $module_setu['id'];?>" <?php if($module_setu['id'] == $compliancedetail['module_id']){ ?>selected <?php
} ?> ><?php echo $module_setu['module_name'];?></option>
                          <?php } ?>
                        </select>
                      </div>
                </div>
             <div class="row">
                  <div class="col-lg-6">
                    <span class="form-label col-lg-3">Category</span>
                    <div class="col-lg-9">
                      <select aria-label="Process" name="process_id" class="form-control form-select js-example-basic-single" id="select_category" onchange="selectCategroy()">
                        <option value="">Select Any One </option>
                        <?php
                        foreach ($task_list as $task){
                          ?>
                          <option value="<?php echo $task['id'];?>" <?php if($compliancedetail['process_id'] == $task['id']){ ?>selected <?php
} ?> ><?php echo $task['category_name'];?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <span class="form-label col-lg-3">Sub Category</span>
                    <div class="col-lg-9">
                      <select aria-label="Sub Cat" name="sub_cat_id" class="form-control form-select js-example-basic-single" id="select_sub_category">
                        <option value="">Select Any One </option>
                        <?php
                        foreach ($sub_category as $task){
                          ?>
                          <option value="<?php echo $task['id'];?>" <?php if($compliancedetail['sub_cat_id'] == $task['id']){ ?>selected <?php
} ?> ><?php echo $task['category_name'];?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-lg-6">
                    <span class="form-label col-lg-3">Act</span>
                    <div class="col-lg-9">
                      <select aria-label="Act" name="act_id" class="form-control form-select js-example-basic-single" id="select_act" onchange="selectAct()">
                        <option value="">Select Any One </option>
                        <?php
                        foreach ($act_categories as $act_cate){
                          ?>
                          <option value="<?php echo $act_cate['id'];?>" <?php if($act_cate['id'] == $compliancedetail['act_id']){ ?>selected <?php
} ?> ><?php echo $act_cate['category_name'];?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <span class="form-label col-lg-3">Sub Act</span>
                    <div class="col-lg-9">
                      <select aria-label="Sub Act" name="sub_act_id" class="form-control form-select js-example-basic-single" id="select_sub_act">
                        <option value="">Select Any One </option>
                        <?php
                        foreach ($sub_act_categories as $task){
                          ?>
                          <option value="<?php echo $task['id'];?>" <?php if($compliancedetail['sub_act_id'] == $task['id']){ ?>selected <?php
} ?> ><?php echo $task['category_name'];?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-lg-6">
                    <div class="checkbox-fade fade-in-primary  mt-4" onclick="selectReviewOnCheck()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                      <label class="form-label">
                        <input aria-label="Review" type="checkbox" value="Y" name="review" id="reviewoncheck"  <?php echo $compliancedetail['review'] == "Y"?"checked":""; ?>>
                        <span class="cr">
                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                        </span>
                        <span>Review</span>
                      </label>
                    </div>
                  </div>
                  <div class="col-lg-6" id="reviewshow" style="<?php echo $compliancedetail['review']=="Y"?'':'display:none'; ?>">
                    <span class="form-label">Review Process</span>
                    <div class="col-lg-9">
                      <select aria-label="Review Process" name="review_process" class="form-control form-select js-example-basic-single">
                        <option value="">Select Any One </option>
                        <?php
                         foreach ($buddy_list as $emptypes) {
                          ?>
                          <option value="<?php echo $emptypes['genc_detail_code'];?>" <?php if($emptypes['genc_detail_code'] == $compliancedetail['review_process']){ ?>selected <?php
} ?> ><?php echo $emptypes['genc_detail_name'];?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-lg-6">
                    <div class="checkbox-fade fade-in-primary  mt-4" onclick="selectEscalatiOnCheck()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                      <label class="form-label">
                        <input aria-label="Escalation" type="checkbox" value="Y" name="escalation" id="escalationcheck" <?php echo $compliancedetail['escalation'] == "Y"?"checked":""; ?>>
                        <span class="cr">
                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                        </span>
                        <span>Escalation Review</span>
                      </label>
                    </div>
                  </div>
                  <div class="col-lg-6" id="escalationreviewshow" style="<?php echo $compliancedetail['escalation']=="Y"?'':'display:none'; ?>">
                    <span class="form-label">Escalation Process</span>
                    <div class="col-lg-9">
                      <select aria-label="Escalation Process" name="escalation_process" class="form-control form-select js-example-basic-single">
                        <option value="">Select Any One </option>
                        <?php
                         foreach ($buddy_list as $empthyp) {
                          ?>
                          <option value="<?php echo $empthyp['genc_detail_code'];?>" <?php if($empthyp['genc_detail_code'] == $compliancedetail['escalation_process']){ ?>selected <?php
} ?> ><?php echo $empthyp['genc_detail_name'];?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row mt-2">
                    <div class="col-lg-3">
                        <span class="form-label">Critical Compliance</span></br>
                        <input aria-label="Critical Compliance" type="checkbox" class="js-switch" name="critical_compliance" value="Y" <?php echo $compliancedetail['critical_compliance'] == "Y"?"checked":""; ?>/>
                    </div>
                    <div class="col-lg-5">
                    <span class="form-label">Description</span>
                    <textarea aria-label="Description" style="overflow:auto" class="form-control form-control-lg" name="description" > <?php echo $compliancedetail['description']; ?> </textarea>
                  </div>
                <div class="col-lg-4">
                    <span class="form-label">Email Template</span>
                    <div class="">
                      <select aria-label="Email Content" name="email_content_id" class="form-select js-example-basic-single" id="email_template">
                        <option value="">Select Any One </option>
                        <?php foreach ($emaildata as $email) {  ?>
                          <option value="<?php echo $email['id'];?>" <?php if($compliancedetail['email_content_id'] == $email['id']){ ?>selected <?php
} ?> ><?php echo $email['email_header'];?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>

                      <div class="row mt-2">
                        <span class="form-label">Set recurrence</span>
                        <div class="col-lg-3 row">
                          <span class="form-label col-lg-6 f-w-600">Repeat Every</span>
                          <div class="col-lg-6"><input aria-label="Action No" type="text" class="form-control col-lg-2" name="action_no" value="1" readonly/></div>
                        </div>
                      <div class="col-lg-3">
                         <select aria-label="Action Frequency" name="action_frequency" class="form-control form-select js-example-basic-single" id="selectscehduleaction" onchange="scehduleaction()">
                          <?php foreach ($due_list as $due) {    ?>
                            <option value="<?php echo $due['genc_detail_code'];?>" <?php if($compliancedetail['action_frequency'] == $due['genc_detail_code']){ ?>selected <?php
} ?> ><?php echo $due['genc_detail_name'];?></option>
                          <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-6" id="monthlyschedule" style="<?php echo $compliancedetail['action_frequency']=="MN"?'':'display:none'; ?>">
                        <div class="row">
                          <span class="form-label col-lg-2 f-w-600">On Day</span>
                          <div class="col-lg-4"><input aria-label="On The Day" type="number" class="form-control col-lg-4" name="on_the_day" value="<?php echo $compliancedetail['on_the_day']; ?>" ></div>
                        </div>
                    </div>
                    <div class="col-lg-6" id="yearlyschedule" style="<?php echo $compliancedetail['action_frequency']=="YR"?'':'display:none'; ?>">
                        <div class="row">
                            <span class="form-label col-lg-3 f-w-600">On Month</span>
                            <div class="col-lg-4">
                                <select aria-label="On The Month" name="on_the_month"  style="width:150px;" class="form-control form-select js-example-basic-single">
                                  <?php foreach ($montharray as $mont=>$montname) {    ?>
                                    <option value="<?php echo $mont;?>" <?php if($mont == $compliancedetail['on_the_month']){ ?>selected <?php
} ?> ><?php echo $montname;?></option>
                                  <?php } ?>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <input aria-label="On The Day Year" type="text" class="form-control col-lg-4" name="on_the_day_year" value="<?php echo $compliancedetail['on_the_day_year']; ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" id="weeklyschedule" style="<?php echo $compliancedetail['action_frequency']=="WK"?'':'display:none'; ?>">
                        <div class="row">
                            <span class="form-label col-lg-3 f-w-600">On Week Day</span>
                            <div class="col-lg-4">
                                <select aria-label="On The Week" name="on_the_week"  style="width:150px;" class="form-control form-select js-example-basic-single">
                                  <?php foreach ($weekdays as $weekkey=>$weekvalue) {    ?>
                                    <option value="<?php echo $weekkey;?>" <?php if($weekkey == $compliancedetail['on_the_week']){ ?>selected <?php
} ?> ><?php echo $weekvalue;?></option>
                                  <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>


              </div>
                    </div>

                <div class="row">
                    <div class="col-xl-6 mt-3">
                       <span class="form-label">Criteria...</span>
                    </div>
                    <div id="tsk_fortable"></div>
                    <div class="col-xl-12">
                          <i class="flaticon2-group text-success"></i>
                          <button type="button" class="text-primary" onclick="showpopup('newtask');">Edit </button>
                    </div>
                </div>


                <div class="col-lg-12 mt-3">
                  <div id="append_statick" class="">
                    <div class="row" style="margin-top: 10px;">
                      <div class="col-lg-9">
                        <span class="form-label">Check List</span>
                      </div>
                      <div class="col-lg-3">
                        <span><b></b></span>
                      </div>
                    </div>
                    <?php $checklist_data = json_decode($compliancedetail['check_list'],1);

                      $m=1;
                      foreach ($checklist_data as $checkvalue) {
                    ?>
                    <div class="row sfitemRow" id="static_div_<?php echo $m; ?>" style="margin-top: 10px;">
                      <div class="col-lg-9">
                        <input aria-label="Input field" type="text" class="form-control"  id="static_value_<?php echo $m; ?>"  name="checklist[]" value="<?php echo $checkvalue['name']; ?>">
                      </div>
                      <div class="col-lg-3 m-t-10">
                        <input type="hidden" value="0" id="static_delete_value_<?php echo $m; ?>" >
                        <button class="btn btn-warning" id="<?php echo $m; ?>" type="button" onclick="deleteRows(this.id)">Delete</button>
                      </div>
                    </div>

                <?php $m++;
} ?>
                 </div>
                  <div class="" style="margin-top: 10px;">
                     <button class="btn btn-success waves-effect waves-light col-lg-3 fw-bold" id="addstatickRows" type="button" onclick="addstatickRows123()">+ Add Check List</button>                  </div>
                </div>
              </div>

            <div class="p-4">
                <input type="hidden" name="process_type" id="process_type" value="C">

              <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light text-uppercase  mt-15 ml-3"/> submit</button>
            </div>
        </form>
              </div>
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

