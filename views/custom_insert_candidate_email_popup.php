<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
    $job_posting_id = $_POST['job_posting_id'];

    $url3 = API."?mode=getcandidateemailpopup&candidate_id=".$_POST['cand_id']."&email_id=".$_POST['email_id']."&temp_id=".$_POST['temp_id'];
    $data_details3 = callAPI($url3);
    $result3 = json_decode($data_details3,1);
    $email_content = $result3['email_content'];
    foreach($email_content as $emailcontent){
         $emailcontentlist[$emailcontent['template_reference_id']] = $emailcontent;
    }
    $email_logs = $result3['email_logs'];
    $emaildetail = $emailcontentlist[$_POST['temp_id']];
?>
<script type="text/javascript">
    function emailTemplateId(temp_id){
        if(temp_id == "-1"){
            $('#subject').attr('readonly', false);
            CKEDITOR.instances['editor11'].setReadOnly(false);
            $("#subject").val('');
            CKEDITOR.instances["editor11"].setData('');
        }else{
            jQuery.ajax({
                url: "ajax.php",
                data:'page=getcandidateemailtemplate&temp_id='+temp_id,
                type: "POST",
                success:function(data){
                    var datas = JSON.parse(data);
                    $('#subject').attr('readonly', true);
                    CKEDITOR.instances['editor11'].setReadOnly(true);
                    $("#subject").val(datas.email_subject_to);
                   CKEDITOR.instances["editor11"].setData(datas.email_content_to);
                },
                error:function (){}
            });
        }
    }
    function sendCandidateMail(){
        CKEDITOR.instances['editor11'].updateElement();
        var uri = $("#emailformdatasub").serialize();
        $.ajax({
            url: "ajax.php",
            data:"page=sendcandidatemailpopup&"+uri,
            type: "POST",
            success:function(data){
                console.log(data);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][9022];?>','');
                $("#kt_modal_dynamic_popup2").modal("hide");
            }
        });
    }
    function showEmailConten(id,email_page){
        console.log();
        $.ajax({
            url: "uploads/email/"+email_page,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#showpopupdetailsemail_"+id).html(data);
            }
        });
    $(".custom-popup-open").hide();
    $(".custom-popup-slide-showemailcontent-"+id).show('slow');
}
function closedPopupcompliance22(){
      $(".custom-popup-open").hide();
      console.log("closed");
}
</script>
<div class="modal-header">
    <h4 class="modal-title">Email</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body col-lg-10 mx-auto">
        <ul class="nav md-tabs infisuite_tab mb-2">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#candidateemailtab" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Email</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#candidatethistoriestab" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Histories</a>
            </li>
        </ul>
        <div class="tab-content card-block p-0 mt-4">
            <div class="tab-pane active" id="candidateemailtab" role="tabpanel">
                <form id="emailformdatasub">
                    <input type="hidden" name="candidate_id" value="<?php echo $_POST['cand_id']; ?>">
                    <input type="hidden" name="email_content_ref_id" value="<?php echo $_POST['temp_id']; ?>">
                <div class="row ">
                    <div class="col-lg-4">
                        <div class="form-group w-full">
                            <span class="form-label">Email Template  <span class="requried">*</span></span>
                            <select aria-label="Email Template" class="form-control js-example-basic-signle" name="email_template_id" onchange="emailTemplateId(this.value)">
                                <option value="-1">New Email</option>
                                <?php foreach($email_content as $res){  ?>
                                <option value="<?php echo $res['template_reference_id']; ?>"><?php echo $res['template_name']; ?></option>
                                <?php }  ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <span class="form-label">Subject<span class="requried">*</span></span>
                            <input aria-label="Subject" class="form-control notemptyclass text-capitalize"  type="text" name="subject" id="subject" value="<?php echo $emaildetail['email_subject_to']; ?>">
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="form-group">
                            <span class="form-label">Email Content<span class="requried">*</span></span>
                            <textarea aria-label="Email Content To" class="form-control " name="email_content_to" id="editor11" ><?php echo $emaildetail['email_content_to']; ?></textarea>
                        </div>
                    </div>
                </div>
                </form>

            </div>
            <div class="tab-pane" id="candidatethistoriestab" role="tabpanel">


                <?php  if(count((array)$email_logs) !=0){ ?>
                <table class="table table-hover m-b-0">
                    <tbody>
                        <?php foreach($email_logs as $email_log){ ?>
                        <tr id="custom-popup">
                            <th scope="col">
                                <div class="d-inline-block align-middle">
                                    <div class="d-inline-block">
                                        <h6>From : <?php echo $email_log['email_source']; ?></h6>
                                        <p class="text-muted m-b-0"><?php echo $email_log['email_date']; ?></p>
                                    </div>
                                </div>
                            </th>
                            <th scope="col" style="text-align: right;">
                                <i class="fa fa-eye" onclick="showEmailConten('<?php echo $email_log['id']; ?>','<?php echo $email_log['content']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>


                                    <div class="custom-popup-open custom-popup-slide-showemailcontent-<?php echo $email_log['id']; ?> text-start" style="display: none;left: 18px;top: 55px; overflow: scroll;">
                                      <div class="card">
                                        <div class="card-header">
                                          <h5>Email Content</h5>
                                          <div class="card-header-right">
                                            <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closedPopupcompliance22()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                                          </div>
                                        </div>
                                        <div class="card-body" id="showpopupdetailsemail_<?php echo $email_log['id']; ?>">
                                            <?php echo $emailcontentlist[$_POST['temp_id']]['email_content_to']; ?>
                                        </div>
                                    </div>
                                </div>
                            </th>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>

            <?php }else{  ?>
                <div class="card-px text-center py-20 m-t-30">
                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Emails </p>
                    <div class="text-center px-4">
                      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>
                  </div>
            <?php } ?>

            </div>
        </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="sendCandidateMail()">Submit</button>
</div>

<script type="text/javascript"  src="assets/pages/wysiwyg-editor/js/tinymce.min.js"></script>
<script  type="text/javascript"  src="assets/pages/wysiwyg-editor/wysiwyg-editor.js"></script>
<script type="text/javascript" src="assets/pages/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="assets/pages/ckeditor/ckeditor-custom.js"></script>
