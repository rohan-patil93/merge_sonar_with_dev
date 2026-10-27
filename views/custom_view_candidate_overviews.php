<?php
$_SESSION['apply_js']['todo'] = 1;
$_SESSION['apply_js']['multiselest'] = '1';
$rid =  encodeId($_REQUEST['Rid']);
$rid = urldecode($rid);
$cand = explode(':', $rid);
$candidate_id = $cand['1'];
$table_name = "candidates";
$transaction_table = "candidates";
if(isset($_POST['submit_doc'])){
  $candidate_id = $_POST['candidate_id'];
  $k=0;
  $path = $_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$table_name."/".$candidate_id."/Documents";
  $imagename = array();
  foreach ($_FILES as $key => $file) {
    $hidimage = "hid".$key;
    if($_FILES[$key]['error'] !=4){
      $name = imageuploadwitharray($path,$key,$_POST[$hidimage],"document",$key);
      $postname = explode('_', $key);
      $imagename[$k]['id'] = $postname['1'];
      $imagename[$k]['name'] = $name;
      $k++;
    }
  }
  $_POST['documents'] =  $imagename;
  $_POST['candidate_id'] =  $candidate_id;
  $url = API."?mode=updatecandaitedocument&method=post";
  $response = file_post_contents($url, $_POST, 'insert');
  $response = json_decode($response,1);
  if($response){ ?>
    <script type="text/javascript">
      document.onreadystatechange = function () {
        if (document.readyState == "complete") {
          tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
        }
      }
    </script>
  }
}
  //request documents
if(isset($_POST['submitrequerd_docs'])){
  $url = API."?mode=addorupdatecandaitedocument&method=post";
  $_POST['company_id'] = $_SESSION['company_id'];
  $response = file_post_contents($url, $_POST, 'insert');
  $response = json_decode($response,1);
  if($response){
    ?>
    <script type="text/javascript">
      document.onreadystatechange = function () {
        if (document.readyState == "complete") {
          tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
        }
      }
    </script>
    <?php
  }
}
  //request documents

$admin_type = 0;
$user_roles = $_SESSION['role_ids'];
if(in_array(RECRUITINGMANAGER_ROLE,(array)$user_roles) || in_array(RECRUITINGTEAM_ROLE,(array)$user_roles) || in_array(ADMIN_ROLE,(array)$user_roles)){
  $admin_type = 1;
}
$document_category_id = 1;
$encodeuser_roles = json_encode((array)$_SESSION['role_ids']);
$url = API."?mode=getcandidateoverviews&candidate_id=".$candidate_id."&admin_type=".$admin_type."&document_category_id=".$document_category_id."&user_type=".$_SESSION['user_type'];
$api = callAPI($url);
$result = json_decode($api,1);
$candidate_documents = $result['candidate_documents'];
$candidates = $result['candidates'];
$application_attachments = $result['application_attachments'];
$totaldocuments =array();
$totaldocuments['uploaded_cnt'] = 0;
$totaldocuments['total'] = 0;

foreach ($candidate_documents as $candidate_document1) {
  $candidatedocumentlist[$candidate_document1['parent_document_id']][] = $candidate_document1;
  if($candidate_document1['document_shared'] !=""){
    $totaldocuments['uploaded_cnt']++;
  }
  $totaldocuments['total']++;
}

$document_lists = $result['document_lists'];
foreach ($document_lists as $document_list) {
  $documentlist[$document_list['id']] = $document_list['document_name'];
}
foreach($document_lists as $alldo){
  if($alldo['parent_document_id']==0){
    $parentdocumentslist[] = $alldo;
  }else{
    $allvdata_details[$alldo['parent_document_id']][] = $alldo;
  }
}

foreach ($candidate_documents as $value) {
  $checkl[$value['document_list_id']] = $value;
}

$taskscategories = array();
foreach($result['tasks_categories'] as $tasks_categories){
  $taskscategories[$tasks_categories['id']] = $tasks_categories['category_name'];
}
$transactionactivityschedule = array();
$transactionactivityschedulecompletedstatus = array();
$rejetcedcnt = $totaltasks = $yettostartcnt = $totalinprogresstask = $completedcnt = $totalapprovedtask = $totaldelayedcnttask =$totaltaskreview_cnt =  0;
foreach($result['transaction_activity_schedule'] as $transaction_activity_schedule){
  if($transaction_activity_schedule['current_status'] == ST_COMPLETE){
    $transactionactivityschedulecompletedstatus[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
    $totaltaskreview_cnt++;
  }else{
    $transactionactivityschedule[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
  }
    if($transaction_activity_schedule['current_status'] == ST_APPROVED){
      $complitedtask[$transaction_activity_schedule['process_id']]++;
      $totalapprovedtask++;
    }elseif($transaction_activity_schedule['current_status'] == ST_COMPLETE){
      $completedcnt++;
    }elseif($transaction_activity_schedule['current_status'] == ST_REJECTED){
      $rejetcedcnt++;
    }elseif($transaction_activity_schedule['current_status'] == ST_INPROGRESS){
      $totalinprogresstask++;
    }elseif($transaction_activity_schedule['current_status'] == ST_ACTIVE){
      $yettostartcnt++;
    }
    if($transaction_activity_schedule['link_page'] == '0'){
      $linktransactionactivityschedule[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
      if($transaction_activity_schedule['current_status'] == ST_APPROVED){
          $linkcomplitedtask[$transaction_activity_schedule['process_id']]++;
      }
    }
  $totaltask[$transaction_activity_schedule['process_id']]++;
  $totaltasks++;
}

$job_posting_candidates =$result['job_posting_candidates'];
foreach($result['departments'] as $department){
  $departments[$department['id']] = $department['department_name'];
}

foreach($result['job_titles'] as $job_title){
  $job_titles[$job_title['id']] = $job_title['job_title'];
}

foreach($result['application_users'] as $application_users){
  $applicationusers[$application_users['id']] = $application_users['display_name'];
}

$profilereadiness = array("Contact Details"=>array('first_name','middle_name','last_name','display_name','phone','email_id','address','post_code','bio'),"Personal Details"=>array('gender','date_of_birth','marital_status','wedding_date','blood_group','father_name','spouse_name','adhar_no','pan_number','hiring_source'),"Dependents"=>array('employee_dependents'),"Current Employment"=>array('employee_contacts','current_location','reason_for_change','relocate','current_ctc','expected_ctc','no_of_reportees','job_grade,pay_template','job_type,location_id'),"Awards and Accolades"=>array('candidate_awards'),"HR Details"=>array('first_role','candidate_current_role','notice_period','np_negotiatable','first_pay','current_pay','gaps_in_employment_reason','performance_ratings'),"Skills"=>array('skills'),"Work Experiences"=>array('candidate_experiances'),"Education Details"=>array('candidate_qualifications'));
$stausresult = ViewStatusData();

$due_date_period_list = $result['period_list'];

?>

<script type="text/javascript">

  function editFormShow(hideclass,showclass,hide_btn,show_btn,submit_type,form_name=""){
    $("."+hideclass).hide();
    $("."+showclass).show('slow');
    $("#"+hide_btn).hide();
    $("#"+show_btn).show('slow');
    $("#"+hide_btn+"_undo").hide();
    $("#"+show_btn+"_undo").show('slow');
    if(submit_type == "Edit"){
      $(".datepickershow_"+showclass).show();
    }
    if(submit_type == "Save"){
      $(".datepickershow_"+hideclass).hide();
      $("."+hideclass).each(function(index) {
        var type = $(this).attr("data-type");
        if(type == 'select'){
          if($(this).val() != "" && $(this).val() !='0'){
            var value  = $(this).find('option:selected').text();
          }
        }else{
          var value = $(this).val();
        }
        if(value ==""){
          value  = "-";
        }
        $("."+showclass+":eq("+index+")").html(value);
        console.log(type+"---"+value);
      });
      var candidate_id = '<?php echo $candidate_id; ?>';
      var postarray = $("#"+form_name).serialize();
      $.ajax({
        url: "ajax.php",
        type: "POST",
        data: 'page=insertcandidatedetails&candidate_id='+candidate_id+'&'+postarray,
        success: function(response){
          console.log(response);
          tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3001]; ?>','');

        }
      });
    }

  }
  function cancelChange(hideclass,showclass,hide_btn,show_btn){
    $("."+hideclass).hide();
    $("."+showclass).show('slow');
    $("#"+hide_btn).hide();
    $("#"+show_btn).show('slow');
    $("#"+hide_btn+"_undo").hide();
    $(".datepickershow_"+hideclass).hide();
  }

function getTaskCheckList(id,aryposition,candidate_id){
  var transaction_table = "candidates";
  $('.checkcheckboxcopliance_'+id+'_'+aryposition).attr("disabled", true);
  $("#kt_modal_KTDatatable_remote").modal({
      backdrop: 'static',
      keyboard: false
  });
  jQuery.ajax({
      url: "views/custom_insert_transaction_activity_schedule_popup.php",
      data:'id='+id+"&aryposition="+aryposition+"&candidate_id="+candidate_id+'&transaction_table='+transaction_table,
      type: "POST",
      success:function(data){
        $('#popup1').html(data);
        $('#kt_modal_KTDatatable_remote').modal("show");
      },
      error:function (){}
  });
}

function updateTaskStatus(task_id,status){
  var transaction_table = "candidates";
  jQuery.ajax({
    url: "views/custom_update_onboardning_task_status_popup.php",
    data:'task_id='+task_id+"&status="+status+'&transaction_action_table='+transaction_table,
    type: "POST",
    success:function(data){
      $('#popup1').html(data);
      $('#kt_modal_KTDatatable_remote').modal("show");
    },
    error:function (){}
  });
}
  function UpdateTransactionActivitySchedule1(id){
    console.log(id);
    $.ajax({
      url: "ajax.php",
      data: "page=updatetransactionactivityschedule&linkurl=Y&rid="+id,
      type: "POST",
      success:function(response){
        console.log(response);
        var jsonres = JSON.parse(response);
        if(jsonres.status == "<?php echo ST_INPROGRESS; ?>"){
          $(".statusid_"+rid).html('<span class="form-label label label-md" style="color: #FFFFFF;    background:  #f5b700; border-color: ##f5b700;">In Progress </span>');
        }
        if(jsonres.status == "<?php echo ST_COMPLETE; ?>"){
          $('.sendapprove_'+rid).attr("disabled", true);
          $(".statusid_"+rid).html('<span class="form-label label label-md" style="color:#2ed8b6;background:#bc00dd;border-color:#FFFFFF;">Completed</span>');
        }
        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');

      }
    });

  }
  function showUpdateChecklist(id){
    $(".custom-popup-open").hide();
    console.log(id);
        // url: "views/custom_list_employee_compliances_update_popup.php",
        // data:'compid='+compid+"&aryposition="+key,
        // type: "POST",
        //     showOn: "both",
        //         dateFormat: date_format,
        //         minDate: -10,
        //         maxDate: +10,
        //         changeMonth: true,
        //         changeYear: true

        console.log("open");

        $(".custom-popup-slide-updatechecklist-"+id).show('slow');
      }
      function yetToRequestDocs(){
        $(".custom-popup-open").hide();
        console.log("closed");
      }

      function statsonlychange(id,status){
        $.ajax({
         url: "ajax.php",
         data:'page=updateonlystatus&hid='+id+'&table=candidate_documents&status='+status,
         type: "POST",
         success:function(data){
           var msg = <?php echo "'".$_SESSION['error_message']['9007']."'" ?>;
           console.log(msg);
           console.log(data);
           swal({
            title: msg,
            type: "success",
            buttonsStyling: false,
            confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",
            confirmButtonClass: "btn btn-danger",
            showCancelButton: false,
            cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
            cancelButtonClass: "btn btn-default"
          }).then(function(result) {
            if (result.value) {
              $('#kt_modal_KTDatatable_remote').modal('hide');
              window.location.reload();
            }
          });
        },
        error:function (){}
      });
      }

      function fileselected(id,valu){
        console.log(valu);
        var filename = valu.split('\\').pop();
        $("#attchmentfilename_"+id).text(filename);
       // attchmentfilename
     }
     function yetToRequestDocs(id){
      $(".custom-popup-open").hide();
      $(".custom-popup-slide-yettorequest_"+id).show('slow');
    }
    function closeSmallPopUp(){
      $(".custom-popup-open").hide('slow');
      console.log("closed");
    }

    function yetToSubmitBtn(id){
      var formdetails  = $("#docrequestform_"+id).serialize();
      jQuery.ajax({
        url: "ajax.php",
        data:"page=candidaterequestdocs&"+formdetails,
        type: "POST",
        success:function(data){
        $("#docssatusbtn_"+id).html('<span class="form-label label label-md" style="color: #FFFFFF;    background:  #bc00dd; border-color: #bc00dd;">Requested</span>')
        $("#docssatusbtnemt_"+id).html('');
        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
        closeSmallPopUp();
      },
      error:function (){}
    });
    }

    function updateTaskStatus(task_id,status){
      var transaction_action_table = "candidates";
      jQuery.ajax({
        url: "views/custom_update_onboardning_task_status_popup.php",
        data:'task_id='+task_id+"&status="+status+'&transaction_action_table='+transaction_action_table,
        type: "POST",
        success:function(data){
          $('#popup1').html(data);
          $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
      });
    }

function candidateUploadDocuments(){
      var candidate_id = "<?php echo $candidate_id; ?>";
      jQuery.ajax({
        url: "views/custom_view_candidate_overview_upload_document_popup.php",
        data:'candidate_id='+candidate_id,
        type: "POST",
        success:function(data){
          $('#popupm').html(data);
          $('#kt_modal_KTDatatable_remotem').modal("show");
        },
        error:function (){}
      });
}

</script>
  <div class="card-body">
    <div class="row mt-3">
      <div class="col-lg-3">
        <div class="card">
          <div class="card-header">
            <h5>About</h5>
            <div class="card-header-right">
              <button type="button" class="btn p-0 border-0 bg-transparent ti-pencil" onclick="editFormShow('contact_detailsL','contact_detailsT','edit_contact_details','save_contact_details','Edit')" id="edit_contact_details" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>

              <button type="button" class="btn p-0 border-0 bg-transparent ti-back-left" data-bs-toggle="tooltip" id="save_contact_details_undo" data-placement="bottom" title="" data-bs-original-title="Cancel" onclick="cancelChange('contact_detailsT','contact_detailsL','save_contact_details','edit_contact_details')" style="display: none" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              <button class="btn waves-effect waves-light btn-primary pt-1 pb-1 m-l-20" id="save_contact_details" onclick="editFormShow('contact_detailsT','contact_detailsL','save_contact_details','edit_contact_details','Save','contactdetailsfrom')" style="display: none" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save">Save</button>
            </div>
          </div>
          <div class="card-block">
            <form id="contactdetailsfrom">
            <div class="mb-2 pb-2">
              <span class="f-w-600 form-label">Job Role</span>
              <p class="f-14 mb-0 contact_detailsL" > <?php echo $candidates['candidate_current_role'] !=""?$candidates['candidate_current_role']:"N/A";?></p>
              <input aria-label="Candidate Current Role" class="form-control contact_detailsT" data-type="text"  type="text"  id="candidate_current_role"  name="candidate_current_role"  value="<?php echo $candidates['candidate_current_role']; ?>" style="display: none">
            </div>
            <div class="mb-2 pb-2">
              <span class="f-w-600 form-label">Department</span>
              <p class="f-14 mb-0 contact_detailsL"><?php echo $candidates['department_id'] !=""?$job_titles[$candidates['department_id']]:"N/A";?></p>
              <select aria-label="Department" class="form-select contact_detailsT" data-type="select" id="department_id" name="department_id" style="display: none">
                <?php foreach($departments as $key4=> $department){ ?>
                  <option <?php if($key4 == $candidates['department_id']){  ?> selected <?php
} ?>  value="<?php echo $key4; ?>"><?php echo $department;  ?></option>
                <?php } ?>
              </select>
            </div>
            <div class=" mb-2 pb-2">
              <span class="f-w-600 form-label">Designation</span>
              <p class="f-14 mb-0 contact_detailsL"><?php echo $candidates['job_tittle_id'] !=""?$job_titles[$candidates['job_tittle_id']]:"N/A";?></p>
              <select aria-label="Job Tittle" class="form-select  contact_detailsT" data-type="select" id="job_tittle_id" name="job_tittle_id" style="display: none">
                <?php foreach($job_titles as $key5=>$job_title){ ?>
                  <option <?php if($key5 == $candidates['job_tittle_id']){  ?> selected <?php
} ?>  value="<?php echo $key5; ?>"><?php echo $job_title;  ?></option>
                <?php } ?>
              </select>
            </div>
          </form>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h5>Key Contacts</h5>
          <div class="card-header-right">
            <button type="button" class="btn p-0 border-0 bg-transparent ti-pencil" onclick="editFormShow('personal_detailsL','personal_detailsT','edit_personal_details','save_personal_details','Edit')" id="edit_personal_details" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
            <button type="button" class="btn p-0 border-0 bg-transparent ti-back-left" data-bs-toggle="tooltip" id="save_personal_details_undo" data-placement="bottom" title="" data-bs-original-title="Cancel" onclick="cancelChange('personal_detailsT','personal_detailsL','save_personal_details','edit_personal_details')" style="display: none" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
            <button class="btn waves-effect waves-light btn-primary pt-1 pb-1 m-l-20" id="save_personal_details" onclick="editFormShow('personal_detailsT','personal_detailsL','save_personal_details','edit_personal_details','Save','personaldetailsfrom')" style="display: none" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save">Save</button>
          </div>
        </div>
        <div class="card-block">
         <form id="personaldetailsfrom">

          <div class=" mb-2 pb-2">
            <span class="f-w-600 form-label">HR Manager</span>
            <p class="f-14 mb-0 personal_detailsL"><?php echo $candidates['hr_connect'] !=""?$_SESSION['users'][$candidates['hr_connect']]:"N/A"; ?></p>
            <select aria-label="Hr Connect" class="form-select  personal_detailsT" data-type="select" id="hr_connect" name="hr_connect" style="display: none">
              <?php foreach($applicationusers as $key7=>$displayname){ ?>
                <option <?php if($candidates['hr_connect'] == $key7){  ?> selected <?php
} ?>  value="<?php echo $key7; ?>"><?php echo $displayname;  ?></option>
              <?php } ?>
            </select>
          </div>

        </form>
      </div>
    </div>

  </div>
  <div class="col-lg-6">
    <div class="card mb-4">
      <div class="row">
        <div class="col-lg-8">
          <ul class="nav md-tabs infisuite_tab mb-2">
            <li class="nav-item">
              <a class="nav-link active" data-bs-toggle="tab" href="#candidateprofiletab" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Profile Readiness</a>
            </li>

          </ul>
        </div>

      </div>
      <div class="tab-content card-block p-0">
        <div class="tab-pane active" id="candidateprofiletab" role="tabpanel">
          <div class="accordion-block color-accordion-block">
            <div class="color-accordion" id="color-accordion">
              <a class="accordion-msg b-none waves-effect waves-light f-w-700">Profile Readiness</a>
              <div class="accordion-desc p-0">
                <table class="table table-hover table-md">
                  <tbody>
                    <?php foreach($profilereadiness as $keylabel=>$profilereadines){
                      $complete_field =  $total_field = 0;
                      $total_field = count((array)$profilereadines);
                      foreach($profilereadines as $profilereadines){
                        if($candidates[$profilereadines] != ""){
                          $complete_field++;
                        }
                      }
                      $percentage = (($complete_field/$total_field)*100);
                      ?>
                      <tr>
                        <th scope="col">
                          <h6 class="mb-0 f-14"><i class="ti-pencil" onclick="window.open('index.php?option=COM_INSERT_CANDIDATE&ETYPE=<?php echo str_replace(" ","_",$keylabel); ?>&Rid=<?php echo $_GET['Rid']; ?>', '_blank');" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>  <?php echo $keylabel; ?></h6>
                        </th>
                        <th scope="col" style="text-align: right;">
                          <div class="d-flex align-items-center w-100px w-sm-120px flex-column ">
                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                              <span class="f-12 f-w-600">Profile </span>
                              <span class="f-12 f-w-600"><?php echo round($percentage); ?>%</span>
                            </div>
                            <div class="col-md-12">
                              <div class="progress progress-xs">
                                <div class="progress-bar progress-bar-<?php echo round($percentage)>=50?"success":"danger"; ?>" role="progressbar" style="width:  <?php echo round($percentage); ?>%;" aria-valuenow="<?php echo round($percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                            </div>
                          </div>
                        </th>
                      </tr>
                    <?php } ?>
                  </tbody></table>
                </div>
              </div>
            </div>
          </div>
    </div>
  </div>
</div>
<div class="col-lg-3">
      <div class="card">
        <div class="card-header">
          <h5>Upload Documents</h5>
          <div class="card-header-right">
             <button type="button" class="btn waves-effect waves-light btn-primary" onclick="candidateUploadDocuments()"><i class="ti-upload m-r-10 text-white"></i></button>
          </div>
        </div>
        <div class="card-block custom-scroll" style=" height: 300px;" id="uploadedalldocuments">
          <?php if(count((array)$application_attachments) !=0){ ?>
          <table class="table table-hover m-b-0" style="font-size:13px;">
            <body>
              <?php
              $attachmentpath = $_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$candidate_id."/Documents";
              foreach($application_attachments as $apatach){ ?>
              <tr>
                <th scope="col" class="w-b-w-s p-2">
                  <a href="uploads/<?php echo $attachmentpath."/".$apatach['actual_file_name']; ?>" download>
                    <span class="d-block"><i class="ti-file"></i> <?php echo $apatach['file_name']; ?></span>
                    <span class="d-block"><i class="ti-alarm-clock"></i> <?php echo $apatach['created_date']; ?></span>
                  </a>
                </th>
              </tr>
            <?php } ?>
            </body>
          </table>
        <?php  }else{  ?>
            <div class="card-px text-center py-20 m-t-30" >
              <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Upload Documents </p>
              <button type="button" class="btn waves-effect waves-light btn-success" onclick="candidateUploadDocuments()"><i class="ti-upload"></i> Upload Document</button>
              <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
              </div>
            </div>
        <?php } ?>
        </div>
    </div>

</div>

<?php echo $todisplaythree = todisplaythree(array('table'=>'candidates','originator'=>$rpdata['created_by'],'encode'=>'yes','id'=>$_GET['Rid'],'dis_query'=>'no','dis_timeline'=>'yes','dis_comments'=>'no','dis_track'=>'no')); ?>
</div>
</div>
<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
