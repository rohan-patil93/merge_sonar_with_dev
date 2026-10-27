<?php
$popup = 1;
if(!isset($_GET['job_post_can_id'])){
 include_once '../includes/session.php';
 include_once '../dbconfig.php';
 include_once '../comm.php';
 $popup = 0;

}
$_SESSION['apply_js']['multiselest'] = 1;
$job_post_can_id = $_GET['job_post_can_id']!=""?$_GET['job_post_can_id']:$_POST['job_post_can_id'];
$url = API."?mode=getcandidateinterviewpopup&job_post_can_id=".$job_post_can_id;
$resurl = callAPI($url);
$resultdetails = json_decode($resurl,1);
$jobcand = $resultdetails['job_posting_candidates'];
$share_details = $resultdetails['application_share'];
$job_posting = $resultdetails['job_postings'];
$spdata  = $resultdetails['job_selection_process_steps'];
$userlist  = $resultdetails['application_users'];
$csdncjdv = filterArrayValue($userlist,"id",'322');
$schedullist  = $resultdetails['job_candidate_interview_schedules'];
$candidate_details = $resultdetails['candidate_details'];
$location = $resultdetails['locations'];
$recruitment_manager = $job_posting['recruitment_manager'] !=""?$job_posting['recruitment_manager']:$candidate_details['rm_id'];
$hiring_manager = $job_posting['requested_by'] !=""?$job_posting['requested_by']:$candidate_details['hm_id'];
if($recruitment_manager != $_SESSION['userid']){
    $event_disabled = "event_disabled";
}

$selectdata = filterArrayValue($schedullist,"job_selection_step_id",$jobcand['job_selection_process_step_id'])[0];

$allpermision = $_SESSION['role_permission']['candidates'];
if($allpermision['permission_view'] == '1'){$permission_listbtn = "disabled";
}
if($allpermision['permission_insert'] == '1'){$insert_permissionbtn = "disabled";
}
if($allpermision['permission_import'] == '1'){$permission_importbtn = "disabled";
}
if($allpermision['permission_update'] == '1'){$edit_permissionbtn = "disabled";
}
if($allpermision['permission_approval'] == '1'){$approval_permissionbtn = "disabled";
}
$dispath = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$candidate_details['id']."/".$candidate_details['image'];
$canimg = $candidate_details['image']!=""?$dispath:"no-image.jpg";












$feedbackurl = API."?mode=togetfeedbackcandidate&id=".$job_post_can_id."&process_id=".$job_posting['job_selection_process'];
$callfeedbackurl = callAPI($feedbackurl);
$feedbackresult = json_decode($callfeedbackurl,1);

$feedbackreporturl = API."?mode=getfeedbackcandidatereport&id=".$job_post_can_id."&process_id=".$job_posting['job_selection_process'];
$callfeedbackreporturl = callAPI($feedbackreporturl);
$feedbackreportresult = json_decode($callfeedbackreporturl,1);




?>
<script type="text/javascript">
 function shareprofile(){
  var job_post_can_id = $('#job_post_can_id').val();
  jQuery.ajax({
   url: "views/custom_insert_share_details.php",
   data:'share_table_id='+job_post_can_id+'&share_table=job_posting_candidates',
   type: "POST",
   success:function(data){
            $('#dynamic_popup1').html(data);
            $('#kt_modal_dynamic_popup').modal({ show: true});
          },
          error:function (){}
        });
}
function interviewsteps(val){
  $("#interview_sudule").css("display","block");
  $("#inerview_date_time").val('');
  $("#remarks").val('');
  $('#assign_id').prop('selectedIndex',0);
}
function feedback(job_selection_step_id="",job_candidate_interview_schedule_id=""){

      var job_post_can_id =  $("#job_post_can_id").val();
      if(job_selection_step_id!=""){
       var job_selection_step_id = job_selection_step_id;
     }else{
       var job_selection_step_id =  $("#job_selection_step_id").val();
     }
     $('#kt_modal_dynamic_popup').modal('hide');
     jQuery.ajax({
       url: "views/custom_candidate_feedback.php",
       data:'share_table_id='+job_post_can_id+'&share_table=job_posting_candidates&job_candidate_id='+job_post_can_id+'&job_selection_process_step_id='+job_selection_step_id+'&job_candidate_interview_schedule_id='+job_candidate_interview_schedule_id,
       type: "POST",
       success:function(data){
        console.log(data);
        $('#popupm').html(data);
        $("#kt_modal_KTDatatable_remotem").modal("show");
         },
         error:function (){}
       });
   }
   function interViewivoiteFormNameSbtcunt(){
     var validations = ajaxFormSubmitValidation('_popup');
     console.log(validations);
        if(validations == 0){
         $.ajax({
          url: "ajax.php",
          data: $('#interviewivoitesbtformname').serialize()+'&page=interviewivoitesbt',
          type: "POST",
          success:function(data){
            console.log(data);
            var datas = JSON.parse(data);
            if(datas['status'] == "fail"){
              swal("<?php echo ucwords($_SESSION['config']['name']); ?>", datas['message'], "error");
            }else{
              if(datas['LASTID'] == "0"){
                swal("<?php echo ucwords($_SESSION['config']['name']); ?>", '<?php echo $_SESSION['error_message']['3001']; ?>', "success");
               recallpopup();
             }else{
               swal("<?php echo ucwords($_SESSION['config']['name']); ?>", '<?php echo $_SESSION['error_message']['3000']; ?>', "success");
              recallpopup();
            }
          }
        }
      });
    }
   }
   function addComments(){
    var value =  $("#txtcommentjobsbt").val();
    var job_post_can_id =  $("#job_post_can_id").val();
    var job_selection_step_id =  $("#job_selection_step_id").val();
    $.post("ajax.php",
      {'page':"insertcommentjobposting",'value':value,'job_post_can_id':job_post_can_id,"job_selection_step_id":job_selection_step_id
    },
    function(datasss){
      console.log(datasss);
      var datas = JSON.parse(datasss);
      if(datas['status'] == "fail"){
        tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',datas['message'],'');
      }else{
        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
        recallpopup();
      }
    })
  }
  function addTag(){

   var selectedValueArray= [];
   $('.txttagjobsbt :selected').each(function(i,selected){

    selectedValueArray[i] = $(selected).text();
  });
var job_post_can_id =  $("#job_post_can_id").val();
var job_selection_step_id =  $("#job_selection_step_id").val();
$.post("ajax.php",{'page':"inserttagjobposting",'value':selectedValueArray,'job_post_can_id':job_post_can_id,"job_selection_step_id":job_selection_step_id},function(data){
      var datas = JSON.parse(data);
      if(datas['status'] == "fail"){
          tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',datas['message'],'');
     }else{
          tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
          recallpopup();
     }
   })
}
function recallpopup(){
  var popup = $('#popup').val();
  if(popup==0){
    var job_post_can_id = $('#job_post_can_id').val();
    jQuery.ajax({
      url: "views/custom_job_candidate_popup.php",
      data:'job_post_can_id='+job_post_can_id,
      type: "POST",
      success:function(data){
       console.log(data);
       $('#dynamic_popup1').html(data);
       $("#kt_modal_dynamic_popup").modal("show");

     },
     error:function (){}
   });
  }else{
   window.location.reload();
 }
}
function newcandidateautoregistation(candidate_id){
  $.ajax({
    url: "ajax.php",
    data:'page=newcandidateautoregistation&candidate_id='+candidate_id,
    type: "POST",
    success:function(data){
      swal("<?php echo ucwords($_SESSION['config']['name']); ?>",'<?php echo $_SESSION['error_message']['9036']; ?>', "success");
       location.reload();
      },
      error:function (){}
    });
}

function candidateShortListStatus(cand_id,status,user_role){
 $.post("ajax.php",{'page':"updatecandidateshortlist",'status':status,'cand_id':cand_id,'user_role':user_role,'candidate_id':'<?php echo $candidate_details['id'];?>'},function(data){
  console.log(data);
        var datas = JSON.parse(data);
        if(datas['status'] == "fail"){
         swal("<?php echo ucwords($_SESSION['config']['name']); ?>", datas['message'], "error");
        }else{
          if(status == '0' ){
            var mes = "Candidate Shortlisted";
          }else{
            var mes = "Candidate Rejected";
          }
          swal("<?php echo ucwords($_SESSION['config']['name']); ?>", mes, "success");
      }
      if(user_role=="RM"){
        if(status == '0' ){
          $("#hrshortyes").removeClass("btn-outline-success");
          $("#hrshortyes").addClass('btn btn-success');
        }else{
          $("#hrshortno").removeClass("btn-outline-danger");
          $("#hrshortno").addClass('btn btn-danger');
        }
     }else{
      if(status == '0' ){
        $("#hmshortyes").removeClass("btn-outline-success");
        $("#hmshortyes").addClass('btn btn-success');
      }else{
        $("#hmshortno").removeClass("btn-outline-danger");
        $("#hmshortno").addClass('btn btn-danger');
      }

    }
    location.reload();
  })
}

function TagDelete(){
 alert("dfvdfv");
}
</script>

<style>
  .texttable td, #customers th {
    border: 1px solid #ddd;
    padding: 8px;
  }

  .texttable tr:nth-child(even){background-color: #f2f2f2;}

  .texttable tr:hover {background-color: #ddd;}

  .texttable th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #04AA6D;
    color: white;
  }
</style>
<?php if($popup == '0'){ ?>
<div class="modal-header">
  <h4 class="modal-title">Candidate details</h4>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<?php } ?>
<input type="hidden" name="popup" value="<?php echo $popup; ?>" id="popup">
<div class="content  d-flex flex-column flex-column-fluid mt-2" id="kt_content">
 <div class="container">
  <div class="row">
   <div class="col-lg-9 col-12">
    <div class="card card-custom <?php echo $event_disabled; ?>">
     <div class="card-body pt-5">
      <div class="text-center mb-10">
       <div class="symbol symbol-60 symbol-circle symbol-xl-90">
        <div class="symbol-label"  >
          <img src="<?php echo $canimg; ?>" class="img-radius img-100 " style="width: 100px; height: 100px;" alt="Details">
        </div>
        <i class="symbol-badge symbol-badge-bottom bg-success"></i>
      </div>
      <h4 class="font-weight-bold my-2"><?php echo ucwords($jobcand['candidate_name']); ?></h4>
      <?php  if($jobcand['candidate_id']!="" && $jobcand['candidate_id']!="0"){ ?>
        <a href="index.php?option=COM_INSERT_CANDIDATE&Rid=<?php echo deocdeId("id:".$jobcand['candidate_id']); ?>" class="btn btn-primary mr-3 mt-5" <?php echo $edit_permissionbtn; ?>><i class="fa ti-eye"></i>
         View Profile
       </a>
     <?php }
     if($candidate_details['user_id']==""){
      ?>
      <button type="button" class="btn btn-success mr-3 mt-5" onclick="newcandidateautoregistation(<?php echo $jobcand['candidate_id']; ?>)"><i class=" fa fa-user"></i> Invite Candidate
     </button>
   <?php }  ?>
   <br/>
   <span class="mr-4 float-left" style="display: flex;">
     <i class="fa fa-group mt-2"></i>
     <div class="symbol-group symbol-hover">
       <?php $share_uses = array(); foreach ($share_details as $share){
         if(!in_array($share['share_id'],(array)$share_uses) ){
          $share_uses[] = $share['share_id']; ?>

          <div class="symbol symbol-35 symbol-circle"  data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo $_SESSION['users'][$share['share_id']]; ?>">
            <span class="symbol-label font-weight-bold"> <?php echo initials($_SESSION['users'][$share['share_id']]); ?></span>
          </div>
          <?php $k++; ?>
          <?php } ?>
          <?php } ?>
        </div>
      </span>
      <button class="btn btn-link float-right" onclick="shareprofile(<?php echo $jobcand['id']; ?>)">Share <i class="ti-sharethis"></i></button>
    </div>
  </div>
</div>
<div class="mt-5">
 <div class="card card-custom">
  <div class="card-body">
   <div class="row">
    <div class="col-3 border-right">
     <h4>Current Location</h4>
     <p><?php echo ucwords($jobcand['current_location']); ?></p>
   </div>
   <div class="col-3 border-right">
     <h4>Preferred Location</h4>
     <p><?php echo ucwords($location['location_name']); ?></p>
   </div>
   <div class="col-3 border-right">
     <h4>Experience</h4>
     <p><?php echo $jobcand['experience']; ?> Year</p>
   </div>
   <div class="col-3">
     <h4>Current Salary</h4>
     <p><?php echo customeummarmonyformate($jobcand['current_salary']); ?></p>
   </div>
 </div>
</div>
</div>
</div>
<div class="card card-custom mt-5">
 <div class="card-body">
  <ul class="nav md-tabs">
   <li class="nav-item">
    <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_1" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Resume</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Interview Timeline</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_3" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">FeedBack</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_4" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">FeedBack Rating Report</a>
  </li>
</ul>
<div class="tab-content mt-5 <?php //echo $event_disabled; ?>" id="myTabContent">
 <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel"  <?php if($jobcand['file']!=""){ ?> style="height: 1000px;" <?php
} ?> >
  <?php if($jobcand['file']!=""){ ?>
   <embed  src="uploads/all/<?php echo $jobcand['file']; ?>" style="width: 100%;height: 100%;border: none;"></embed>
   <?php
 }else{ ?>
  <div class="card-px text-center py-20 m-t-30">
    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Resume upload</p>
    <div class="text-center px-4">
      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
    </div>
  </div>
<?php } ?>
</div>
<div class="tab-pane fade " id="kt_tab_pane_2" role="tabpanel" aria-labelledby="kt_tab_pane_2">

  <div class="col-xl-12 col-md-12">
    <div class="card latest-update-card">
      <div class="card-block">
        <div class="scroll-widget mt-4">
          <?php
          $m=0; if(count((array)$schedullist) != 0){
            foreach($schedullist as $schedullistss){
             $valueg = filterArrayValue($spdata,"id",$schedullistss['job_selection_step_id']);
             $assign_to = filterArrayValue($userlist,"id",$schedullistss['assign_id']);
             ?>

             <div class="latest-update-box">
              <div class="row p-t-5 p-b-5">
                <div class="col-auto text-end update-meta p-r-0 p-l-5">

                  <?php if($assign_to[0]['user_photo']!=""){ ?>
                   <div class=" m-r-15 text-center userimageround">
                    <img src="uploads/all/<?php echo $assign_to[0]['user_photo']; ?>" alt="Details" class="img-radius img-50">
                  </div>
                <?php }else{ ?>
                 <div class="img-radius img-50 align-top m-r-15 text-center userimageround bg-c-red text-white">
                  <span class="font-size-h3 font-weight-boldest f-14 p-t-5 d-inline-block"><?php echo initials($assign_to[0]['display_name']); ?></span>
                </div>
              <?php } ?>
            </div>
            <div class="col p-l-5">
             <h5><?PHP echo $valueg[0]['select_name']; ?> </h5>
             <span class="form-label label label-info mt-1 mb-2"><?php echo $schedullistss['inerview_date_time'];?></span>
             <p class="text-muted m-b-5"><i class="ti-user m-r-5"></i> <b class="f-w-600">Assign To :</b> <?PHP echo $assign_to[0]['display_name']; ?></p>
             <p class="text-muted m-b-5"><i class="ti-file m-r-5"></i><b class="f-w-600"> Remark :</b> <?PHP echo $schedullistss['remarks']; ?></p>
           </div>
         </div>
       </div>

     <?php } ?>
     <?php } else { ?>
      <div class="card-px text-center py-20 m-t-30">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Interview Timeline</p>
        <div class="text-center px-4">
          <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
        </div>
      </div>
    <?php } ?>

  </div>
</div>
</div>
</div>

</div>
<div class="tab-pane fade" id="kt_tab_pane_3" role="tabpanel" aria-labelledby="kt_tab_pane_3">
 <div class="card-block accordion-block color-accordion-block">
  <div class="color-accordion" id="color-accordion">
   <?php $kk=0; if(count((array)$feedbackresult) !=0){  foreach($feedbackresult as $feedbackresultrow){ //print_r($feedbackresultrow); ?>
     <a class="accordion-msg bg-dark-primary b-none waves-effect waves-light"><?php echo $feedbackresultrow['procees_step_name'];?></a>
     <div class="accordion-desc dt-responsive table-responsive">
      <?php $jk=1;
      if(count((array)$feedbackresultrow['feedback_setup'][0]['quest']) !=0){
      foreach($feedbackresultrow['feedback_setup'] as $feed){
        if(count((array)$feed['quest'])!=0){
         echo  "<h3 class='mt-2'>".$feed['name']."</h3>";
         $i = 1;
         ?>
         <table class="datatable-bordered  datatable-table texttable" style="width: 100%;">
          <tr><th scope="col" class="p-3" style="width: 2%;"><b>#</b></th><th scope="col" class="p-3" style="width: 40%;"><b>Question</b></th><th scope="col" class="p-3" style="width: 10%;"><b>Rating</b></th><th scope="col" class="p-3" style="width: 60%;"><b>Comments</b></th></tr>
          <?php foreach($feed['quest'] as $queresult){  ?>
           <tr><td class="p-3 font-size-lg"><?php echo $i; ?></td><td class="p-3 font-size-lg"><?php echo  $queresult['feedback_question'];?></td><td class="p-3 text-center font-size-lg"><?php echo  is_numeric($queresult['response_option'])=='1'?$queresult['response_option']:"-";?></td><td class="p-3 font-size-lg">
            <?php echo  is_numeric($queresult['response_option'])=='1'?$queresult['comments']:$queresult['response_option'].', '.$queresult['comments']; ?>
          </td></tr>
          <?php $i++;
} ?>
        </table>
      <?php } $jk++;
}
}else{ ?>
        <div class="card-px text-center ">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No feedbacks </p>
        <div class="text-center px-4">
          <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
        </div>
      </div>
      <?php } ?>
    </div>

  <?php } ?>
  <?php } else { ?>
      <div class="card-px text-center ">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Interview Steps </p>
        <div class="text-center px-4">
          <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
        </div>
      </div>
  <?php } ?>
</div>
</div>
</div>
<div class="tab-pane fade " id="kt_tab_pane_4" role="tabpanel" aria-labelledby="kt_tab_pane_4">
  <div class="dt-responsive table-responsive">
   <?php   $listu = array();
   foreach($userlist as $luser){
    $listu[$luser['id']] = $luser['display_name'];
  }
  $userlistt = array();
  foreach($feedbackreportresult as $feedbackreportresultss){

   foreach($feedbackreportresultss['quest'] as $unamess){
    if(!in_array($unamess['created_by'], (array)$userlistt)){   array_push($userlistt, $unamess['created_by']);
  }
}
}
?>
<?php  $i=1;
foreach($feedbackreportresult as $feedbackreportresult){  ?>
 <?php echo "<h3 class='text-info mt-2'>".ucwords($feedbackreportresult['feedback_setup_name'])."</h3>";?>

 <table class="datatable-bordered  datatable-table texttable" style="width: 100%;">
  <thead>
   <tr>
    <th scope="col" class="p-3" style="width: 2%;"><b>#</b></th>
    <th scope="col" class="p-3 " style="width: 15%;"><b>Questios</b></th>
    <th scope="col" class="p-3" style="width: 3%;"><b>Rating</b></th>
    <th scope="col" class="p-3" style="width: 5%;"><b>Required Rating</b></th>
    <?php foreach($userlistt as $userlistss){ ?>
     <th scope="col" class="p-3" style="width: 5%;"><b> <?php echo $listu[$userlistss];?></b></th>
   <?php  } ?>
 </tr>
 <thead>
   <tbody>
    <?php  $j=1; $total_cc_rr=0; $user_total=array();
    foreach($feedbackreportresult['quest'] as $que){ ?>
                     <tr>
                       <td class="p-3 font-size-lg"><?php echo $j; ?></td>
                       <td class="p-3  font-size-lg"><?php echo $que['feedback_question']; ?></td>
                       <td class="p-3 font-size-lg">
                        <?php
                        $requrd = json_decode($que['feedback_question_option'],1);
                        $cc_rr = count((array)$requrd)=='1'?5:count((array)$requrd);
                        echo  "1 - ".$cc_rr;
                        $total_cc_rr = $total_cc_rr+$cc_rr;
                        ?>
                      </td>
                      <td class="p-3  font-size-lg text-right"><?php echo round($que['require_ratting'],2); ?></td>
                      <?php foreach($userlistt as $userlistssd){ if($userlistssd == $que['created_by']){
                        if($que['require_ratting']>$que['response_option']){
                         $ccol = "text-danger";
                       }else{
                         $ccol = "text-success";
                       } ?>
                      <td style="width: 3%;" class="p-3 <?php echo $ccol; ?> text-right"> <?php
                        echo $que['response_option']; ?>
                      </td>
                   <?php }else{ echo "<td>-</td>"; ?>
                   <?php } ?>
                   <?php } ?>
                 </tr>
                 <?php $j++;
                 } /**/ ?>
                 <tr>
                  <td class="p-3" style="width: 2%;"><b></b></td>
                  <td class="p-3 " style="width: 15%;"><b>OverAll</b></td>
                  <td class="p-3" style="width: 3%;"><b>-</b></td>
                  <td class="p-3 text-right" style="width: 5%;"><b>-</b></td>
                  <?php foreach($userlistt as $userlistss){ ?>
                   <td class="p-3 text-right" style="width: 5%;">
                    <b>
                          $show = 0;
                          if($total_cc_rr != 0 AND $total_cc_rr != ''){
                            $show = (($user_total[$que['created_by']]/$total_cc_rr)*100)/20;
                          }
                          $total_show[$que['created_by']] = $total_show[$que['created_by']]+$show;
                          echo number_format($show,1); ?>

                        </b>
                      </td>
                    <?php  } ?>
                  </tr>
                </tbody>
              </table>
              <?php  $i++;
              } ?>

              <table style="width: 100%;">
               <tr>
                <th scope="col" class="p-3" style="width: 2%;"><b></b></th>
                <th scope="col" class="p-3 " style="width: 15%;"><b>Total</b></th>
                <th scope="col" class="p-3" style="width: 3%;"><b>-</b></th>
                <th scope="col" class="p-3 " style="width: 5%;"><b>-</b></th>
                <?php $chk_total = 0;  foreach($userlistt as $userlistss){ ?>
                 <th scope="col" class="text-right" style="width: 5%;">
                  <b>
                    <?php
                    $mval = $i=$i-1;
                    if($mval != 0 && $mval != ''){
                      if(isset($userlistss['created_by'])){
                        $chk_total= ($total_show[$userlistss['created_by']])/($mval);
                      }
                    }
                    echo number_format($chk_total,1);
                    ?>

                  </b>
                </th>
              <?php  } ?>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
              <div class="col-lg-3 col-12">
                <div class="card card-custom">
                 <div class="card-body">

                  <form method="post" id="interviewivoitesbtformname">
                   <input type="hidden" name="job_posing_candidate_id" id="job_post_can_id" value="<?php echo $job_post_can_id; ?>">
                   <input type="hidden" name="candidate_user_id" id="candidate_user_id" value="<?php echo $candidate_details['user_id']; ?>">

                   <input type="hidden" name="candidate_name" id="candidate_name" value="<?php echo $jobcand['candidate_name']; ?>">
                   <input type="hidden" name="candidate_email" id="candidate_email" value="<?php echo $jobcand['candidate_email']; ?>">
                   <input type="hidden" name="candidate_phone" id="candidate_phone" value="<?php echo $jobcand['candidate_phone']; ?>">
                   <input type="hidden" name="created_by" id="created_by" value="<?php echo $_SESSION['userid']; ?>">
                   <div class="<?php echo $recruitment_manager == $_SESSION['userid']?"":"event_disabled"; ?>" >
                  <div class="mt-2">
                    RM
                  </div>
                  <button type="button"  <?php if(is_null($jobcand['shortlisted'])){ ?> onclick="candidateShortListStatus('<?php echo $job_post_can_id; ?>','0','RM')"  <?php
}else{ echo "disabled";
} ?> id="hrshortyes" class=" btn btn-
<?php if(is_null($jobcand['shortlisted']) || $jobcand['shortlisted']=='1'){ echo "outline-";
} ?>success">
                    Shortlist
                  </button>
                  <button type="button"   <?php if(is_null($jobcand['shortlisted'])){ ?> onclick="candidateShortListStatus('<?php echo $job_post_can_id; ?>','1','RM')"  <?php
}else{ echo "disabled";
} ?> id="hrshortno" class=" btn btn-
<?php if(is_null($jobcand['shortlisted']) || $jobcand['shortlisted']=='0'){ echo "outline-";
} ?>danger">Reject
                  </button>
                </div>
                  <div class="<?php echo $hiring_manager == $_SESSION['userid']?"":"event_disabled"; ?>" >
                  <div class="mt-2">
                    HM
                  </div>

                  <button type="button"  <?php if(is_null($jobcand['hm_shortlisted'])){ ?> onclick="candidateShortListStatus('<?php echo $job_post_can_id; ?>','0','HM')"  <?php
}else{ echo "disabled";
} ?>  id="hmshortyes" class="btn btn-
<?php if(is_null($jobcand['hm_shortlisted']) || $jobcand['hm_shortlisted']==1){ echo "outline-";
} ?>success mb-3">
                    Shortlist
                  </button>

                  <button type="button"   <?php if(is_null($jobcand['hm_shortlisted'])){ ?> onclick="candidateShortListStatus('<?php echo $job_post_can_id; ?>','1','HM')"  <?php
}else{ echo "disabled";
} ?>  id="hmshortno" class="btn btn-
<?php if(is_null($jobcand['hm_shortlisted']) || $jobcand['hm_shortlisted']==0){ echo "outline-";
} ?>danger mb-3">                 Reject
                  </button>
                </div>
                <div class="mt-3 <?php echo $event_disabled; ?>">
                  <select aria-label="Job Selection Step" class="form-control mt-5 form-select js-example-basic-single" id="job_selection_step_id" name="job_selection_step_id" onchange="interviewsteps(this.value)">
                    <option value="0">Send interview invite</option>
                    <?php foreach ($spdata as  $sp) { ?>
                     <option  value="<?php echo $sp['id']; ?>" <?php  echo $selectdata['job_selection_step_id']==$sp['id']?"selected":""; ?>><?php echo $sp['select_name']; ?></option>
                   <?php } ?>
                 </select>
                </div>
                 <div id="interview_sudule" class="<?php echo $event_disabled; ?>">
                  <div class="mt-2">
                   Assign Interviewer
                 </div>
                 <span class="d-block error-message text-danger"></span>
                 <div class="mt-2">
                   <select aria-label="Assign" class="form-control mt-4 form-select js-example-basic-single requried_field_popup" id="assign_id" name="assign_id">
                    <option value="">Send interview invite</option>
                    <?php foreach ($userlist as  $ul) { ?>
                     <option  value="<?php echo $ul['id']; ?>"   <?php echo $selectdata['assign_id']==$ul['id']?"selected":""; ?> ><?php echo $ul['display_name']; ?></option>
                   <?php } ?>
                 </select>
               </div>
               <div class="mt-2">
                 Interview Date & Time
               </div>
               <span class="d-block error-message text-danger"></span>
               <div class="date mt-2" id=""  data-target-input="nearest">
                 <input aria-label="Input field" type="datetime-local" class="form-control requried_field_popup" value="<?php echo $selectdata['inerview_date_time']; ?>" name="inerview_date_time" id="" placeholder="Select date time" data-target="">

               </div>
               <div class="mt-2">Remark</div>
               <span class="d-block error-message text-danger"></span>
               <div class="checkbox-inline mt-2">
                <textarea aria-label="Remarks" class="form-control requried_field_popup"  name="remarks" id="remarks"  style="height:60px"><?php echo $selectdata['remarks']; ?></textarea>
              </div>
              <div class="mt-2 mb-2">Send Information</div>
              <div class="col-lg-12" >
                <div class="checkbox-fade fade-in-primary">
                  <label class="form-label">
                    <input aria-label="Interviewer Check" type="checkbox" name="interviewer_check" value="interviewer">
                    <span class="cr">
                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                    </span>
                    <span>Interviewer</span>
                  </label>
                </div>
                <div class="checkbox-fade fade-in-primary <?php echo $candidate_details['user_id']!=""?"FGF":"event_disabled";?>">
                  <label class="form-label">
                   <input aria-label="Candidate Check" type="checkbox" name="candidate_check" value="candidate" >
                   <span class="cr">
                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                  </span>
                  <span>Candidate</span>
                </label>
              </div>
            </div>




            <div class="mt-2">
             <button type="button" onclick="interViewivoiteFormNameSbtcunt()" name="interviewivoitesbt" class="btn btn-primary">Submit</button>
             <?php  ?>
            </div>

          </div>
        </form>

        <hr>


        <div class="col-xl-12 col-md-12">
          <div class="card latest-update-card">

            <div class="card-block">
              <div class="scroll-widget mt-4">
                <div class="latest-update-box">

                 <?php $color = array("primary","warning","danger"); $i=0;
                 foreach($schedullist as $schedullist){
                    $value = filterArrayValue($spdata,"id",$schedullist['job_selection_step_id']);
                    $feedback_disabled = "";
                    if($schedullist['assign_id'] == $_SESSION['userid']){
}else{
                        $feedback_disabled = "event_disabled";
                    }

                  ?>
                  <div class="row ">
                    <div class="col-auto text-end update-meta p-r-0">
                      <a class="img-radius img-40 align-top m-r-15 update-icon">
                        <i class="ti-folder text-primary"></i>
                      </a>
                    </div>
                    <div class="col p-l-5 mt-2">
                        <a class="text-<?php echo $color[$i]; ?>"><h6><?php echo date('d-m-Y H:i A',strtotime($schedullist['inerview_date_time']));?></h6></a>
                        <span class="d-block f-12"><?PHP echo $value[0]['select_name']; ?></span>
                        <span class="d-block f-10"><i class="fa fa-user"></i> <?php echo ucwords($_SESSION['users'][$schedullist['assign_id']]);?>
                        <button type="button" class="btn-primary btn <?php echo $feedback_disabled; ?> mt-2" onclick="feedback(<?php echo $schedullist['job_selection_step_id']; ?>,'<?php echo $schedullist['id']; ?>')">Feed Back</button>
                      </div>
                    </div>

                  <?php } ?>



                </div>
              </div>
            </div>
          </div>
        </div>


        <hr>

       <a href="uploads/all/<?php echo $jobcand['file']; ?>" target="_blank" class="btn btn-text-dark-50 btn-icon-primary font-weight-bold btn-hover-bg-light mr-3">
         <i class="fas fa-download"></i> Download Resume
       </a>
       <hr>
       <h4>Contact Information</h4>
       <div class="mt-2">
         <h6>Phone
          <i class="ti-mobile float-right"></i></h6>
          <span>+91 <?php echo $jobcand['candidate_phone']; ?></span>

        </div>
        <div class="mt-2">
         <h6>Email <i class="ti-email float-right"></i></h6>
         <span> <?php echo $jobcand['candidate_email']; ?></span>

       </div>
       <hr>
       <div class="mt-2 <?php echo $event_disabled; ?>">
        <h4>Comment</h4>
        <textarea aria-label="Commentjobsbt" class="form-control" id="txtcommentjobsbt" style="height:60px"></textarea>
        <button type="button" onclick="addComments()" class="btn btn-outline-success mt-2 mb-2">
         Add
       </button>
     </div>
     <?php

     $commentswhile["where"] = array('type '=>'comment','job_posting_candedate_id'=>$job_post_can_id);
     $commentswhile["field"] = "id,comments,value,created_by";
     $commentdata = urlencode(json_encode($commentswhile));
     $commentturl = API."?mode=gettabledetailswithconditions&table=candidate_history&data=".$commentdata;
     $commenttres = callAPI($commentturl);
     $commenttres = json_decode($commenttres,1);
     foreach($commenttres as $commenttres){
      $slectdat = filterArrayValue($userlist,"id",$commenttres['created_by'])[0];

      ?>
      <span> <?php echo $slectdat['display_name']; ?> : <span class="text-primary"><?php echo $commenttres['comments']; ?><span></span></br>
      <?php } ?>
      <hr>




      <?php

      $tagswhile["where"] = array('application_table_name '=>'job_posting_candidates','transaction_id'=>$job_post_can_id);
      $tagswhile["field"] = "id,tages";
      $tagdata = urlencode(json_encode($tagswhile));
      $tagturl = API."?mode=gettabledetailswithconditions&table=application_tags&data=".$tagdata;
      $tagtres = callAPI($tagturl);
      $tagtres = json_decode($tagtres,1);


      ?>
      <div class="mt-4 <?php echo $event_disabled; ?>">
       <h4>Tag</h4>
       <select aria-label="Job Tag[]" class="form-control form-select js-example-tags txttagjobsbt" id="kt_select2_11" multiple="multiple"  name="job_tag[]">
         <?php  foreach($tagtres as $tagtres){ ?>

          <option  selected><?php echo $tagtres['tages']; ?></option>
        <?php } ?>
      </select>
      <button onclick="addTag()" type="button" class="btn btn-outline-success mt-2">
       Submit Tag
     </button>
   </div>

 </div>
</div>
</div>
</div>
</div>
</div>
<?php if(!isset($_GET['job_post_can_id'])){ ?>
  <script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
  <script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
  <script type="text/javascript">
   $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar'></i>",
    dateFormat: date_format,
    changeMonth: true,
    changeYear: true
  });
</script>
<?php } ?>
