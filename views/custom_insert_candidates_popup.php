<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$job_posting_id = $_POST['job_posting_id'];
?>
<script type="text/javascript">
function submitdetails(){
    var validations = ajaxFormSubmitValidation();
    if(validations == 0){
        var formData = new FormData();
        formData.append("page", "insertcandidatespopup");
        var dataArray = $("#candidatesform").serializeArray();
        $(dataArray).each(function(i, field){
           formData.append(field.name, field.value);
       });
        if($('input[name=file]')[0]){
            formData.append('file', $('input[name=file]')[0].files[0]);
        }
        $.ajax({
            url: "ajax.php",
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            success:function(data){
                var result = JSON.parse(data);
                if(result.status =="fail"){
                    tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',result.message,'');
                }else{
                    if(result.LASTID == '0'){
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3001]; ?>','');
                    }else{
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3000]; ?>','');
                    }
                    $('#kt_modal_dynamic_popup2').modal("hide");
                    location.reload();
                }
            },
            error:function (){}
        });

    }


}

</script>
<?php
$wh3["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);
$wh3['field'] = "id,location_name";
$data3 = urlencode(json_encode($wh3));
$url3 = API."?mode=gettabledetailswithconditions&table=locations&data=".$data3;
$data_details3 = callAPI($url3);
$result3 = json_decode($data_details3,1);
foreach($result3 as $location){
  $locationlist[$location['id']] = $location['location_name'];
}
?>
<script type="text/javascript">

  function checkEmailidAllReadyAvilableOrNot(){
    $(".allemailerrer").hide();
    $("#submitcandidatepopup").removeClass("disabled");
    var poststop = 0;
    var posterrerdata = [];
    $(".allemails").each(function(index) {
      if($(this).val() !=""){
        var arrarowid = $(this).attr("data-errorcls");
        var emailval = $(this).val();
        if($.inArray(emailval, posterrerdata)>=0){
          $("#"+arrarowid).show();
          $("#submitcandidatepopup").addClass("disabled");
          poststop = 1;
        }else{
           posterrerdata.push($(this).val());
        }
      }
    });
    if(poststop == '0'){
      $.ajax({
            url: "ajax.php",
            data:'page=checkemailidallreadyavilableornot&email_ids='+posterrerdata,
            type: "POST",
            success:function(data){
              var res = JSON.parse(data);
              var allreadyavailable = res.application_users;
              console.log(allreadyavailable);
              $(".allemails").each(function(index){
                  if($(this).val() !=""){
                    var arrarowid = $(this).attr("data-errorcls");
                    var emailval = $(this).val();
                    if($.inArray(emailval, allreadyavailable)>=0){
                      $("#"+arrarowid).show();
                      console.log("match - "+emailval);
                      $("#submitcandidatepopup").addClass("disabled");
                    }else{
                      console.log("not match - "+emailval);
                    }
                  }
              });
            },
            error:function (){}
        });
    }
  }
  function uploadCandidateResume(){
        var ai_file = $("#ai_file").val();
        if(ai_file !=""){
            $("#uploadcandidateresumebtn").text("Please Wait........");
            $("#uploadcandidateresumebtn").addClass("disabled");
            var formData = new FormData();
            var dataArray = $("#candidatesform_resume").serializeArray();
            $(dataArray).each(function(i, field){
              formData.append(field.name, field.value);
            });
            var ai_file = document.getElementById('ai_file').files[0];
            formData.append("files",ai_file);
            $.ajax({
                url: "apis/push_upload_resume_with_ai.php",
                data: formData,
                type: "POST",
                contentType: false,
                processData: false,
                success:function(data){
                    console.log(data);
                    result = JSON.parse(data);
                    $("#messsage_result").html(result.msg);
                    if(result.status == "success"){
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3000]; ?>','');
                        window.location.href = "index.php?option=COM_INSERT_CANDIDATE&Rid="+result.encodeid;
                    }else{
                        $("#uploadcandidateresumebtn").text("Submit");
                        $("#uploadcandidateresumebtn").removeClass("disabled");
                    }
                },
                error:function (){}
            });
        }else{
            $("#messsage_result").text('Please upload at least one file.');
        }
    }
    function popupbtnhideshow(type){
        if(type=='hide'){
            $("#submitcandidatepopup").hide();
        }else{
            $("#submitcandidatepopup").show();
        }
    }
</script>
<div class="modal-header">
    <h4 class="modal-title">Add Candidate</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body ">
    <ul class="nav md-tabs infisuite_tab">
        <li class="nav-item">
            <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#manual-insert-candidate" role="tab" aria-selected="true" onclick="popupbtnhideshow('show')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Manual Insert Candidate</a>
        </li>
        <li class="nav-item">
            <a class="nav-link f-w-600 f-12 " data-bs-toggle="tab" href="#AI-insert-candidate" role="tab" aria-selected="true" onclick="popupbtnhideshow('hide')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">AI Insert Candidate</a>
        </li>
    </ul>
    <div class="tab-content tabs">
        <div class="tab-pane  card-body active" id="manual-insert-candidate" role="tabpanel">
            <form id="candidatesform" method="post" >
                <div class="row ">
                    <div class="form-group col-lg-3">
                        <span class="form-label">First Name <span class="requried">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <input aria-label="First Name" class="form-control requried_field text-capitalize "  type="text" name="first_name" id="first_name" >
                    </div>
                    <div class="form-group col-lg-3">
                        <span class="form-label">Last Name <span class="requried">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <input aria-label="Last Name" class="form-control requried_field text-capitalize"  type="text"  name="last_name">
                    </div>
                    <div class="form-group col-lg-3">
                        <span class="form-label">Email Id <span class="requried">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <input aria-label="Email" class="form-control requried_field allemails"  data-errorcls="repeateemailerror"  onblur="checkEmailidAllReadyAvilableOrNot()"  type="email"  name="email_id" >
                         <span class="text-danger f-12 allemailerrer" id="repeateemailerror"  style="display:none">Email Id Already Register</span>
                    </div>
                    <div class="form-group col-lg-3">
                        <span class="form-label">Phone <span class="requried">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <input aria-label="Phone" class="form-control requried_field"  type="number"  name="phone" >
                    </div>
                    <div class="form-group col-lg-3">
                        <span class="form-label">Current Location<span class="requried">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <input aria-label="Current Location" class="form-control requried_field"  type="text"  name="current_location" value="<?php echo $result['current_location']; ?>" >
                    </div>
                    <div class="form-group col-lg-3">
                        <span class="form-label">Experience (years)<span class="requried">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <div class="input-group input-group-button ">
                            <input aria-label="Candidate Experiances" class="form-control requried_field width1"  type="number"  name="candidate_experiances" value="<?php echo $result['candidate_experiances']; ?>" >
                            <span class="input-group-text addon-style">
                                <span class="">Years</span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-lg-3 w-full">
                        <span class="form-label">Preferred Location<span class="requried">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <select aria-label="Location" class="form-control js-example-basic-single requried_field" name="location_id" data-type="select" >
                            <?php foreach($result3 as $location){ ?>
                              <option value="<?php echo $location['id']; ?>" <?php echo $candidates['location_id']==$location['id']?"selected":""; ?>><?php echo $location['location_name']; ?></option>
                          <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-3">
                        <span class="form-label">Current CTC<span class="requried">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <div class="input-group input-group-button ">
                            <input aria-label="Current Ctc" class="form-control width1 requried_field"  type="number"  name="current_ctc" value="<?php echo $result['current_ctc']; ?>" >
                            <span class="input-group-text addon-style">
                                <span class="">Yearly</span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-lg-3">
                        <span class="form-label">Resume</span>
                        <input aria-label="File" type="file" name="file"/>
                        <input type="hidden" name="old_file" value="<?php echo $data['files'];?>" />
                    </div>
                </div>
                <input type="hidden" name="job_posting_id" value="<?php echo $job_posting_id; ?>"/>
            </form>
        </div>
        <div class="tab-pane  card-body" id="AI-insert-candidate" role="tabpanel">
            <form id="candidatesform_resume" method="post" >
                <input class="form-control" type="hidden" name="company_id" value="<?php echo $_SESSION['company_id']; ?>" >
                <input class="form-control" type="hidden" name="data_name" value="<?php echo $_SESSION['DB_NAME']; ?>" >
                <input class="form-control" type="hidden" name="user_id" value="<?php echo $_SESSION['userid']; ?>" >
                <div class="row">
                    <div class="form-group col-lg-1"></div>

                    <div class="form-group col-lg-3">
                        <span class="form-label mb-2 f-w-600">Resume</span>
                        <input aria-label="Ai File" type="file" name="ai_file" id="ai_file"/>
                    </div>
                    <div class="form-group col-lg-2">
                        <button type="button" class="btn btn-primary waves-effect waves-light m-t-20" id="uploadcandidateresumebtn" onclick="uploadCandidateResume()">Submit</button>
                    </div>
                    <div class="form-group col-lg-5 text-danger m-t-20 text-left" id="messsage_result">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light disabled" id="submitcandidatepopup"  onclick="submitdetails()">Submit</button>
</div>
