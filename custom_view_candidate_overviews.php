<?php 
$_SESSION['apply_js']['todo'] = 1;
$_SESSION['apply_js']['multiselest'] = '1';

$rid =  encodeId($_REQUEST['Rid']);
$rid = urldecode($rid);
$cand = explode(':', $rid);
$candidate_id = $cand['1'];
$table_name = "candidates";
//echo $candidate_id; exit;
//$option_c = $_GET['option'];
//$RID_c = $_GET['Rid'];
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
   // echo "<pre>"; print_r($response); echo "</pre>";
  if($response){ ?>
    <script type="text/javascript">
      document.onreadystatechange = function () {
        if (document.readyState == "complete") {
          tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
        } 
      }
    </script>
    <?php   // header("location:index.php?option=COM_INSERT_CANDIDATE&NI=".$_GET['NI']."&Rid=".$RID_c);
  }
}
  //request documents
if(isset($_POST['submitrequerd_docs'])){
  $url = API."?mode=addorupdatecandaitedocument&method=post";
  $_POST['company_id'] = $_SESSION['company_id'];
  $response = file_post_contents($url, $_POST, 'insert');
  $response = json_decode($response,1); 
          //echo "<pre>"; print_r($response); echo "</pre>";
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
            //header("location:index.php?option=COM_INSERT_CANDIDATE&NI=1&Rid=".$RID_c);   
  }
}
  //request documents



$admin_type = 0;
if(in_array(RECRUITINGMANAGER_ROLE,(array)$user_roles) || in_array(RECRUITINGTEAM_ROLE,(array)$user_roles) || in_array(ADMIN_ROLE,(array)$user_roles)){
  $admin_type = 1;
}
$document_category_id = 1;
$url = API."?mode=getcandidateoverviews&candidate_id=".$candidate_id."&admin_type=".$admin_type."&document_category_id=".$document_category_id; 
$api = callAPI($url);
$result = json_decode($api,1); 
$candidate_documents = $result['candidate_documents'];
$candidates = $result['candidates'];
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
//echo "<pre>"; print_r($document_lists); echo "</pre>";
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
foreach($result['transaction_activity_schedule'] as $transaction_activity_schedule){
  $transactionactivityschedule[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
  if($transaction_activity_schedule['current_status'] == ST_APPROVED){
    $complitedtask[$transaction_activity_schedule['process_id']]++;
  }
  if($transaction_activity_schedule['link_page'] == '0'){
    $linktransactionactivityschedule[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
    if($transaction_activity_schedule['current_status'] == ST_APPROVED){
      $linkcomplitedtask[$transaction_activity_schedule['process_id']]++;
    }
  }
  $totaltask[$transaction_activity_schedule['process_id']]++;
}

//print_r($linktransactionactivityschedule);
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

//$job_titles = $result['job_titles'];





 // echo "<pre>dcdcdcbdcdfhvb";print_r($checkl);
 // echo "</pre>";

$profilereadiness = array("Contact Details"=>array('first_name','middle_name','last_name','display_name','phone','email_id','address','post_code','bio'),"Personal Details"=>array('gender','date_of_birth','marital_status','wedding_date','blood_group','father_name','spouse_name','adhar_no','pan_number','hiring_source'),"Dependents"=>array('employee_dependents'),"Current Employment"=>array('employee_contacts','current_location','reason_for_change','relocate','current_ctc','expected_ctc','no_of_reportees','job_grade,pay_template','job_type,location_id'),"Awards and Accolades"=>array('candidate_awards'),"HR Details"=>array('first_role','candidate_current_role','notice_period','np_negotiatable','first_pay','current_pay','gaps_in_employment_reason','performance_ratings'),"Skills"=>array('skills'),"Work Experiences"=>array('candidate_experiances'),"Education Details"=>array('candidate_qualifications'));
$stausresult = ViewStatusData();
//echo "<pre>"; print_r($data); echo "</pre>";

$due_date_period_list = $result['period_list'];

// $statuslist  = getstatusprogresslist('candidate_documents');
// foreach($statuslist as $statuslist){
//   $candidatedocumentsstatusname[$statuslist['status']] = $statuslist['name']; 
//   $candidatedocumentsstatusnameaction[$statuslist['status']] = $statuslist['action_label'];
// }

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
    //alert(id+" - "+aryposition);
    $('.checkcheckboxcopliance_'+id+'_'+aryposition).attr("disabled", true);
    $("#kt_modal_KTDatatable_remote").modal({
      backdrop: 'static',
      keyboard: false
    });
    jQuery.ajax({
      url: "views/custom_insert_transaction_activity_schedule_popup.php",
      data:'id='+id+"&aryposition="+aryposition+"&candidate_id="+candidate_id,
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
      if(jsonres.status == "3"){
        $(".statusid_"+rid).html('<label class="form-label label label-md" style="color: #FFFFFF;    background:  #f5b700; border-color: ##f5b700;">In Progress </label>');
      }
      if(jsonres.status == "10"){
        $('.sendapprove_'+rid).attr("disabled", true);
        $(".statusid_"+rid).html('<label class="form-label label label-md" style="color: #FFFFFF;    background:  #fcca46; border-color: ##fcca46;">Approval Pending </label>');
      }
      tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');




       // $(".statusid_"+id).html('<label class="form-label label label-md" style="color: ###4f2424;    background:  #17c3b2; border-color: #17c3b2;">Approved</label>');
       // tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
        //$('#kt_modal_KTDatatable_remote').modal("hide");
      }
    });


  }
  function showUpdateChecklist(id){
    $(".custom-popup-open").hide();
    console.log(id);
         // $(".custom-popup-open").empty();
        //  jQuery.ajax({
        // url: "views/custom_list_employee_compliances_update_popup.php",
        // data:'compid='+compid+"&aryposition="+key,
        // type: "POST",
        // success:function(data){
        //   //console.log(data);
        //   $(".custom-popup-slide-updatechecklist-"+compid+"-"+key).html(data);
        //   $(".custom-popup-slide-updatechecklist-"+compid+"-"+key).show('slow');
        //   $(".js-example-basic-single").select2();
        //       $(".js-example-disabled-results").select2();
        //       $( ".datepicker3" ).datepicker({
        //     showOn: "both",
        //     buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
        //         dateFormat: date_format,
        //         minDate: -10,
        //         maxDate: +10,
        //         changeMonth: true,
        //         changeYear: true
        //       });
        
        // }
        // }); 
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
           swal.fire({              
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
        //console.log(data);
        $("#docssatusbtn_"+id).html('<label class="form-label label label-md" style="color: #FFFFFF;    background:  #bc00dd; border-color: #bc00dd;">Requested</label>')
        $("#docssatusbtnemt_"+id).html('');
        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
        closeSmallPopUp();
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
              <i class="ti-pencil" onclick="editFormShow('contact_detailsL','contact_detailsT','edit_contact_details','save_contact_details','Edit')" id="edit_contact_details" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit"></i> 
              
              <i class="ti-back-left" data-bs-toggle="tooltip" id="save_contact_details_undo" data-placement="bottom" title="" data-bs-original-title="Cancel" onclick="cancelChange('contact_detailsT','contact_detailsL','save_contact_details','edit_contact_details')" style="display: none"></i>
              <button class="btn waves-effect waves-light btn-primary pt-1 pb-1 m-l-20" id="save_contact_details" onclick="editFormShow('contact_detailsT','contact_detailsL','save_contact_details','edit_contact_details','Save','contactdetailsfrom')" style="display: none" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save">Save</button>
            </div>
          </div>
          <div class="card-block">
           <form id="contactdetailsfrom">
            <div class="mb-2 pb-2">
              <label class="f-w-600 form-label">Job Role</label>
              <p class="f-14 mb-0 contact_detailsL" > <?php echo $candidates['candidate_current_role'] !=""?$candidates['candidate_current_role']:"N/A";?></p>
              <input class="form-control contact_detailsT" data-type="text"  type="text"  id="candidate_current_role"  name="candidate_current_role"  value="<?php echo $candidates['candidate_current_role']; ?>" style="display: none">
            </div>
            <div class="mb-2 pb-2">
              <label class="f-w-600 form-label">Department</label>
              <p class="f-14 mb-0 contact_detailsL"><?php echo $candidates['department_id'] !=""?$job_titles[$candidates['department_id']]:"N/A";?></p>
              <select class="form-select contact_detailsT" data-type="select" id="department_id" name="department_id" style="display: none">
                <?php foreach($departments as $key4=> $department){ ?>
                  <option <?php if($key4 == $candidates['department_id']){  ?> selected <?php } ?>  value="<?php echo $key4; ?>"><?php echo $department;  ?></option>
                <?php } ?>
              </select>
            </div> 
            <div class=" mb-2 pb-2">
              <label class="f-w-600 form-label">Designation</label>
              <p class="f-14 mb-0 contact_detailsL"><?php echo $candidates['job_tittle_id'] !=""?$job_titles[$candidates['job_tittle_id']]:"N/A";?></p>
              <select class="form-select  contact_detailsT" data-type="select" id="job_tittle_id" name="job_tittle_id" style="display: none">
                <?php foreach($job_titles as $key5=>$job_title){ ?>
                  <option <?php if($key5 == $candidates['job_tittle_id']){  ?> selected <?php } ?>  value="<?php echo $key5; ?>"><?php echo $job_title;  ?></option>
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
            <i class="ti-pencil" onclick="editFormShow('personal_detailsL','personal_detailsT','edit_personal_details','save_personal_details','Edit')" id="edit_personal_details" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit"></i> 
            <i class="ti-back-left" data-bs-toggle="tooltip" id="save_personal_details_undo" data-placement="bottom" title="" data-bs-original-title="Cancel" onclick="cancelChange('personal_detailsT','personal_detailsL','save_personal_details','edit_personal_details')" style="display: none"></i>
            <button class="btn waves-effect waves-light btn-primary pt-1 pb-1 m-l-20" id="save_personal_details" onclick="editFormShow('personal_detailsT','personal_detailsL','save_personal_details','edit_personal_details','Save','personaldetailsfrom')" style="display: none" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save">Save</button>
          </div>
        </div>


        <div class="card-block">
         <form id="personaldetailsfrom">
          <div class="row mb-2 pb-2">
            <label class="f-w-600 form-label">Manager</label>
            <p class="f-14 mb-0"><?php echo $job_posting_candidates['requested_by'] !=""?$_SESSION['users'][$job_posting_candidates['requested_by']]:"N/A"; ?></p>
          </div>
          <div class=" mb-2 pb-2">
            <label class="f-w-600 form-label">HR Manager</label>
            <p class="f-14 mb-0 personal_detailsL"><?php echo $candidates['hr_connect'] !=""?$_SESSION['users'][$candidates['hr_connect']]:"N/A"; ?></p>
            <select class="form-select  personal_detailsT" data-type="select" id="hr_connect" name="hr_connect" style="display: none">
              <?php foreach($applicationusers as $key7=>$displayname){ ?>
                <option <?php if($candidates['hr_connect'] == $key7){  ?> selected <?php } ?>  value="<?php echo $key7; ?>"><?php echo $displayname;  ?></option>
              <?php } ?>
            </select>
          </div> 
          <div class="row mb-2 pb-2">
            <label class="f-w-600 form-label">Recruiter</label>
            <p class="f-14 mb-0"><?php echo $job_posting_candidates['recruitment_manager'] !=""?$_SESSION['users'][$job_posting_candidates['recruitment_manager']]:"N/A"; ?></p>
          </div>
          <!--           <div class="row mb-2 pb-2">
            <h4 class="f-w-600 f-16 mb-0">Mentor</h4>
            <p class="f-14 mb-0">Rohan</p>
          </div> -->
        </form>
      </div>
    </div>
      <!-- <div class="card">
        <div class="card-header">
          <h5>Assigned To</h5>
           <div class="card-header-right">
            <a class="text-primary" href=""><i class="ti-pencil"></i></a>
          </div> 
        </div>
        <div class="card-block">
          <div class="row mb-2 pb-2">
            <h4 class="f-w-600 f-16 mb-0">Manager</h4>
            <p class="f-14 mb-0">1 Person</p>
          </div>
          <div class="row mb-2 pb-2">
            <h4 class="f-w-600 f-16 mb-0">Buddy</h4>
            <p class="f-14 mb-0">3 People</p>
          </div> 
        </div>
      </div> -->
      <?php echo $todisplaythree = todisplaythree(array('table'=>'candidates','originator'=>$rpdata['created_by'],'encode'=>'yes','id'=>$_GET['Rid'],'dis_query'=>'no','dis_timeline'=>'yes','dis_comments'=>'no','dis_track'=>'no')); ?>
    </div>




    <div class="col-lg-6">
      <div class="card mb-4">
      <!--   <ul class="nav md-tabs infisuite_tab mb-2" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#candidateprofiletab" role="tab">Profile Readiness</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#candidatettask" role="tab"> Tasks</a>
          </li> 
        </ul> -->
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
                          <td>
                            <h6 class="mb-0 f-14"><i class="ti-pencil" onclick="window.open('index.php?option=COM_INSERT_CANDIDATE&ETYPE=<?php echo str_replace(" ","_",$keylabel); ?>&Rid=<?php echo $_GET['Rid']; ?>', '_blank');" ></i>  <?php echo $keylabel; ?></h6>
                          </td>
                          <td align="right">
                            <div class="d-flex align-items-center w-100px w-sm-120px flex-column ">
                              <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                <span class="f-12 f-w-600">Profile </span>
                                <span class="f-12 f-w-600"><?php echo round($percentage); ?>%</span>
                              </div>
                              <div class="col-md-12">
                                <div class="progress progress-xs">
                                  <div class="progress-bar progress-bar-<?php echo round($percentage)>=50?"success":"danger"; ?>" role="progressbar" style="width:  <?php echo round($percentage); ?>%;" aria-valuenow=" 55" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                              </div>
                            </div>
                          </td>
                        </tr>
                      <?php } ?>

                    </tbody></table>

                  </div>

<?php /** ?>
               <a class="accordion-msg b-none waves-effect waves-light f-w-700">Documents <span class="float-end"><?php echo $totaldocuments['uploaded_cnt']
            ;?> of <?php echo $totaldocuments['total'];?> Uploaded</span></a>
                <div class="accordion-desc">
                    <div class="row">
                      
                      <form method="post"  enctype="multipart/form-data">
                        <div class="accordion-block color-accordion-block">
                <div class="color-accordion" id="color-accordion">
            <?php 
            $subbtn = 1;
            foreach($parentdocumentslist as $parentdocuments){ 
                      $docsdetails = $allvdata_details[$parentdocuments['id']];
                      $vdata_details = $docsdetails; 
                      $candidateuserid = 6;
                      $candidateuserid = $data['user_id'];
                      if($candidateuserid == $_SESSION['userid']){
                        $vdata_details= array();
                            foreach ($docsdetails as $detail1){ 
                                if($checkl[$detail1['id']]){
                                    $vdata_details[] = $detail1;
                                }
                            }                            
                      }
                       if(count((array)$vdata_details) != 0){
                        ?>
                  <a class="accordion-msg b-none waves-effect waves-light f-w-700"><i class="fa fa-folder"></i> <?php echo ucwords(strtolower($parentdocuments['document_name'])); ?></a>
                  <div class="accordion-desc p-0">
                    <table class="table table-hover m-b-0 " style="font-size:13px;">
                      <tbody>
                        <?php foreach ($vdata_details as $details){ 
                          $candidatedocument = $checkl[$details['id']];
                          if($candidatedocument['current_status'] ==""){
                            $statucolorname = $stausresult[ST_ACTIVE];
                            $candidatedocument['current_status'] = ST_ACTIVE; 
                          }else{
                            $statucolorname = $stausresult[$candidatedocument['current_status']]; 
                          }
                           $displaydocuments = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$table_name."/".$candidate_id."/Documents/".$candidatedocument['document_shared']; 
                          ?>
                          <tr id="custom-popup">
                            <td class="w-b-w-s">
                            
                                <?php if($candidatedocument['document_shared']!=""){  ?> 
                                <a class="text-primary text-capitalize f-14" href="<?php  echo $displaydocuments; ?>"  target="_blank" download>
                            <?php } ?>
                                <?php if($candidatedocument['document_shared']!=""){  ?>
                                      <?php if($candidatedocument['current_status'] == ST_REJECTED){ ?> 
                                        <i class="fa fa-times text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Rejected"></i> 
                                      <?php }elseif($candidatedocument['current_status'] == ST_APPROVED){ ?>  
                                        <i class="fa fa-thumbs-up" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Approved"></i>
                                      <?php }else{  ?>
                                       <i class="fa fa-hourglass-half" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Pendding"></i>
                                     <?php } }else{ ?>
                                      <i class="fa fa-upload" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Uploading Pendding"></i>
                                    <?php }  
                                    echo ucwords($details['document_name']); 
                                       ?>
                                     </a>
                            </td>
                            <td id="docssatusbtn_<?php echo $details['id']; ?>">
                                <label class="form-label label label-md" style="color: <?php echo $statucolorname['color']; ?>;    background:  <?php echo $statucolorname['background_color']; ?>; border-color: <?php echo $statucolorname['background_color']; ?>;"><?php echo $candidatedocumentsstatusname[$candidatedocument['current_status']]; ?></label>
                            </td>
                            <td align="right">
                              <div id="docssatusbtnemt_<?php echo $details['id']; ?>">
                              
                              <?php if($candidatedocument['current_status'] =="" || $candidatedocument['current_status'] ==ST_ACTIVE){ 
                                $statucolorname = $stausresult[ST_INPROGRESS];
                                ?>
                                <button onclick="yetToRequestDocs('<?php echo $details['id']; ?>')" class="btn btn-primary desktop-view text-capitalize" type="button" style="color: <?php echo $statucolorname['color']; ?>;    background:  <?php echo $statucolorname['background_color']; ?>; border-color: <?php echo $statucolorname['background_color']; ?>;"><?php echo $candidatedocumentsstatusnameaction[ST_INPROGRESS]; ?></button>
                              <?php }elseif($candidatedocument['current_status'] ==ST_INPROGRESS){ $subbtn = 0;?>

                                    <div class="image-input image-input-empty image-input-outline mr-10 mt-2" id="kt_image_5"  style=" height:0px; ">
                                       <label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Uploade File">
                                         <i class="ti-clip" style="font-size: 20px;"></i>
                                         <input type="file" name="image_<?php echo $candidatedocument['id']; ?>" onchange="fileselected('<?php echo $candidatedocument['id'] ?>',this.value)"/>
                                         <input type="hidden" name="hidimage_<?php echo $candidatedocument['id']; ?>" value="<?php echo $candidatedocument['document_shared']; ?>"/>
                                       </label>

                                    </div>
                                    <div class="text-right">
                                      <span id="attchmentfilename_<?php echo $candidatedocument['id'] ?>" class="text-info f-12"></span>
                                    </div>

                              <?php }elseif($candidatedocument['current_status'] ==ST_COMPLETE){ ?>
                                
                                <button class="btn btn-primary desktop-view text-capitalize" onclick="statsonlychange(<?php echo $candidatedocument['id'] ?>,<?php echo ST_APPROVED; ?>)"  name="status" id="status" value="<?php echo ST_APPROVED; ?>)" style="color: <?php echo $stausresult[ST_APPROVED]['color']; ?>;    background:  <?php echo $stausresult[ST_APPROVED]['background_color']; ?>; border-color: <?php echo $stausresult[ST_APPROVED]['background_color']; ?>;" >Approved</button>
                                <button class="btn btn-primary desktop-view text-capitalize" onclick="statsonlychange(<?php echo $candidatedocument['id'] ?>,<?php echo ST_REJECTED; ?>)"  name="status" id="status" value="<?php echo ST_REJECTED; ?>)" style="color: <?php echo $stausresult[ST_REJECTED]['color']; ?>;    background:  <?php echo $stausresult[ST_REJECTED]['background_color']; ?>; border-color: <?php echo $stausresult[ST_REJECTED]['background_color']; ?>;">Rejected</button>

                              <?php } ?>
                              
                               </div>
                          <?php if($candidatedocument['current_status'] =="" || $candidatedocument['current_status'] ==ST_ACTIVE){  ?>
                                <div class="custom-popup-open custom-popup-slide-yettorequest_<?php echo $details['id']; ?> text-start" style="display: none;right: 0px;top: 55px; width: 50%;">
                                  <div class="card">
                                    <div class="card-header">
                                      <h5><?php echo ucwords($details['document_name']); ?> Request</h5>
                                      <div class="card-header-right">
                                        <i class="ti-close" onclick="closeSmallPopUp()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Close"></i>
                                      </div>
                                    </div>
                                    <div class="card-body"> 
                                      <div class="form-group">
                                        <label class="form-label">Joining Date : <?php echo fromDate($data['reporting_date']); ?></label>
                                      </div>
                                      <form id="docrequestform_<?php echo $details['id']; ?>">
                                      <input type="hidden" name="category_id" value="<?php echo $parentdocuments['id']; ?>">  
                                      <input type="hidden" name="candidate_id" value="<?php echo $candidate_id; ?>"> 
                                      <input type="hidden" name="document_id" value="<?php echo $details['id']; ?>"> 
                                       <input type="hidden" name="reporting_date" value="<?php echo $data['reporting_date']; ?>"> 
                                      <div class="row">
                                        <div class="form-group col-lg-8 w-full">
                                          <label class="form-label d-block">Due Date</label>
                                           <select name="before_or_after" style="width:150px;" class="form-control form-select js-example-basic-single" >
                                              <option value="AT">Any Time</option>
                                              <?php foreach ($due_date_period_list as $due){  ?>
                                                <option value="<?php echo $due['genc_detail_code'];?>" ><?php echo $due['genc_detail_name']." Joining";?></option>
                                              <?php } ?>
                                            </select> 
                                        </div>
                                        <div class="form-group col-lg-4">
                                          <label class="form-label">Days</label>
                                          <input type="text" name="day" class="form-control" value="0"/>
                                        </div>
                                      </div>
                                    </form>
                                      <!-- <div class="form-group">
                                        <label class="form-label">Due Date : <span id="docduedate_<?php echo $details['id']; ?>"> - </span></label>
                                         
                                      </div> -->
                                    </div>
                                    <div class="card-footer" >
                                        <button type="button" class="btn btn-primary" onclick="yetToSubmitBtn('<?php echo $details['id']; ?>')" >Request Document</button>
                                    </div>
                                  </div>
                                </div>
                               <?php } ?> 
                            </td>
                          </tr>
                          <?php }?>  
                      </tbody>
                    </table>
                  </div>
             <?php } }?>
             </div>
            </div>
            <?php if($subbtn == 0 AND $candidateuserid == $_SESSION['userid']){ ?>
             <div class="card-footer">
                <div class="row">
                  <div class="col-lg-4">                   
                   <input type="hidden" name="candidate_id" value="<?php echo $candidate_id;  ?>" />
                   <input type="hidden" name="submit_doc" value="submit_doc" />  
                   <button id="kt_login_signin_submit" class="btn btn-primary mr-2"   name="submitto" value="exit"><i class="la la-check"></i>Submit</button>
                 </div>
               </div>
             </div>
            <?php } ?>
            </form>
            </div>
          </div>

          <?php **/ ?>


            <a class="accordion-msg b-none waves-effect waves-light f-w-700">Task </a>
                <div class="accordion-desc">
                   <div class="accordion-block color-accordion-block">
                      <div class="color-accordion" id="color-accordion">
    <?php if(count((array)$transactionactivityschedule) != 0){  
            foreach($transactionactivityschedule as $key1=>$transactionactivityschedule){ ?> 
              <a class="accordion-msg b-none waves-effect waves-light f-w-700"><?php echo $taskscategories[$key1];?>
              <span class="float-end"><?php echo $totaltask[$key1]
              ;?> of <?php echo $complitedtask[$key1] !=""?$complitedtask[$key1]:"0";?> Completed</span>
            </a> 
            <div class="accordion-desc">
              <table class="table table-hover">
                <tbody>
                  <?php foreach($transactionactivityschedule as $tas){ ?>
                    <tr id="custom-popup" >
                      <td class="w-b-w-s">
                        <?php if($tas['link_page'] == '0'){?>
                         <a href="<?php echo $tas['page_url'];  ?>" target="_blank" onclick="UpdateTransactionActivitySchedule1('<?php echo $tas['id']; ?>')" class="text-primary"><h6 class="mb-0 f-14" ><i class="ti-new-window"></i> <?php echo $tas['task_name']; ?></h6></a>
                       <?php  }else{ ?> 
                        <h6 class="mb-0 f-14" onclick="showUpdateChecklist('<?php echo $tas['id']; ?>')"><i class="ti-check-box"></i> <?php echo $tas['task_name']; ?></h6>
                      <?php } ?> 
                    </td>
                    <td align="center" class="statusid_<?php echo $tas['id']; ?>">
                     <?php $stausresultv = $stausresult[$tas['current_status']]; 
                     ?>
                     <label class="form-label label label-success" style="color: <?php echo "#".$stausresultv['color']; ?>;    background:  <?php echo $stausresultv['background_color']; ?>; border-color: <?php echo $stausresultv['background_color']; ?>;" ><?php echo $stausresultv['name']; ?></label>

                     <?php if($tas['check_list'] !=""){ ?>
                      <div class="custom-popup-open custom-popup-slide-updatechecklist-<?php echo $tas['id']; ?> text-start" style="display: none;left: 18px;top: 55px; ">
                        <div class="card">
                          <div class="card-header">
                            <h5>Task</h5>
                            <div class="card-header-right">
                              <i class="ti-close" onclick="closeSmallPopUp()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed"></i>
                            </div>
                          </div>
                          <div class="card-body"> 
                            <table width="100%">
                              <tbody>

                                <?php 
                                $check_list = json_decode($tas['check_list'], 1);
                                foreach($check_list as $keyval=>$list){   
                                  $checkboxcl=$classcheck="";
                                  if($list['checklist_completed']=="Y"){ 
                                    $taskstatus++; 
                                    $classcheck="check-task check-task done-task";
                                    $checkboxcl = "checked disabled";
                                  } 
                                  ?>
                                  <tr>
                                   <td width="90%"> 
                                    <div class="to-do-list">
                                      <div class="checkbox-fade fade-in-primary" onchange="getTaskCheckList('<?php echo $tas['id']; ?>','<?php echo $keyval; ?>','<?php echo $candidate_id; ?>')">
                                        <label class="form-label check-task <?php echo $classcheck; ?> campliancecros_<?php echo $tas['id']."_".$keyval; ?>">
                                          <input type="checkbox" value="" <?php echo $checkboxcl; ?>  class="checkcheckboxcopliance_<?php echo $tas['id']."_".$keyval; ?>" >
                                          <span class="cr">
                                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                          </span>
                                          <span><?php echo $list['name'];?> <?php if($list['mandatory_checklist']=="Y"){ ?><span class="text-danger">*</span><?php } ?></span>

                                        </label>
                                      </div>

                                    </div>
                                  </td> 
                                  <td width="10%">
                                    <span class="float_end">
                                      <?php if($list['downloadfile'] !=""){ ?>
                                        <a href="<?php echo "uploads/all/".$list['downloadfile']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Demo Form Download" download><i class="ti-cloud-down"></i></a> 
                                        <?php
                                      }if($list['attachment_name'] !=""){ 
                                       $path = $_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$candidate_id."/Documents/";
                                       ?>
                                       <a href="<?php echo $path.$list['attachment_name']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Uploaded Form Download" download><i class="ti-cloud-down m-l-20"></i></a>
                                     <?php } ?> 
                                   </span>
                                 </td>
                               </tr>
                             <?php }  ?>
                           </tbody>
                         </table>
                       </div>
                     </div>
                   </div>
                 <?php  } ?>
               </td>
             </tr>
           <?php } ?>
         </tbody>
       </table>
     </div>
   <?php }  }else{ ?>
     <div class="card-px text-center py-20 mt-2">
      <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Task </p>   
      <div class="text-center px-4">
        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
      </div>   
    </div> 
  <?php } ?>
</div>
</div>
</div>

























































                <?php /** ?>  
                  <a class="accordion-msg b-none waves-effect waves-light f-w-700">Forms</a>
                  <div class="accordion-desc">
                   <?php if(count((array)$linktransactionactivityschedule) != 0){  foreach($linktransactionactivityschedule as $key1=>$transactionactivityschedule11){?> 
                    <a class="accordion-msg b-none waves-effect waves-light f-w-700"><?php echo $taskscategories[$key1];?>
                    <span class="float-end"><?php echo count((array)$linktransactionactivityschedule[$key1])
                    ;?> of <?php echo $linkcomplitedtask[$key1] !=""?$linkcomplitedtask[$key1]:"0";?> Completed</span>
                  </a> 
                  <div class="accordion-desc">
                    <table class="table table-hover">
                      <tbody>
                        <?php foreach($transactionactivityschedule11 as $tas){ 
                          ?>
                          <tr id="custom-popup" >
                            <td class="w-b-w-s">

                              <?php if($tas['link_page'] == '0'){?>
                                <!--    <a href="<?php echo $tas['page_url']; ?>" target="_blank"><h6 class="mb-0 f-14" onclick="showUpdateChecklist('<?php echo $tas['id']; ?>')"><i class="ti-new-window"></i> <?php echo $tas['task_name']; ?></h6></a> -->
                                <a href="<?php echo $tas['page_url'];  ?>" target="_blank" onclick="UpdateTransactionActivitySchedule1('<?php echo $tas['id']; ?>')" class="text-primary"><h6 class="mb-0 f-14" ><i class="ti-new-window"></i> <?php echo $tas['task_name']; ?></h6></a>
                              <?php  }else{ ?> 
                                <h6 class="mb-0 f-14" onclick="showUpdateChecklist('<?php echo $tas['id']; ?>')"><i class="ti-check-box"></i> <?php echo $tas['task_name']; ?></h6>
                              <?php } ?> 
                            </td>
                            <td align="center" class="statusid_<?php echo $tas['id']; ?>">
                             <?php $stausresultv = $stausresult[$tas['current_status']]; 
                             ?>
                             <label class="form-label label label-success" style="color: <?php echo "#".$stausresultv['color']; ?>;    background:  <?php echo $stausresultv['background_color']; ?>; border-color: <?php echo $stausresultv['background_color']; ?>;" ><?php echo $stausresultv['name']; ?></label>
                           </td>
                         </tr>
                       <?php } ?>
                     </tbody>
                   </table>

                 </div>
               <?php }  }else{ ?>
                 <div class="card-px text-center py-20 mt-2">
                  <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Task </p>   
                  <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                  </div>   
                </div> 
              <?php } ?>
            </div>
            <?php **/ ?>
            <a class="accordion-msg b-none waves-effect waves-light f-w-700">Policies And Procedures</a>
            <div class="accordion-desc">
              <table class="table table-hover">
                <tbody><tr>
                  <td>
                    <h6 class="mb-0 f-14">Drivers License</h6>
                    <p>Uploaded on 22 March 2023</p>
                  </td>
                  <td align="right"><label class="form-label label label-success">Completed</label></td>
                  <td align="right">
                    <div class="dropdown-primary dropdown open">
                      <button class="btn btn-primary dropdown-toggle waves-effect waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
                        <a class="dropdown-item waves-light waves-effect d-block" href="#">Action</a>
                        <a class="dropdown-item waves-light waves-effect d-block" href="#">Another action</a>
                        <a class="dropdown-item waves-light waves-effect d-block" href="#">Something else</a>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody></table>
            </div>
          </div>
        </div>


      </div>  

















































































































































      <div class="tab-pane" id="candidatettask" role="tabpanel">
        <div class="accordion-block color-accordion-block">
          <div class="color-accordion" id="color-accordion">
            <a class="accordion-msg b-none waves-effect waves-light f-w-700">Documents <span class="float-end"><?php echo $totaldocuments['uploaded_cnt']
            ;?> of <?php echo $totaldocuments['total'];?> Uploaded</span></a>


            <div class="accordion-desc">
             <ul class="nav md-tabs infisuite_tab mb-2" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#uploaddocumentstab" role="tab">Upload Documents</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#reqquestdocumentstab" role="tab">Request Documents</a>
              </li>
            </ul>

            <div class="tab-content card-block p-0">
              <div class="tab-pane active" id="uploaddocumentstab" role="tabpanel">
                <form method="post"  enctype="multipart/form-data" class="row">
                  <?php
               /*   if(count((array)$candidatedocumentlist)>=1){      
                    $displaydocuments = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$table_name."/".$candidate_id."/Documents/";
                    foreach ($candidatedocumentlist as $parentkey => $parentdocuments){
                      ?>
                      <div class="col-md-6 col-sm-6 col-xs-12 mb-10">
                        <div class="pricing ui-ribbon-container">

                          <div class="title" style="height: 50px !important;border-radius: 5px;">
           count                <h6><?php echo ucwords($documentlist[$parentkey]); ?></h6>
                         </div>
                         <div class="x_content">
                          <div class="">
                            <div class="pricing_features">    
                              <?php
                              foreach($parentdocuments as $value){ ?>

                               <div style="min-height: 38px; box-shadow: 0px 1px 9px #cccccc; padding: 2px 10px 2px 10px;" class="alert alert-custom alert-white fade show" role="alert">
                                <div class="alert-text">
                                 <div class="d-flex align-items-center mb-0">
                                  <div class="symbol symbol-35 symbol-success mr-3 mt-1">
                                    <?php if($value['document_shared']!=""){  ?>

                                      <?php if($value['current_status'] == ST_REJECTED){ ?> 
                                        <i class="fa fa-times text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Rejected"></i> 
                                      <?php }elseif($value['current_status'] == ST_APPROVED){ ?>  
                                        <i class="fa fa-thumbs-up" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Approved"></i>
                                      <?php }else{  ?>
                                       <i class="fa fa-hourglass-half" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Pendding"></i>
                                     <?php } }else{ ?>
                                      <i class="fa fa-upload" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Uploading Pendding"></i>
                                    <?php }     ?>
                                  </div>
                                  <div class="d-flex flex-column font-weight-bold w-100">
                                   <a <?php if($value['document_shared']!=""){ ?> class=" nav-link text-active-primary me-6 active text-capitalize" href="<?php  echo $displaydocuments.$value['document_shared'];   ?>"  target="_blank" <?php }else{ echo "class='nav-link me-6 text-capitalize'"; } ?> >  <?php echo ucwords($documentlist[$value['document_list_id']]); ?></a>
                                 </div>

                                 <div class="d-flex font-weight-bold">   
                                   <?php if($value['current_status'] != ST_APPROVED){ ?> 
                                     <div class="image-input image-input-empty image-input-outline mr-10 mt-2" id="kt_image_5"  style=" height:0px; ">
                                       <label class="btn-hover-text-primary mt-1 <?php //echo $value['document_shared']!=""?"mt-3":"mt-1"; ?>" data-action="change" data-toggle="tooltip" title="" data-original-title="Uploade File">
                                         <i class="ti-clip" style="font-size: 20px;"></i>
                                         <input type="file" name="image_<?php echo $value['id']; ?>" onchange="fileselected('<?php echo $value['id'] ?>',this.value)"/>
                                         <input type="hidden" name="hidimage_<?php echo $value['id']; ?>" value="<?php echo $value['document_shared']; ?>"/>
                                       </label>

                                     </div>

                                   <?php } ?>
                                   <?php if($value['document_shared']!="" AND $_SESSION['user_type']!='4'){ ?>                           
                                    <div class="dropdown-primary dropdown open" title="<?php if($value['current_status'] == ST_REJECTED){  echo "Rejected"; }elseif($value['current_status'] == ST_APPROVED){ echo "Approved"; }else{  echo "Active"; } ?>">
                                      <button class="label label-sm label-success" style="height: 18px; border-radius: 30px;"  type="button" data-content="<?php echo $value[$current_status]['name']; ?>"></button>
                                      <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                        <?php if($value['current_status'] == 0){ ?>
                                          <div class="dropdown-item <?php echo $value['current_status'] == ST_APPROVED?"active":""; ?>" onclick="statsonlychange(<?php echo $value['id'] ?>,<?php echo ST_APPROVED; ?>)"  name="status" id="status" value="<?php echo ST_APPROVED; ?>)">Approved</div>
                                          <div class="dropdown-item <?php echo $value['current_status'] == ST_REJECTED?"active":""; ?>" onclick="statsonlychange(<?php echo $value['id'] ?>,<?php echo ST_REJECTED; ?>)"  name="status" id="status" value="<?php echo ST_REJECTED; ?>)">Rejected</div>
                                        <?php } ?>
                                      </div>
                                    </div> 

                                  <?php } ?>

                                </div>
                              </div>
                              <div class="text-right">
                                <span id="attchmentfilename_<?php echo $value['id'] ?>" class="text-info"></span>
                              </div>
                            </div>
                          </div>

                        <?php } //} ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>
              <div class="card-footer">
                <div class="row">
                  <div class="col-lg-4">                   
                   <input type="hidden" name="candidate_id" value="<?php echo $candidate_id;  ?>" />
                   <input type="hidden" name="submit_doc" value="submit_doc" />  
                   <button id="kt_login_signin_submit" class="btn btn-primary mr-2"   name="submitto" value="exit"><i class="la la-check"></i>Submit</button>
                 </div>
               </div>
             </div>
           <?php    }else{    ?>

             <div class="card-px text-center py-20 mt-2">
              <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Required Documents</p>   
              <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
              </div>   
            </div> 
          <?php   }  */?>
          <form>

          </div>
          <div class="tab-pane" id="reqquestdocumentstab" role="tabpanel">














            <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
             <div class="" >


              <div class="card-footer">
                <div class="row">
                  <div class="col-lg-4">                   
                    <input type="hidden" name="candidate_id" value="<?php echo $candidate_id;  ?>" />
                    <!--  <input type="hidden" name="submit" value="submit" />   -->
                    <button id="kt_login_signin_submit" class="btn btn-primary mr-2"   name="submitrequerd_docs" value="exit"><i class="la la-check"></i>Submit</button>
                  </div>
                </div>
              </div>

            </div>

          </form>



























        </div>
      </div>
    </div>

















































































  </div>
</div>  


</div>   

</div>




</div>
</div>



















































































































<div class="col-lg-3">
 <div class="card latest-update-card">
  <div class="card-header">
    <h5>Onboarding Progress</h5>
  </div>
  <div class="card-block">
    <div class="scroll-widget">
      <div class="latest-update-box">

        <?php foreach($taskscategories as $taskscatekey =>$taskscate){ ?>
          <div class="row p-t-20 p-b-30">
            <div class="col-auto text-end update-meta p-r-0">
              <i class="b-primary update-icon ring"></i>
            </div>
            <div class="col p-l-5">
              <a href="#!"><h6><?php echo $taskscate; ?></h6></a>
              <p class="text-muted m-b-0">01 Mar 2023 18:47:34</p>
            </div>
          </div>

        <?php } ?>

      </div>
    </div>
  </div>
</div>



<div class="card">
  <div class="card-header">
    <h5>Login Activity</h5>
  </div>
  <div class="card-block">
    <div class="row">
      <div class="col-lg-2"><i class="ti-mobile"></i></div>
      <div class="col-lg-10">
        <h5 class="f-w-600 f-14 mb-0">last login on mobile browser</h5>
        <p><?php echo date("d M Y H:i:s"); ?></p>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-2"><i class="ti-apple"></i></div>
      <div class="col-lg-10">
        <h5 class="f-w-600 f-14 mb-0">last login on mobile App</h5>
        <p><?php echo date("d M Y H:i:s"); ?></p>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-2"><i class="ti-desktop"></i></div>
      <div class="col-lg-10">
        <h5 class="f-w-600 f-14 mb-0">last login on mobile desktop</h5>
        <p><?php echo date("d M Y H:i:s"); ?></p>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
