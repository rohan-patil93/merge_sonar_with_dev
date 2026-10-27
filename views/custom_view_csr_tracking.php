<?php
if(isset($_GET['ajax_page']) && $_GET['ajax_page'] == 'Y'){
  include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';
}

 $_SESSION['apply_js']['dropzone'] = "1";

  function csrTrackingViewDecodeRid($ridParam)
  {
    if ($ridParam == '') {
      return 0;
    }
    $rid = encodeId($ridParam);
    $rid = urldecode($rid);
    $ridParts = explode(':', $rid);
    return (int)($ridParts[1] ?? 0);
  }

  function csrTrackingViewEncodeRid($proposalId)
  {
    $proposalId = (int)$proposalId;
    if ($proposalId <= 0) {
      return '';
    }
    return deocdeId('id:' . $proposalId);
  }

  function csrTrackingViewIsApiSuccess($response)
  {
    if (!is_array($response)) {
      return false;
    }
    $status = strtolower((string)($response['status'] ?? ''));
    return $status == 'success' || $status == 'sucess';
  }

  function csrTrackingViewSetFlash($type, $message)
  {
    $_SESSION['csr_tracking_toast'] = array(
      'type' => $type,
      'message' => $message
    );
  }

  function csrTrackingViewParseApiResponse($apiResponseRaw)
  {
    $response = json_decode($apiResponseRaw, true);
    if (!is_array($response)) {
      $response = json_decode((string)$apiResponseRaw, true);
    }
    return is_array($response) ? $response : array();
  }

  function csrTrackingViewApiMessage($response, $apiResponseRaw, $defaultSuccess, $defaultError)
  {
    if (csrTrackingViewIsApiSuccess($response)) {
      $message = trim((string)($response['message'] ?? ''));
      return $message != '' ? $message : $defaultSuccess;
    }
    $message = '';
    if (is_array($response)) {
      $message = trim((string)($response['message'] ?? ($response['msg'] ?? '')));
      if ($message == '' && isset($response['error_no'])) {
        $message = 'DB Error: ' . $response['error_no'];
      }
    }
    if ($message == '') {
      $message = trim((string)$apiResponseRaw);
    }
    return $message != '' ? $message : $defaultError;
  }

  $csr_tracking_company_id = $_SESSION['company_id'] ?? 0;
  $csr_tracking_user_id = $_SESSION['userid'] ?? '';

  if (isset($_POST['submit']) && $_POST['submit'] == 'save_project_dates') {
    $proposal_id = isset($_POST['proposal_id']) ? (int)$_POST['proposal_id'] : 0;
    $encodedRid = isset($_POST['Rid']) ? (string)$_POST['Rid'] : csrTrackingViewEncodeRid($proposal_id);

    if ($proposal_id > 0) {
      $savePost = array(
        'proposal_id' => $proposal_id,
        'company_id' => $csr_tracking_company_id,
        'user_id' => $csr_tracking_user_id,
        'project_start_date' => $_POST['project_start_date'] ?? '',
        'project_end_date' => $_POST['project_end_date'] ?? ''
      );
      $saveUrl = API . "?mode=saveCsrProposalProjectDates&method=post&company_id=" . urlencode((string)$csr_tracking_company_id) . "&user_id=" . urlencode((string)$csr_tracking_user_id);
      $apiResponseRaw = file_post_contents($saveUrl, $savePost, 'update');
      $apiResponse = csrTrackingViewParseApiResponse($apiResponseRaw);
      if (csrTrackingViewIsApiSuccess($apiResponse)) {
        csrTrackingViewSetFlash('success', csrTrackingViewApiMessage($apiResponse, $apiResponseRaw, 'Project dates saved successfully.', 'Project dates saved successfully.'));
      } else {
        csrTrackingViewSetFlash('error', csrTrackingViewApiMessage($apiResponse, $apiResponseRaw, 'Project dates saved successfully.', 'Failed to save project dates.'));
      }
      header("location:index.php?option=COM_VIEW_CSR_TRACKING&Rid=" . urlencode($encodedRid));
      exit;
    } else {
      csrTrackingViewSetFlash('error', 'Invalid project details.');
      header("location:index.php?option=COM_VIEW_CSR_TRACKING&Rid=" . urlencode($encodedRid));
      exit;
    }
  }

  if (isset($_POST['submit']) && $_POST['submit'] == 'save_tracking') {
    $proposal_id = isset($_POST['proposal_id']) ? (int)$_POST['proposal_id'] : 0;
    $encodedRid = isset($_POST['Rid']) ? (string)$_POST['Rid'] : csrTrackingViewEncodeRid($proposal_id);

    if ($proposal_id > 0) {
      $savePost = $_POST;
      $savePost['company_id'] = $csr_tracking_company_id;
      $savePost['user_id'] = $csr_tracking_user_id;
      $savePost['proposal_id'] = $proposal_id;
      $saveUrl = API . "?mode=saveCsrProjectTracking&method=post&company_id=" . urlencode((string)$csr_tracking_company_id) . "&user_id=" . urlencode((string)$csr_tracking_user_id);
      $apiResponseRaw = file_post_contents($saveUrl, $savePost, 'update');
      $apiResponse = csrTrackingViewParseApiResponse($apiResponseRaw);
      if (csrTrackingViewIsApiSuccess($apiResponse)) {
        csrTrackingViewSetFlash('success', csrTrackingViewApiMessage($apiResponse, $apiResponseRaw, 'Tracking details saved successfully.', 'Tracking details saved successfully.'));
      } else {
        csrTrackingViewSetFlash('error', csrTrackingViewApiMessage($apiResponse, $apiResponseRaw, 'Tracking details saved successfully.', 'Failed to save tracking details.'));
      }
      header("location:index.php?option=COM_VIEW_CSR_TRACKING&Rid=" . urlencode($encodedRid));
      exit;
    } else {
      csrTrackingViewSetFlash('error', 'Invalid project details.');
      header("location:index.php?option=COM_VIEW_CSR_TRACKING&Rid=" . urlencode($encodedRid));
      exit;
    }
  }

  if ($_GET['Rid']) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $table_name1 = 'csr_project_tracking';
    $response =   getTrackingData($rid, $table_name1);

    if (!$response) {
      $tracking_response1 = array();
      $proposal_return = getProposalData($rid);
      if ($proposal_return['tracking_data']) {
        $trackingData = json_decode($proposal_return['tracking_data'], true);
        $i = 1;
        foreach ($trackingData as $tracking) {
          $tracking['proposal_id'] = $rid;
          $tracking['status'] = '';
          $tracking['current_status'] = '0';
          $tracking['status_date'] = nowDate();
          $tracking['company_id'] = $_SESSION['company_id'];
          $tracking['created_date'] = nowDate();
          $tracking['created_by'] = $proposal_return['created_by'];

          $url = API . "?mode=adddata&table=" . $table_name1 . "&method=post";
          $tracking_response1[$i] = file_post_contents($url, $tracking, 'insert');
          $i++;
        }
      }
      $response =  getTrackingData($rid, $table_name1);
    } else {
      $proposal_return = getProposalData($rid);
    }
  }
  function getTrackingData($rid, $table_name1)
  {
    $fid = 'proposal_id';
    $url = API . "?mode=gettabledetails&method=get&rid=" . $rid . "&table=" . $table_name1 . "&fid=" . $fid;
    $che = callAPI($url);
    $return = json_decode($che, 1);
    return $return;
  }
  function getProposalData($rid)
  {
    $table_name2 = 'csr_proposal_data';
    $fid = 'id';
    $url = API . "?mode=singlerowtabledata&method=get&rid=" . $rid . "&table=" . $table_name2 . "&fid=" . $fid;
    $proposal_data = callAPI($url);
    $proposal_return = json_decode($proposal_data, 1);
    return $proposal_return;
  }
  $table_name2 = 'csr_proposal_data';
  $url = API . "?mode=gettabledetails&method=get&table=" . $table_name2;
  $response2 = callAPI($url);
  $response2 = json_decode($response2, 1);

  ?>

<div
    id="csr_config"
    data-rid="<?= htmlspecialchars(deocdeId('id:' . $rid), ENT_QUOTES, 'UTF-8') ?>"
    data-app-name="<?= htmlspecialchars(ucwords($_SESSION['config']['name']), ENT_QUOTES, 'UTF-8') ?>"
    data-success-msg="<?= htmlspecialchars($_SESSION['error_message']['9059'], ENT_QUOTES, 'UTF-8') ?>"
    hidden>
</div>

<link rel="stylesheet" href="assets/css/custom_view_csr_tracking.css">

 <div class="page-body" id="CSRContiner">
   <!-- Pradeep HTML Start here -->
   <div class="card">
     <div class="card-block">
       <div class="text-right mb-2">
         <button type="button" class="btn btn-primary waves-effect waves-light m-r-5 csr-open-tracking-popup" data-rid="<?php echo htmlspecialchars(deocdeId('id:' . $rid), ENT_QUOTES, 'UTF-8'); ?>" data-proposal-id="<?php echo (int)$rid; ?>">
           <i class="fa fa-plus m-r-5"></i> Tracking
         </button>
         <button type="button" class="btn btn-primary waves-effect waves-light m-r-5 csr-open-details-popup" data-rid="<?php echo htmlspecialchars(deocdeId('id:' . $rid), ENT_QUOTES, 'UTF-8'); ?>" data-proposal-id="<?php echo (int)$rid; ?>">
           <i class="fa fa-calendar m-r-5"></i> Add Details
         </button>
         <a class="btn btn-primary waves-effect waves-light m-r-5" href="index.php?option=COM_INSERT_CSR_PROJECT_DOCUMENTS&Rid=<?php echo deocdeId('id:' . $rid); ?>">
           <i class="fa fa-folder-open m-r-5"></i> Documents
         </a>
         <a class="btn btn-primary waves-effect waves-light" href="index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?php echo deocdeId('id:' . $rid); ?>">
           <i class="fa fa-file-text-o m-r-5"></i> Project View
         </a>
       </div>
       <ul class="nav nav-tabs  tabs">
         <?php
          foreach ($response2 as $row) :
            if (
              isset($row['current_status']) && $row['current_status'] == 199 &&
              (!isset($row['tracking_status']) || $row['tracking_status'] == 0)
            ) :

              $isActive = (isset($rid) && $row['id'] == $rid) ? 'active' : '';
          ?>
             <li class="nav-item">
               <a class="nav-link <?php echo $isActive; ?>" href="index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?php echo  deocdeId('id:' . $row['id']); ?> " role="tab"><?php echo $row['subject']; ?></a>
             </li>
         <?php endif;
          endforeach; ?>
       </ul>
     </div>
   </div>

   <div class="tab-content tabs mt-3">
     <div class="tab-pane active" id="home1" role="tabpanel">
       <div class="row">
         <div class="col-lg-12">
           <div class="card mb-xl-10">
             <div class="card-body pt-9 pb-0">
               <div class="banner">
                 <img src="./assets/images/user-profile/bg-img1.jpg" class="w-100" alt="">
               </div>
             </div>
             <div class="profile-img" >
               <div class="row align-items-bottom">
                 <div class="col-lg-4 m-l-30">
                   <div class="image-input image-input-empty image-input-outline" id="kt_image_5">
                     <form method="post" id="profile_pic_file" enctype="multipart/form-data" class="position-relative">
                       <label class="btn-hover-text-primary position-absolute  text-white profile-pic-edit" data-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Edit Profile Pic">
                         <span><i class="ti-pencil-altj"></i></span>
                         <input aria-label="Profile Pic File" type="file" name="profile_pic_file" accept=".png, .jpg, .jpeg"   class="profile-pic-input">
                       </label>
                       <img src="./assets/images/faq_man.jpg" class="img-radius img-90" alt="">
                       <span id="verified_btn">
                         <button type="button" class="btn waves-effect waves-light btn-success pt-1 pb-1 m-l-20">
                           <i class="ti-check m-r-5"></i> Active
                         </button>
                       </span>
                     </form>
                   </div>
                   <div class="mt-3 d-block mb-3">
                     <div class="d-flex mr-3 mb-2">
                       <a href="#" class="text-dark-75 f-18 text-hover-primary font-size-h5 font-weight-bold mr-10 f-w-600"><?php echo $_SESSION['us_name']; ?></a>
                     </div>
                     <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                       <i class="ti-email f-14 text-primary"></i>&nbsp;<?php echo $_SESSION['email']; ?></a>


                     <div class="font-weight-bold text-dark-50 mt-1 f-14">
                       <b class="f-w-600">Project Requestor : </b><?php echo $proposal_return['name'] ?>&nbsp;
                     </div>
                   </div>
                 </div>
                 <?php
                  $url = API . "?mode=getapprovedmembers&method=get&id=".$rid."&table=csr_proposal_data";
                  $result = callAPI($url);
                  $result = json_decode($result, 1);
                  ?>
                 <div class="col-lg-7 p-3">
                   <div class="row mt-5">
                      <?php foreach($result as $row){ ?>
                        <div class="col-3 mt-1 b-l-default text-center">
                          <div class="s-caption  text-primary"><i class="ti ti-user f-20"></i></div>
                          <div class="s-cont d-inline-block">
                            <span class="f-w-700 m-b-0"><?php echo $row['display_name'] ?></span>
                            <p class="m-b-0 f-10"><?php echo $row['user_email'] ?></p>
                          </div>
                        </div>
                      <?php }?>
                   </div>

                 </div>
               </div>
             </div>
           </div>
         </div>

         <?php if ($response) {
            $allCompleted = true;
            foreach ($response as $tracking) {
              if ($tracking['current_status'] !== '199') {
                $allCompleted = false;
                break;
              }
            }

            if($proposal_return['tracking_status'] == 0){
            echo '<div class="d-flex justify-content-end">';
            echo '  <button class="btn btn-primary waves-effect waves-light btn-s csr-complete-btn'. $allCompleted ? '' : 'd-none'. '"
                           type="button" data-rid="<?php echo $rid; ?>"';
            echo '</div>';
          }
            $currentDate = date('Y-m-d');
            foreach ($response as  $tracking) {
              $endDate = $tracking['end_date'];
              $color = ($endDate < $currentDate) ? 'red' : 'inherit';
              $status = $tracking['status'] ?? 'not_started';
              $startButtonClass = ($status == 'started' || $status == 'completed') ? 'd-none' : '';
              $holdCompleteButtonClass = ($status == 'not_started' || $status == 'completed' || $status == 'hold') ? 'd-none' : '';
          ?>
             <div class="col-lg-4">
               <div class="card">
                  <div class="card-header">
                    <div class="row">
                      <div class="col-lg-6">
                        <h5 class="f-18 mb-2 mt-2"><?php echo $tracking['tracking_name'] ?? ''; ?></h5>
                      </div>
                      <div class="col-lg-6">
                        <button class="btn btn-danger waves-effect waves-light csr-status-btn <?php echo $holdCompleteButtonClass; ?>"
                                data-id="<?php echo $tracking['id']; ?>"
                                data-status="hold"
                                data-rid="<?php echo $rid; ?>">Hold</button>

                        <button class="btn btn-success waves-effect waves-light csr-status-btn <?php echo $holdCompleteButtonClass; ?>"
                                data-id="<?php echo $tracking['id']; ?>"
                                data-status="completed"
                                data-rid="<?php echo $rid; ?>">Complete</button>

                      </div>
                    </div>
                  </div>
                  <div class="card-block text-center">

                        <div class="d-block">
                          <?php if ($status == 'completed'){
                              $table_name = 'csr_project_tracking';

                              $workflowsteps = getWorkflowStatus($table_name,$tracking['id'],$tracking['current_status']);
                              echo $workflowsteps['workflow_btn'];
                            }
                          ?>
                        </div>
                        <?php if ($status == 'completed'): ?>
                        <img src="assets/images/complete.png" alt="completed" class="csr-complete-img" >
                        <?php endif; ?>


                  </div>
                 <div class="card-body">
                   <div class="row">
                     <div class="col-lg-6">
                       <p class="m-0 f-12 text-muted">Start </p>
                       <h4 class="f-14 f-w-600 m-0 mt-1"><?php echo fromDate($tracking['start_date']);  ?></h4>
                     </div>
                     <div class="col-lg-6">
                       <p class="m-0 f-12 text-muted">End</p>
                       <h4 class="f-14 f-w-600 m-0 mt-1 csr-end-date" data-color="<?php echo $color; ?>"><?php echo fromDate($endDate);  ?></h4>
                     </div>
                     <div class="col-lg-6 mt-3">
                       <p class="m-0 f-12 text-muted">Budget</p>
                       <h4 class="f-14 f-w-600 m-0 mt-1"><?php echo $tracking['tracking_budget'];  ?></h4>
                     </div>

                     <div class="col-lg-12 mt-3 custom_file_upload">
                        <?php $file_array = array('application_table_name'=>'csr_project_tracking','transaction_id'=>$tracking['id'] ,'is_active'=>'0','width'=>$file_data['fileuplodingwidth']!=""?$file_data['fileuplodingwidth']:'12','deletewidth'=>$file_data['fileuplodingdleetewidth']!=""?$file_data['fileuplodingdleetewidth']:'12','deleteview'=>$file_data['fileuplodingdeleteddisplay']!=""?$file_data['fileuplodingdeleteddisplay']:0,'file_size'=>$file_data['file_size']!=""?$file_data['file_size']:'200000','file_limit'=>$file_data['number_of_files']!=""?$file_data['number_of_files']:'6','lable'=>$file_data['fileuplodinglabel']);
                          fileUplodingUi($file_array);
                        ?>

                     </div>
                   </div>
                 </div>
                 <div class="card-footer border-top text-center">

<button class="btn btn-primary waves-effect waves-light csr-start-btn <?php echo $startButtonClass ?>"
        data-id="<?php echo $tracking['id']; ?>"
        data-rid="<?php echo $rid; ?>">Start</button>

<button class="btn btn-success waves-effect waves-light csr-po-btn"
        data-id="<?php echo $tracking['id']; ?>"
        data-rid="<?php echo $rid; ?>">PO Details</button>

<button class="btn btn-primary waves-effect waves-light csr-settlement-btn"
        data-id="<?php echo $tracking['id']; ?>"
        data-rid="<?php echo $rid; ?>">Settlement</button>

                 </div>
               </div>
             </div>
         <?php
            }
          } ?>

       </div>
     </div>

   </div>
   <!-- Pradeep HTML End here -->
 </div>

<?php
if (!empty($_SESSION['csr_tracking_toast'])) {
  $csrTrackingToast = $_SESSION['csr_tracking_toast'];
  unset($_SESSION['csr_tracking_toast']);
  $csrTrackingToastSuccess = (($csrTrackingToast['type'] ?? '') == 'success');
  $csrTrackingToastIcon = $csrTrackingToastSuccess ? 'fa fa-thumbs-up' : 'fa fa-thumbs-down';
  $csrTrackingToastClass = $csrTrackingToastSuccess ? 'form-label label label-success' : 'form-label label label-danger';
  $csrTrackingToastMessage = htmlspecialchars((string)($csrTrackingToast['message'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
  tostarnotify('top', 'right', '<?php echo $csrTrackingToastIcon; ?>', '<?php echo $csrTrackingToastClass; ?>', 'animated flipInX', 'animated flipOutX', '<?php echo $csrTrackingToastMessage; ?>', '');
});
</script>
<?php } ?>

<script src="assets/js/custom_view_csr_tracking.js"></script>

