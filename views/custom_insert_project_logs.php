<?php
  if($_POST['ajaxcall'] == 'Y'){
      include_once '../includes/session.php';
      include_once '../dbconfig.php';
      include_once '../comm.php';
      $candidate_id = $_POST['candidate_id'];
  }
  $auditid = $_POST['id'];
  $url4 = API."?mode=getcandidatelogs&id=".$candidate_id;
  $tasklists = callAPI($url4);
  $tasklist = json_decode($tasklists,1);
  $tasklogdetails = $tasklist['logs']['logsdetails'];
?>
<script type="text/javascript">
  //           url: "ajax.php",
  //           type: "POST",
  //           data: 'page=getdocumentlists&id='+catid+'&audit_id='+audit_id,

function replayMessage(id,message,message_type){
  $('.replay_div').show("slow");
  $("#replay_message").text(message);
  $("#replay_id").val(id);
  $("#replay_message_type").val(message_type);
  $(".private_check").prop('disabled', true);
  $("#checkbosshowhide").css("opacity","0.4");
}
function replayMessageRemoved(){
  $('.replay_div').hide("slow");
  $("#replay_message").text(' ');
  $("#replay_id").val('0');
  $("#replay_message_type").val(' ');
  $(".private_check").prop('disabled', false);
  $("#checkbosshowhide").css("opacity","1");
}
</script>
<div class="row " style=" height: 400px;">
                              <?php if(count((array)$tasklogdetails) !='0'){ ?>
                                <div class="custom-scroll" style=" height: 400px;">
                                  <?php foreach($tasklogdetails as $ky=>$datares){

                                     $messgae = "";
                                     $messgae .= $datares['field_group'] !=""?"<span class='f-w-700'>".$datares['field_group']." - </span>":"";
                                     $message_type = json_decode($datares['message_type'],1);
                                     $showornot = '0';
                                  if($showornot == '0'){
                                   $empdeails = $tasklist['users_list'][$datares['user_id']];

                                   $replay_msg = "";
                                   if($datares['parent_id'] != '0'){
                                      $replay_details = $tasklogdetails[$datares['parent_id']];
                                      $replay_msg = '<div class="msg p-3 mt-2"><p class="m-b-0">'.$replay_details['description'].'</p></div>';
                                  }
                                  $datetime = date('d-m-Y H:i a',strtotime($datares['response_time']));

                                  if($datares['user_id'] == $_SESSION['userid']){
                                      ?>
                                      <div class="row m-b-20 send-chat">
                                         <div class="col">
                                            <div class="msg">
                                               <i class="fa fa-reply float-left cursor-pointer" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Replay" onclick="replayMessage('<?php echo $datares['id']; ?>','<?php echo $datares['description']; ?>','<?php echo urlencode($datares['message_type']); ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                               <span class="float-right"></span>
                                               <p class="m-b-0"><?php echo $messgae." ".$datares['description']; ?></p>
                                               <?php
                                                echo $replay_msg;
                                               if(count((array)$tasklist['logs']['attachment'][$ky])!='0'){ ?>
                                                  <span class="form-label col-form-label d-block"><i class="ti-clip"></i> Attchment : </span>
                                                  <?php  $im=1; foreach($tasklist['logs']['attachment'][$ky] as $attchmentt){ ?>
                                                     <a href="uploads/attachments/<?php echo $attchmentt['actual_file_name']; ?>" download><?php echo $im.") ".$attchmentt['file_name']; ?></a></br>
                                                     <?php $im++; ?>
                                                     <?php } ?>
                                                     <?php } ?>
                                                 </div>
                                                 <p class="text-muted m-b-0 f-12"><i class="fa fa-clock-o m-r-10"></i><?php echo $datetime; ?></p>
                                             </div>
                                             <div class="col-auto p-l-0" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $empdeails['display_name']; ?>">
                                                <?php if($empdeails['user_photo']!=""){ ?>
                                                   <img src="uploads/all/<?php echo $empdeails['user_photo']; ?>"   class="img-radius img-40" alt="User Profile">
                                               <?php } else { ?>
                                                   <span  alt="user" class="img-radius img-40 p-10" ><?php echo initials($empdeails['display_name']); ?></span>
                                               <?php } ?>
                                           </div>
                                       </div>

                                   <?php  }else{   ?>
                                      <div class="row m-b-20 received-chat">
                                         <div class="col-auto p-r-0" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $empdeails['display_name']; ?>">
                                            <?php if($empdeails['user_photo']!=""){ ?>
                                               <img src="uploads/all/<?php echo $empdeails['user_photo']; ?>"   class="img-radius img-40" alt="User Profile">
                                           <?php } else { ?>
                                               <span  alt="user" class="img-radius img-40 p-10" ><?php echo initials($empdeails['display_name']); ?></span>
                                           <?php } ?>
                                       </div>
                                       <div class="col">
                                          <div class="msg">
                                             <i class="fa fa-reply float-right cursor-pointer" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Replay" onclick="replayMessage('<?php echo $datares['id']; ?>','<?php echo $datares['description']; ?>','<?php echo urlencode($datares['message_type']); ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                             <p class="m-b-0"><?php echo $messgae." ".$datares['description']; ?></p>
                                             <?php echo $replay_msg;
                                             if(count((array)$tasklist['logs']['attachment'][$ky])!='0'){ ?>
                                                <span class="form-label col-form-label d-block"><i class="ti-clip"></i> Attchment : </span>
                                                <?php  $im=1; foreach($tasklist['logs']['attachment'][$ky] as $attchmentt){ ?>
                                                   <a href="uploads/attachments/<?php echo $attchmentt['actual_file_name']; ?>" download><?php echo $im.") ".$attchmentt['file_name']; ?></a></br>
                                                   <?php $im++; ?>
                                                   <?php } ?>
                                                   <?php } ?>
                                               </div>
                                               <p class="text-muted m-b-0 f-12"><i class="fa fa-clock-o m-r-10"></i><?php echo $datetime; ?></p>
                                           </div>
                                       </div>
                                   <?php } ?>
                                   <?php } ?>
                                   <?php } ?>

                               </div>
                           <?php }else{ ?>
                            <div class="card-px text-center py-20 mt-2">
                                <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Query </p>
                                <div class="text-center px-4">
                                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                              </div>
                          </div>
                      <?php } ?>
                  </div>
                <form method="post" enctype="multipart/form-data" class="p-t-10">
                  <input type="hidden" name="rid" value="<?php echo $candidate_id; ?>">
                  <input type="hidden" name="display_name" value="<?php echo $_SESSION['display_name']; ?>">
                  <input type="hidden" name="parent_id" id="replay_id" value="0">
                  <input type="hidden" name="replay_message_type" id="replay_message_type" value="">
                  <div class="bg-info p-2 b-radius-5 replay_div" style="display: none;">
                     <button type="button" class="btn p-0 border-0 bg-transparent ti-close float-right text-dark f-12" onclick="replayMessageRemoved()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button><p class="mb-0" id="replay_message"></p>
                 </div>
                 <div class="input-group input-group-button cursor-pointer" <?php echo $notshowmessgaeshow; ?>>
                     <button type="button" class="input-group-text addon-style p-0" >
                        <div class="image-input image-input-empty image-input-outline mt-2" id="kt_image_5"  >
                           <label class="btn-hover-text-primary mt-1" data-action="change" data-toggle="tooltip" title="" data-original-title="Uploade File" aria-label="Upload File">
                              <i class="ti-clip f-30 mr-5 mt-2" ></i>
                              <input aria-label="Files[]" type="file" name="files[]" multiple/>
                          </label>
                      </div>
                  </button>
                  <textarea aria-label="Eg. Descriptions" class="form-control  width1" name="description" value="" placeholder="Eg. Descriptions" required ></textarea>
                  <button class="input-group-text addon-style cursor-pointer" name="submitlog" ><span class=""><i class="ti-share"></i></span></button>
              </div>
              <div class="form-group">
                  <span class="form-label">Field Group</span>
                  <select aria-label="Application Query" class="form-select js-example-basic-single" name="application_query_id">
                    <option value="0">Select Any One</option>
                <?php foreach($application_query as $applicationquery){ ?>
                    <option   value="<?php echo $applicationquery['id']; ?>"><?php echo $applicationquery['field_group'];  ?></option>
                <?php } ?>
                  </select>
              </div>
        </form>
