<?php
$rids =  encodeId($_GET['Rid']);
$rid = urldecode($rids);
$cand = explode(':', $rid);
$update_row = $cand['1'];
if($update_row!=''){
   $data12["where"] = array('id'=>$update_row);
   $datw12 = urlencode(json_encode($data12));
   echo $curl12 = API."?mode=gettabledetailswithconditions&table=".TBL_PERFORMANCE_RESPONSES."&data=".$datw12;
   $cudata12 = callAPI($curl12);
   $emp_performance_response = json_decode($cudata12,1);
   $emp_performance_response_data = $emp_performance_response[0];
}
$emp_id = $emp_performance_response_data['employee_id'];
$assessment_id = 4;
$mgr_rating = $emp_performance_response_data['manager_rating'];

$url = API."?mode=getperformanceemployees&company_id=".$_SESSION['company_id']."&emp_id=".$emp_id;
$response = callAPI($url);
$employees_details = json_decode($response,1);
$appln_users = $employees_details['appln_users'];
$employees = $employees_details['employees'];
$reports_to = $emp_performance_response_data['reports_to'];
$userid = $_SESSION['userid'];
if($reports_to==""){
   $reports_to = $appln_users[$userid];
}
$review_manager = $emp_performance_response_data['reviewer'];

 echo $url = API."?mode=getempperformancepeerfeedback&company_id=".$_SESSION['company_id']."&emp_id=".$emp_id."&reports_to=".$reports_to."&reviewer=".$review_manager."&assessment_id=".$assessment_id;
$response = callAPI($url);
$peerfeedbacktable = json_decode($response,1);
$peerfeedback = $peerfeedbacktable['peerfeedback'];
$peerfeedback_count = count((array)$peerfeedbacktable['peerfeedback']);
$designations = $peerfeedbacktable['designations'];
$assessment = $peerfeedbacktable['assessment'];
$peer_questions = $peerfeedbacktable['peer_questions'];

foreach($peer_questions as $ass) {
   $peer_qid[] = $ass['id'];
}
$peer_questions = json_encode($peer_qid,1);

$assessment_questions = $peerfeedbacktable['assessment_questions'];
$assessment_questions_selected = $peerfeedbacktable['assessment_questions_selected'];
$selected_assessment_id = $peerfeedbacktable['selected_assessment_id'];
$emp_questions = $peerfeedbacktable['emp_questions'];
$mgr_questions = $peerfeedbacktable['mgr_questions'];
$reviewmgr_questions = $peerfeedbacktable['reviewmgr_questions'];
$qustion_types = $peerfeedbacktable['qustion_types'];
$typeofquestion= array('1'=>'MCQ','2'=>'FT');

$employee_responses = $peerfeedbacktable['employee_responses'];
$manager_responses = $peerfeedbacktable['manager_responses'];
$reviewer_responses = $peerfeedbacktable['reviewer_responses'];

if(isset($_POST['sendtopeers'])){
   $sendto_peerslist = $_POST['sendto_peerslist'];
   $assessment_id = $_POST['assessment_id'];

   $peer_questions = urldecode($_POST['peer_questions']);

   foreach($sendto_peerslist as $peer_id){
     $peer[] = array('company_id'=>$_SESSION['company_id'],'emp_id'=>$_SESSION['emp_id'],'assessment_id'=>$_POST['assessment_id'],'peer_feedback_empid'=>$peer_id,'questions'=>$peer_questions,'answers'=>"",'is_active'=>'0','updated_date'=>date('Y-m-d'),'updated_by'=>$_SESSION['userid']);
  }
  $url = API."?mode=savepeerfeedbackquestions&table=performance_responses&method=post";
  $retunn_inserted_qid = file_post_contents($url,$peer,'insert');

}

if(isset($_POST['selfsubmit'])){
   $insert_main = array('rowid'=>$_POST['response_row'],'reviewer'=>$_POST['review_manager'],'reports_to'=>$reports_to,'employee_status'=>'1','updated_date'=>date('Y-m-d'),'updated_by'=>$_SESSION['userid']);

   foreach($_POST['value_id'] as $q_no) {
      $response_option= explode('||',$_POST['response_option'.$q_no]);
      $response_option = json_encode($response_option,1);
      $question_id=$q_no;
      $response_detail_row = $_POST['response_rowid'.$q_no];
      $insert_sub = array('rowid'=>$response_detail_row,'reviewer'=>$_POST['review_manager'],'reports_to'=>$reports_to,'question_id'=>$question_id,'response_option'=>$response_option,'is_active'=>'0','current_status'=>'0');
       $insert_sub['company_id'] = $_SESSION['company_id'];
   $insert_sub['employee_id'] = $_SESSION['userid'];
   $insert_sub['assessment_id'] = $_POST['assessment_id'];
   $insert_sub['created_by'] = $_SESSION['userid'];
   $insert_sub['created_date'] = date('Y-m-d');
   $insert_sub['updated_by'] = $_SESSION['userid'];
   $insert_sub['updated_date'] = date('Y-m-d');
      $insert_resp_detail['performance_response_details'][] = $insert_sub;

   }
   callInsertApi($insert_main,$insert_resp_detail);

}

if(isset($_POST['selfsave'])){
  $insert_main = array('rowid'=>$_POST['response_row'],'reviewer'=>$_POST['review_manager'],'reports_to'=>$reports_to,'employee_status'=>'0','updated_date'=>date('Y-m-d'),'updated_by'=>$_SESSION['userid']);

  foreach($_POST['value_id'] as $q_no) {
   $response_option= explode('||',$_POST['response_option'.$q_no]);
   $response_option = json_encode($response_option,1);
   $question_id=$q_no;
   $response_detail_row = $_POST['response_rowid'.$q_no];

   $insert_sub = array('rowid'=>$response_detail_row,'reviewer'=>$_POST['review_manager'],'reports_to'=>$reports_to,'question_id'=>$question_id,'response_option'=>$response_option,'is_active'=>'0','current_status'=>'0');
    $insert_sub['company_id'] = $_SESSION['company_id'];
   $insert_sub['employee_id'] = $_SESSION['userid'];
   $insert_sub['assessment_id'] = $_POST['assessment_id'];
   $insert_sub['created_by'] = $_SESSION['userid'];
   $insert_sub['created_date'] = date('Y-m-d');
   $insert_sub['updated_by'] = $_SESSION['userid'];
   $insert_sub['updated_date'] = date('Y-m-d');
   $insert_resp_detail['performance_response_details'][] = $insert_sub;
}
callInsertApi($insert_main,$insert_resp_detail);

}

if(isset($_POST['managersubmit'])){
    $manager_rating = $_POST['manager_rating'];
   $insert_main = array('rowid'=>$_POST['response_row'],'manager_status'=>'1','manager_rating'=>$manager_rating,'updated_date'=>date('Y-m-d'),'updated_by'=>$_SESSION['userid']);

   foreach($_POST['value_id'] as $q_no) {
      $response_option= explode('||',$_POST['response_option'.$q_no]);
      $response_option = json_encode($response_option,1);
      $question_id=$q_no;
      $response_detail_row = $_POST['response_rowid'.$q_no];

      $insert_sub = array('rowid'=>$response_detail_row,'reports_to'=>$reports_to,'reviewer'=>$_POST['review_manager'],'question_id'=>$question_id,'response_option'=>$response_option,'is_active'=>'0','current_status'=>'0');
       $insert_sub['company_id'] = $_SESSION['company_id'];
   $insert_sub['employee_id'] = $_SESSION['userid'];
   $insert_sub['assessment_id'] = $_POST['assessment_id'];
   $insert_sub['created_by'] = $_SESSION['userid'];
   $insert_sub['created_date'] = date('Y-m-d');
   $insert_sub['updated_by'] = $_SESSION['userid'];
   $insert_sub['updated_date'] = date('Y-m-d');
      $insert_resp_detail['performance_response_details'][] = $insert_sub;
   }

   callInsertApi($insert_main,$insert_resp_detail);

}

if(isset($_POST['managersave'])){
    $manager_rating = $_POST['manager_rating'];
   $insert_main = array('rowid'=>$_POST['response_row'],'manager_rating'=>$manager_rating,'manager_status'=>'0','updated_date'=>date('Y-m-d'),'updated_by'=>$_SESSION['userid']);

   foreach($_POST['value_id'] as $q_no) {
      $response_option= explode('||',$_POST['response_option'.$q_no]);
      $response_option = json_encode($response_option,1);
      $question_id=$q_no;
      $response_detail_row = $_POST['response_rowid'.$q_no];

      $insert_sub = array('rowid'=>$response_detail_row,'reviewer'=>$_POST['review_manager'],'reports_to'=>$reports_to,'question_id'=>$question_id,'response_option'=>$response_option,'is_active'=>'0','current_status'=>'0');
       $insert_sub['company_id'] = $_SESSION['company_id'];
   $insert_sub['employee_id'] = $_SESSION['userid'];
   $insert_sub['assessment_id'] = $_POST['assessment_id'];
   $insert_sub['created_by'] = $_SESSION['userid'];
   $insert_sub['created_date'] = date('Y-m-d');
   $insert_sub['updated_by'] = $_SESSION['userid'];
   $insert_sub['updated_date'] = date('Y-m-d');
      $insert_resp_detail['performance_response_details'][] = $insert_sub;
   }

   callInsertApi($insert_main,$insert_resp_detail);
}

if(isset($_POST['reviewersave'])){
   $insert_main = array('rowid'=>$_POST['response_row'],'reviewer_status'=>'0','updated_date'=>date('Y-m-d'),'updated_by'=>$_SESSION['userid']);

   foreach($_POST['value_id'] as $q_no) {
      $response_option= explode('||',$_POST['response_option'.$q_no]);
      $response_option = json_encode($response_option,1);
      $question_id=$q_no;
      $response_detail_row = $_POST['response_rowid'.$q_no];

      $insert_sub = array('rowid'=>$response_detail_row,'reviewer'=>$_POST['review_manager'],'reports_to'=>$reports_to,'question_id'=>$question_id,'response_option'=>$response_option,'is_active'=>'0','current_status'=>'0');
       $insert_sub['company_id'] = $_SESSION['company_id'];
   $insert_sub['employee_id'] = $_SESSION['userid'];
   $insert_sub['assessment_id'] = $_POST['assessment_id'];
   $insert_sub['created_by'] = $_SESSION['userid'];
   $insert_sub['created_date'] = date('Y-m-d');
   $insert_sub['updated_by'] = $_SESSION['userid'];
   $insert_sub['updated_date'] = date('Y-m-d');
      $insert_resp_detail['performance_response_details'][] = $insert_sub;
   }

   callInsertApi($insert_main,$insert_resp_detail);
}

if(isset($_POST['reviewersubmit'])){
   $insert_main = array('rowid'=>$_POST['response_row'],'reviewer_status'=>'1','updated_date'=>date('Y-m-d'),'updated_by'=>$_SESSION['userid']);

   foreach($_POST['value_id'] as $q_no) {
      $response_option= explode('||',$_POST['response_option'.$q_no]);
      $response_option = json_encode($response_option,1);
      $question_id=$q_no;
      $response_detail_row = $_POST['response_rowid'.$q_no];

      $insert_sub = array('rowid'=>$response_detail_row,'reports_to'=>$reports_to,'reviewer'=>$_POST['review_manager'],'question_id'=>$question_id,'response_option'=>$response_option,'is_active'=>'0','current_status'=>'0');
       $insert_sub['company_id'] = $_SESSION['company_id'];
   $insert_sub['employee_id'] = $_SESSION['userid'];
   $insert_sub['assessment_id'] = $_POST['assessment_id'];
   $insert_sub['created_by'] = $_SESSION['userid'];
   $insert_sub['created_date'] = date('Y-m-d');
   $insert_sub['updated_by'] = $_SESSION['userid'];
   $insert_sub['updated_date'] = date('Y-m-d');
      $insert_resp_detail['performance_response_details'][] = $insert_sub;
   }
   callInsertApi($insert_main,$insert_resp_detail);

}

function callInsertApi($insert_main,$insert_sub){


   $postdata = array("response_array"=>$insert_main,"response_detail_array"=>$insert_sub);
   $url = API."?mode=saveperformanceresponse&table=performance_responses&method=post";
   $retunn_inserted_qid = file_post_contents($url,$postdata,'insert');
 header("location:index.php?option=COM_LIST_PERFORMANCE_RESPONSES");

}

?>

<!-- custom html start here -->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
   <div class="card-body">
      <div class="alert alert-white" id="" style="">
         <ul class="nav nav-tabs nav-tabs-line mb-5">
            <li class="nav-item">
               <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_1" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                  <span class="nav-icon"><i class="fas fa-bullseye"></i></span>
                  <span class="nav-text">Goals</span>
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                  <span class="nav-icon"><i class="fas fa-users"></i></span>
                  <span class="nav-text">Peer Feedback</span>
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_3" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                  <span class="nav-icon"><i class="fas fa-award"></i></span>
                  <span class="nav-text">Assessment</span>
               </a>
            </li>

         </ul>
         <div class="tab-content mt-5" id="myTabContent">
            <div class="tab-pane fade" id="kt_tab_pane_1" role="tabpanel" aria-labelledby="kt_tab_pane_2">
               <div class="row">
                  <div class="col-9"></div>
                  <div class="col-3 float-right">
                     <div class="form-group row">
                        <div class="col-4 mt-2">
                           <label for="exampleSelect1">Status
                              <span class="text-danger">*</span></label>
                           </div>
                           <div class="col-8">
                              <select class="form-control" id="exampleSelect1">
                                 <option>In Progress</option>
                                 <option>In Progress</option>
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row mb-5 border-bottom pb-5">
                     <div class="col-md-1">
                        <div class="symbol symbol-40  mr-5">
                           <span class="symbol-label">
                              <span class="svg-icon svg-icon-xl svg-icon-primary">

                                 <i class="fas fa-bullseye"></i>
                                 <!--end::Svg Icon-->
                              </span>
                           </span>
                        </div>
                     </div>
                     <div class="col-md-9">
                        <a href="#" class="text-dark text-hover-primary mb-1 font-size-lg">
                           <h4>Strategic Goal: Technology Gaps</h4>
                        </a>
                        <h6 class="text-muted">Due August 04, 2021</h6>
                        <h6 class="mt-4">Analyze Technology gaps in the company.</h6>
                        <div class="progress mt-4">
                          <div class="progress-bar" role="progressbar" style="width: 10%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">10%</div>
                       </div>
                       <div class="custom-comment mt-5">
                        <a href="#"><i class="fas fa-plus-circle"></i> Add Comment</a>
                        <div class="form-group mt-4 mb-1 open-comment" style="">
                           <label for="exampleTextarea">Add Comment
                              <span class="text-danger">*</span></label>
                              <textarea class="form-control" id="exampleTextarea" rows="3"></textarea>
                              <button class="btn btn-primary mt-3">Submit</button>
                           </div>

                        </div>
                     </div>
                  </div>
                  <div class="row mb-5 border-bottom pb-5">
                     <div class="col-md-1">
                        <div class="symbol symbol-40  mr-5">
                           <span class="symbol-label">
                              <span class="svg-icon svg-icon-xl svg-icon-primary">

                                 <i class="fas fa-bullseye"></i>
                                 <!--end::Svg Icon-->
                              </span>
                           </span>
                        </div>
                     </div>
                     <div class="col-md-9">
                        <a href="#" class="text-dark text-hover-primary mb-1 font-size-lg">
                           <h4>Strategic Goal: Technology Gaps</h4>
                        </a>
                        <h6 class="text-muted">Due August 04, 2021</h6>
                        <h6 class="mt-4">Analyze Technology gaps in the company.</h6>
                        <div class="progress mt-4">
                          <div class="progress-bar" role="progressbar" style="width: 10%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">10%</div>
                       </div>
                       <div class="custom-comment mt-5">
                        <a href="#"><i class="fas fa-plus-circle"></i> Add Comment</a>
                        <div class="form-group mt-4 mb-1 open-comment" style="display: none;">
                           <label for="exampleTextarea_2">Add Comment
                              <span class="text-danger">*</span></label>
                              <textarea class="form-control" id="exampleTextarea_2" rows="3"></textarea>
                              <button class="btn btn-primary mt-3">Submit</button>
                           </div>

                        </div>
                     </div>
                  </div>
               </div>
               <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel" aria-labelledby="kt_tab_pane_2">


                              <?php if($peerfeedback_count > 0) {
                                foreach($peerfeedback as $peervalue){
                                 $peer_emp_id = $peervalue['peer_feedback_empid'];
                                 $updated_date = date('D, M d, Y', strtotime($peervalue['updated_date']));

                                 $start_date = date('D, M d, Y', strtotime($peervalue['created_date']));
                                 $end_date = date('D, M d, Y', strtotime($assessment[$peervalue['assessment_id']]['assessment_end_date']));

                                 $start = date('Y-m-d', strtotime($assessment[$peervalue['assessment_id']]['assessment_end_date']));
                                 $end = date('Y-m-d', strtotime($peervalue['created_date']))  ;
                                 $datediff = strtotime($start) - strtotime($end);

                                 if($peervalue['answers']==""){ ?>
                                    <div class="card-body pt-2 bg-light pl-2 pr-2 mt-5">
                                       <div class="row mt-5 mb-5">
                                          <div class="col-6">
                                             <h5>Waiting for Feedback from...</h5>
                                          </div>
                                          <div class="col-6">
                                             <a class="float-right mr-5" href=""><i class="fas fa-times"></i> Cancel</a>
                                          </div>
                                       </div>
                                       <div class="d-flex align-items-center mb-10">
                                          <!--begin::Symbol-->
                                          <div class="symbol symbol-75 symbol-light mr-5">
                                             <div class="symbol-group symbol-hover">
                                                <?php if($employees[$peer_emp_id]['image']!=""){ ?>
                                                   <div class="symbol symbol-45 symbol-circle" data-toggle="tooltip">
                                                      <img src="<?php echo "uploads/all/".$employees[$peer_emp_id]['image']; ?>" alt="Details">
                                                   </div>
                                                <?php }else{ ?>
                                                   <div class="symbol symbol-45 symbol-circle symbol-primary" data-toggle="tooltip" >
                                                      <span class="symbol-label font-size-h3 font-weight-boldest"><?php echo initials($employees[$peer_emp_id]['display_name']); ?></span>
                                                   </div>
                                                <?php } ?>
                                             </div>
                                          </div>
                                          <!--end::Symbol-->
                                          <!--begin::Text-->
                                          <div class="d-flex flex-column font-weight-bold">
                                             <a href="#" class="text-dark text-hover-primary mb-1 font-size-lg"><?php echo $employees[$peer_emp_id]['display_name']; ?></a>
                                             <span class="text-muted"><?php echo $designations[$employees[$peer_emp_id]['designation_id']]; ?></span>
                                          </div>

                                          <!--end::Text-->
                                       </div>
                                       <p>An email requesting  <?php echo $employees[$peer_emp_id]['display_name']; ?> complete peer feedback was sent <?php echo $start_date; ?> <br> <?php echo $employees[$peer_emp_id]['display_name']; ?> has until <?php echo $end_date;?> (<?php echo round($datediff / (60 * 60 * 24)); ?> days) to complete this.</p>
                                    </div>
                                 <?php } else {  ?>
                                    <div class="row mt-10 custom-width">
                                       <div class="col-1">
                                        <!--begin::Symbol-->
                                        <div class="symbol symbol-75 symbol-light mr-5">
                                          <div class="symbol-group symbol-hover">
                                             <?php if($employees[$peer_emp_id]['image']!=""){ ?>
                                                <div class="symbol symbol-45 symbol-circle" data-toggle="tooltip">
                                                   <img src="<?php echo "uploads/all/".$employees[$peer_emp_id]['image']; ?>" alt="Details">
                                                </div>
                                             <?php }else{ ?>
                                                <div class="symbol symbol-45 symbol-circle symbol-primary" data-toggle="tooltip" >
                                                   <span class="symbol-label font-size-h3 font-weight-boldest"><?php echo initials($employees[$peer_emp_id]['display_name']); ?></span>
                                                </div>
                                             <?php } ?>
                                          </div>
                                       </div>
                                       <!--end::Symbol-->
                                    </div>
                                    <div class="col-10">
                                       <h5><?php echo $employees[$peer_emp_id]['display_name']; ?> </h5>
                                       <h6 class="text-muted"><?php echo $designations[$employees[$peer_emp_id]['designation_id']]; ?></h6>
                                       <span><?php echo $updated_date; ?> </span>
                                       <?php
                                       $performance_qids = json_decode($peervalue['questions'],1);
                                       $performance_answers = json_decode($peervalue['answers'],1);

                                       foreach($performance_qids as $qid) {

                                          ?>
                                          <h5 class="mt-5"><?php echo $assessment_questions[$qid]['assessment_question']; ?> </h5>
                                          <p class="mb-1"><?php echo $performance_answers[$qid]; ?> </p>
                                       <?php } ?>
                                    </div>
                                 </div>
                              <?php }
}
} else { ?>
                                 <form method="post">
                                    <input type="hidden" name="assessment_id" value="<?php echo $assessment_id; ?>">
                                    <input type="hidden" name="peer_questions" value="<?php echo urlencode($peer_questions); ?>">

                                    <div class="form-group col-lg-6">
                                       <label for="example-date-input-to" class="col-form-label font-weight-bolder">Select Peers to get feedback </label>
                                       <select aria-label="Sendto Peerslist[]" class="form-control" id="kt_select2_43" name="sendto_peerslist[]" multiple="multiple">
                                          <?php foreach ($employees as $value) { $key=$value['id']; ?>
                                          <option value="<?php echo $key;?>"><?php echo $value['display_name']; ?></option>
                                       <?php } ?>
                                    </select>
                                    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 mt-10" name="sendtopeers">Submit</button>
                                 </div>
                              </form>
                           <?php  }?>
                        </div>
                        <div class="tab-pane fade assessment show active" id="kt_tab_pane_3" role="tabpanel" aria-labelledby="kt_tab_pane_3">
                           <div class="row">
                              <div class="col-6">
                                 <div class="card-body pt-2 bg-light border-bottom pl-2 pr-2 mt-5">
                                    <div class="d-flex align-items-center mb-10">
                                      <!--begin::Symbol-->
                                      <div class="symbol symbol-75 symbol-light mr-5">
                                       <div class="symbol-group symbol-hover">
                                          <?php if($employees[$emp_id]['image']!=""){ ?>
                                             <div class="symbol symbol-45 symbol-circle" data-toggle="tooltip" data-title="<?php echo $employees[$emp_id]['display_name']; ?>">
                                                <img src="<?php echo "uploads/all/".$employees[$emp_id]['image']; ?>" alt="Details">
                                             </div>
                                          <?php }else{ ?>
                                             <div class="symbol symbol-45 symbol-circle symbol-primary" data-toggle="tooltip" data-title="<?php echo $employees[$emp_id]['display_name']; ?>" >
                                                <span class="symbol-label font-size-h3 font-weight-boldest"><?php echo initials($employees[$emp_id]['display_name']); ?></span>
                                             </div>
                                          <?php } ?>
                                       </div>
                                    </div>
                                    <!--end::Symbol-->

                                    <div class="d-flex flex-column font-weight-bold">
                                       <a href="#" class="text-dark text-hover-primary mb-1 font-size-lg">Self Assessment</a>
                                    </div>
                                 </div>

                                 <?php  if($emp_performance_response_data['employee_status']=='1') {

                                   foreach($emp_questions as $emp_que) {
                                    ?>
                                    <div class="form-group">
                                       <label class="col-12 font-weight-bold" for=""><?php echo $emp_que['assessment_question'];?></label>
                                       <?php if($emp_que['options_display_type']== $typeofquestion[1] ) {
                                          ?>
                                          <div class="ml-5 mt-2">
                                             <?php for($k=0;$k<$emp_que['number_of_options'];$k++){
                                                ?>
                                                <button type="button" class="btn ml-2 <?php echo $employee_responses[$emp_que['id']]['selected_option_index']== $k ? "btn-primary":"btn-outline-primary"; ?>" data-toggle="tooltip" data-title="<?php echo json_decode($emp_que['question_option'],1)[$k]; ?>"><?php echo $k+1; ?></button>

                                             <?php } ?>
                                          </div>
                                          <div class="mt-2 ml-7">
                                             <span class="text-primary"><?php echo $employee_responses[$emp_que['id']]['selected_option_value']; ?> </span> </div>
                                          <?php } elseif($emp_que['options_display_type']== $typeofquestion[2] ) { ?>
                                            <span class="text-primary ml-4"><?php echo $employee_responses[$emp_que['id']]['selected_option_value']; ?></span>
                                         <?php } ?>
                                      </div>
                                   <?php } ?>
                                   <?php } else { ?>


                                    <form method="post" action="">
                                       <input type="hidden" name="reports_to" value="<?php echo $reports_to;?>">
                                       <input type="hidden" name="review_manager" value="<?php echo $review_manager;?>">

                                       <input type="hidden" name="assessment_id" value="<?php echo $selected_assessment_id;?>">
                                       <input type="hidden" name="response_row" value="<?php echo $update_row; ?>">

                                       <?php
                                       $value_id = 0;

                                       foreach($emp_questions as $emp_que) {
                                          $value_id++;
                                          $optionlist = json_decode($emp_que['question_option'],1);
                                          ?>
                                          <div class="form-group mt-5">
                                             <label class="col-12 font-weight-bold" for=""><?php echo $emp_que['assessment_question'];?></label>

                                             <input type="hidden" name="value_id[]" value="<?php echo $emp_que['id'];?>">
                                             <input type="hidden" name="response_rowid<?php echo $emp_que['id'];?>" value="<?php echo $employee_responses[$emp_que['id']]['id']; ?>">

                                             <?php if($emp_que['options_display_type']== $typeofquestion[1] ) {
                                               $response_answered_option = json_decode($employee_responses[$emp_que['id']]['response_option'],1)[1];
                                               ?>
                                               <select aria-label="Select option" name="response_option<?php echo $emp_que['id'];?>" class="form-control" id="select_emp_option_<?php echo $emp_que['id'];?>">
                                                  <?php foreach($optionlist as $opkey=>$opts) { $optiondata = $opkey."||".$opts; ?>
                                                  <option value="<?php echo $optiondata; ?>" <?php if($opts==$response_answered_option){ echo "selected";
} ?> ><?php echo $opts;?></option>
                                               <?php } ?>
                                            </select>
                                         <?php } elseif($emp_que['options_display_type']== $typeofquestion[2] ) {
                                          $response_answered_option = json_decode($employee_responses[$emp_que['id']]['response_option'],1)[0];
                                          ?>
                                          <textarea aria-label="Text area" name="response_option<?php echo $emp_que['id'];?>" class="form-control col-md-12"> <?php echo $response_answered_option; ?></textarea>
                                       <?php } ?>
                                    </div>

                                 <?php } ?>

                                 <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="selfsubmit">Submit</button>
                                 <button id="kt_login_signin_submit_3" class="btn btn-secondary btn-shadow-hover font-weight-bold mr-2" name="selfsave">Save & Finish Later</button>
                              </form>
                           <?php } ?>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="card-body pt-2 bg-light-success border-bottom pl-2 pr-2 mt-5">
                           <div class="d-flex align-items-center mb-10">
                             <!--begin::Symbol-->
                             <div class="symbol symbol-75 symbol-light mr-5">
                              <div class="symbol-group symbol-hover">
                                 <?php if($employees[$reports_to]['image']!=""){ ?>
                                    <div class="symbol symbol-45 symbol-circle" data-toggle="tooltip" data-title="<?php echo $employees[$reports_to]['display_name']; ?>">
                                       <img src="<?php echo "uploads/all/".$employees[$reports_to]['image']; ?>" alt="Details">
                                    </div>
                                 <?php }else{ ?>
                                    <div class="symbol symbol-45 symbol-circle symbol-primary" data-toggle="tooltip" data-title="<?php echo $employees[$reports_to]['display_name']; ?>">
                                       <span class="symbol-label font-size-h3 font-weight-boldest"><?php echo initials($employees[$reports_to]['display_name']); ?></span>
                                    </div>
                                 <?php } ?>
                              </div>
                           </div>
                           <!--end::Symbol-->
                           <div class="d-flex flex-column font-weight-bold">
                              <a href="#" class="text-dark text-hover-primary mb-1 font-size-lg">Manager Assessment</a>
                           </div>
                        </div>
                        <!-- Manager Output Page --->
                        <?php if(($emp_performance_response_data['manager_status']=='1' && $emp_performance_response_data['reviewer_status']=='1') || ( $emp_performance_response_data['reports_to'] == $_SESSION['userid'] && $emp_performance_response_data['manager_status']=='1')  || ( $emp_performance_response_data['reviewer'] == $_SESSION['userid'] && $emp_performance_response_data['manager_status']=='1') ) {


                         foreach($mgr_questions as $mgrs_que) {
                            if($_SESSION['userid']==$emp_id){
                              if($mgrs_que['display_questions']== 0){
                                  $display = 1;
                              }else{
                                  $display = 0;
                              }
                           }else{
                                  $display = 1;
                           }
                         ?>
                         <div class="form-group">
                           <?php if($display==1 ) { ?>
                              <label class="col-12 font-weight-bold" for=""><?php echo $mgrs_que['assessment_question'];?></label>
                              <div class="ml-5 mt-2">
                                 <?php if($mgrs_que['options_display_type']== $typeofquestion[1] ) {
                                    for($k=0;$k<$mgrs_que['number_of_options'];$k++){

                                       ?>
                                       <button type="button" class="btn ml-2 <?php echo $manager_responses[$mgrs_que['id']]['selected_option_index']== $k ? "btn-primary":"btn-outline-primary"; ?>" data-toggle="tooltip" data-title="<?php echo json_decode($emp_que['question_option'],1)[$k]; ?>" ><?php echo $k+1; ?></button>
                                    <?php } ?>
                                 </div>
                                 <div class="mt-2 ml-7">
                                    <span class="text-primary"><?php echo $manager_responses[$mgrs_que['id']]['selected_option_value']; ?> </span>
                                 </div>
                              <?php } elseif($mgrs_que['options_display_type']== $typeofquestion[2] ) {
                                 ?>
                                 <span class="text-primary ml-4"><?php echo $manager_responses[$mgrs_que['id']]['selected_option_value']; ?></span>

                              <?php } ?>
                              <?php } ?>
                                 </div>

                              <?php  }?>
                                 </div>

                               <div class="row mt-2 ml-2">
                             <label class="col-3 font-weight-bold" for="">Overall Rating * </label>
                             <input aria-label="NOT RATED" type="text" name="manager_rating" class="form-control mb-10 col-lg-4" placeholder="NOT RATED" readonly value="<?php echo $mgr_rating=="0"?"":$mgr_rating;?>">
                          </div>
                          </div>

                           <?php }  elseif($emp_performance_response_data['employee_status']=='1' && $emp_performance_response_data['reports_to'] == $_SESSION['userid'])  { ?>

                              <form method="post" action="">
                                 <input type="hidden" name="review_manager" value="<?php echo $review_manager;?>">
                                 <input type="hidden" name="reports_to" value="<?php echo $reports_to;?>">
                                 <input type="hidden" name="assessment_id" value="<?php echo $selected_assessment_id;?>">
                                 <input type="hidden" name="response_row" value="<?php echo $update_row; ?>">
                                 <div class="custom-content">

                                   <?php
                                   $value_id = 0;
                                   foreach($mgr_questions as $mgr_que) {
                                      $value_id++;
                                      $optionlist = json_decode($mgr_que['question_option'],1);

                                      ?>
                                      <div class="form-group mt-5">
                                         <input type="hidden" name="value_id[]" value="<?php echo $mgr_que['id'];?>">
                                         <input type="hidden" name="response_rowid<?php echo $emp_que['id'];?>" value="<?php echo $manager_responses[$mgrs_que['id']]['id']; ?>">

                                         <label class="col-12 font-weight-bold" for=""><?php echo $mgr_que['assessment_question'];?></label>
                                         <?php if($mgr_que['options_display_type']== $typeofquestion[1] ) {
                                          $response_answered_option = json_decode($manager_responses[$mgrs_que['id']]['response_option'],1)[1];
                                          ?>
                                          <select aria-label="Select option" class="form-control" name="response_option<?php echo $mgr_que['id'];?>" id="select_mgr_option_<?php echo $mgr_que['id'];?>">
                                           <?php foreach($optionlist as $opkey=>$opts) { $optiondata = $opkey."||".$opts; ?>
                                           <option value="<?php echo $optiondata; ?>" <?php if($opts==$response_answered_option){ echo "selected";
} ?> ><?php echo $opts;?></option>
                                        <?php } ?>
                                     </select>
                                  <?php } elseif($mgr_que['options_display_type']== $typeofquestion[2] ) {
                                   $response_answered_option = json_decode($manager_responses[$mgrs_que['id']]['response_option'],1)[0];
                                   ?>
                                   <textarea aria-label="Text area" name="response_option<?php echo $mgr_que['id'];?>" class="form-control col-md-12"> <?php echo $response_answered_option; ?>  </textarea>
                                <?php } ?>

                             </div>
                          <?php } ?>
                          <div class="row mt-2 ml-2">
                             <label class="col-3 font-weight-bold" for="">Overall Rating * </label>
                             <input aria-label="NOT RATED" type="text" name="manager_rating" class="form-control mb-10 col-lg-4" placeholder="NOT RATED" value="<?php echo $mgr_rating=="0"?"":$mgr_rating;?>">
                          </div>
                          <button id="kt_login_signin_submit_4" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="managersubmit">Submit</button>
                          <button id="kt_login_signin_submit_5" class="btn btn-secondary btn-shadow-hover font-weight-bold mr-2" name="managersave">Save & Finish Later</button>
                       </div>
                    </form>
                 <?php }
                      //Manager Lock Page
                 else { ?>
                    <div class="custom-content text-center">
                       <h4 class="text-muted pl-5 pr-5"><i class="fas fa-lock"></i>
                        Complete your assessment to unlock your manager's Assessment
                     </h4>
                  </div>
               <?php } ?>

            </div>

            <div class="card-body pt-2 bg-light-warning border-bottom pl-2 pr-2 mt-5">
               <div class="d-flex align-items-center mb-10">
                 <!--begin::Symbol-->
                 <div class="symbol symbol-75 symbol-light mr-5">
                  <div class="symbol-group symbol-hover">
                     <?php if($employees[$review_manager]['image']!=""){ ?>
                        <div class="symbol symbol-45 symbol-circle" data-toggle="tooltip" data-title="<?php echo $employees[$review_manager]['display_name']; ?>">
                           <img src="<?php echo "uploads/all/".$employees[$review_manager]['image']; ?>" alt="Details">
                        </div>
                     <?php }else{ ?>
                        <div class="symbol symbol-45 symbol-circle symbol-primary" data-toggle="tooltip" data-title="<?php echo $employees[$review_manager]['display_name']; ?>">
                           <span class="symbol-label font-size-h3 font-weight-boldest"><?php echo initials($employees[$review_manager]['display_name']); ?></span>
                        </div>
                     <?php } ?>
                  </div>
               </div>
               <!--end::Symbol-->
               <div class="d-flex flex-column font-weight-bold">
                  <a href="#" class="text-dark text-hover-primary mb-1 font-size-lg">Review Manager Assessment</a>
               </div>
            </div>
            <div class="custom-content">

               <!-- ReviewManager Output Page --->
               <?php if($emp_performance_response_data['reviewer_status']=='1') {
                  foreach($reviewmgr_questions as $emp_que) {
                     ?>
                     <div class="form-group">
                        <label class="col-12 font-weight-bold" for=""><?php echo $emp_que['assessment_question'];?></label>
                        <div class="ml-5 mt-2">
                           <?php if($emp_que['options_display_type']== $typeofquestion[1] ) {
                              for($k=0;$k<$emp_que['number_of_options'];$k++){
                                 ?>
                                 <button type="button" class="btn ml-2 <?php echo $reviewer_responses[$emp_que['id']]['selected_option_index']== $k ? "btn-primary":"btn-outline-primary"; ?>" data-toggle="tooltip" data-title="<?php echo json_decode($emp_que['question_option'],1)[$k]; ?>"><?php echo $k+1; ?></button>
                              <?php } ?>
                           </div>
                           <div class="mt-2 ml-7">
                           <span class="text-primary"><?php echo $reviewer_responses[$emp_que['id']]['selected_option_value']; ?> </span> </div>
                        <?php } elseif($emp_que['options_display_type']== $typeofquestion[2] ) { ?>
                          <span class="ml-4"><?php echo $reviewer_responses[$emp_que['id']]['selected_option_value']; ?></span>
                       <?php } ?>
                    </div>
                 <?php }
              } elseif($emp_performance_response_data['reviewer_status']=='0' && $emp_performance_response_data['reviewer'] == $_SESSION['userid']) { ?>
               <form method="post" action="">
                  <input type="hidden" name="reports_to" value="<?php echo $reports_to;?>">
                  <input type="hidden" name="review_manager" value="<?php echo $review_manager;?>">

                  <input type="hidden" name="assessment_id" value="<?php echo $selected_assessment_id;?>">
                  <input type="hidden" name="response_row" value="<?php echo $update_row; ?>">
                  <?php
                  $value_id = 0;
                  foreach($reviewmgr_questions as $emp_que) {
                     $value_id++;
                     $optionlist = json_decode($emp_que['question_option'],1);
                     ?>
                     <div class="form-group mt-5">
                        <label class="col-12 font-weight-bold" for=""><?php echo $emp_que['assessment_question'];?></label>

                        <input type="hidden" name="value_id[]" value="<?php echo $emp_que['id'];?>">
                        <input type="hidden" name="response_rowid<?php echo $emp_que['id'];?>" value="<?php echo $reviewer_responses[$emp_que['id']]['id']; ?>">

                        <?php if($emp_que['options_display_type']== $typeofquestion[1] ) {
                          $response_answered_option = json_decode($reviewer_responses[$emp_que['id']]['response_option'],1)[1];
                          ?>
                          <select aria-label="Select option" name="response_option<?php echo $emp_que['id'];?>" class="form-control" id="select_reviewer_option_<?php echo $emp_que['id'];?>">
                             <?php foreach($optionlist as $opkey=>$opts) { $optiondata = $opkey."||".$opts; ?>
                             <option value="<?php echo $optiondata; ?>" <?php if($opts==$response_answered_option){ echo "selected";
} ?> ><?php echo $opts;?></option>
                          <?php } ?>
                       </select>
                    <?php } elseif($emp_que['options_display_type']== $typeofquestion[2] ) {
                     $response_answered_option = json_decode($reviewer_responses[$emp_que['id']]['response_option'],1)[0];
                     ?>
                     <textarea aria-label="Text area" name="response_option<?php echo $emp_que['id'];?>" class="form-control col-md-12"> <?php echo $response_answered_option; ?></textarea>
                  <?php } ?>
               </div>

            <?php } ?>

            <button id="kt_login_signin_submit_6" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="reviewersubmit">Submit</button>
            <button id="kt_login_signin_submit_7" class="btn btn-secondary btn-shadow-hover font-weight-bold mr-2" name="reviewersave">Save & Finish Later</button>
         </form>
      <?php } else { ?>
         <div class="custom-content text-center">
           <h4 class="text-muted pl-5 pr-5"><i class="fas fa-lock"></i>
             You can unlock once it is completed.
          </h4>
       </div>
    <?php } ?>
 </div>

 <!-- FOOTER -->
</div>
</div>
</div>

<!-- custom html end here -->
